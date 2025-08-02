# Hanaya Shop Ubuntu Deployment Guide

## Prerequisites
- Ubuntu 20.04 LTS or higher
- Internet connection
- Sudo privileges

## Quick Setup

### 1. Download and run the setup script:
```bash
curl -fsSL https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-setup.sh | bash
```

### 2. Or manual setup:

#### Download the setup script:
```bash
wget https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-setup.sh
chmod +x ubuntu-setup.sh
./ubuntu-setup.sh
```

## Manual Installation

### 1. Install Docker and Docker Compose:
```bash
# Update package list
sudo apt-get update

# Install required packages
sudo apt-get install -y ca-certificates curl gnupg lsb-release

# Add Docker's official GPG key
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

# Set up the repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Update package list again
sudo apt-get update

# Install Docker Engine
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Add current user to docker group
sudo usermod -aG docker $USER

# Install Docker Compose (if not included)
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### 2. Create project directory:
```bash
mkdir -p ~/hanayashop
cd ~/hanayashop
```

### 3. Create docker-compose.yml:
```bash
cat > docker-compose.yml << 'EOF'
version: '3.8'

services:
  app:
    image: assassincreed2k1/hanaya-shop:latest
    container_name: hanaya-shop-app
    ports:
      - "80:80"
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=db
      - DB_DATABASE=hanaya_shop
      - DB_USERNAME=hanaya_user
      - DB_PASSWORD=Trungnghia2703
      - REDIS_HOST=redis
      - REDIS_CLIENT=predis
      - CACHE_DRIVER=redis
      - SESSION_DRIVER=redis
      - QUEUE_CONNECTION=redis
    volumes:
      - storage_data:/var/www/html/storage/app
      - logs_data:/var/www/html/storage/logs
    depends_on:
      - db
      - redis
    networks:
      - hanaya-network
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s

  db:
    image: mysql:8.0
    container_name: hanaya-shop-db
    environment:
      - MYSQL_ROOT_PASSWORD=Trungnghia2703
      - MYSQL_DATABASE=hanaya_shop
      - MYSQL_USER=hanaya_user
      - MYSQL_PASSWORD=Trungnghia2703
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - hanaya-network
    restart: unless-stopped
    command: --default-authentication-plugin=mysql_native_password

  redis:
    image: redis:7-alpine
    container_name: hanaya-shop-redis
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    networks:
      - hanaya-network
    restart: unless-stopped
    command: redis-server --appendonly yes

volumes:
  db_data:
    driver: local
  redis_data:
    driver: local
  storage_data:
    driver: local
  logs_data:
    driver: local

networks:
  hanaya-network:
    driver: bridge
EOF
```

### 4. Deploy the application:
```bash
# Pull the latest image
docker-compose pull

# Start the application
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f
```

## Management Commands

### View application logs:
```bash
docker-compose logs -f
```

### Stop the application:
```bash
docker-compose down
```

### Update the application:
```bash
docker-compose pull
docker-compose up -d
```

### Restart the application:
```bash
docker-compose restart
```

### Access the application:
- Web: http://your-server-ip
- Local: http://localhost

## Troubleshooting

### Check container status:
```bash
docker-compose ps
```

### View specific service logs:
```bash
docker-compose logs app
docker-compose logs db
docker-compose logs redis
```

### Access application container:
```bash
docker-compose exec app bash
```

### Database backup:
```bash
docker-compose exec db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > backup.sql
```

### Database restore:
```bash
docker-compose exec -T db mysql -u hanaya_user -pTrungnghia2703 hanaya_shop < backup.sql
```

## Security Considerations

1. Change default passwords in the docker-compose.yml file
2. Use environment files for sensitive data
3. Enable firewall and restrict access to necessary ports only
4. Regularly update Docker images
5. Monitor logs for suspicious activities

## Support

For issues and support, please check the project repository or contact the development team.
