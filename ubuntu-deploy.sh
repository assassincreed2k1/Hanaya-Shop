#!/bin/bash

# ========================================
# HANAYA SHOP - ONE-COMMAND DEPLOYMENT FOR UBUNTU
# ========================================
# This script will automatically deploy Hanaya Shop on Ubuntu
# Usage: curl -fsSL https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-deploy.sh | bash

set -e  # Exit on any error

echo "🚀 HANAYA SHOP - UBUNTU DEPLOYMENT"
echo "=================================="
echo "Starting automatic deployment..."
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first:"
    echo "   curl -fsSL https://get.docker.com -o get-docker.sh && sudo sh get-docker.sh"
    exit 1
fi

# Check if Docker Compose is available
if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose is not available. Please install Docker Compose."
    exit 1
fi

# Get server IP
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}' || echo "localhost")

echo "📡 Detected server IP: $SERVER_IP"
echo ""

# Step 1: Cleanup
echo "🧹 Step 1: Cleaning up old files..."
cd ~
rm -f docker-compose.yml get-docker.sh
docker system prune -f >/dev/null 2>&1 || true

# Step 2: Create project directory  
echo "📁 Step 2: Creating project directory..."
mkdir -p ~/Hanaya-Shop
cd ~/Hanaya-Shop

# Stop old containers
docker-compose -f docker-compose.production.yml down >/dev/null 2>&1 || true

# Step 3: Create docker-compose.production.yml
echo "📝 Step 3: Creating Docker configuration..."
cat > docker-compose.production.yml << 'EOF'
version: '3.8'

services:
  app:
    image: assassincreed2k1/hanaya-shop:latest
    container_name: hanaya-shop-app
    ports:
      - "80:80"
      - "443:443"
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_KEY=base64:sQbpp5LuUmxSQBAaSIyt+ph7NzDu4Y8x6PYh0CPnZm8=
      - APP_URL=https://hanaya-shop.com
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
      - ./deployment/ssl:/etc/nginx/ssl
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

# Step 4: Create SSL certificates
echo "🔐 Step 4: Creating SSL certificates..."
mkdir -p deployment/ssl

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout deployment/ssl/hanaya-shop.key \
  -out deployment/ssl/hanaya-shop.crt \
  -subj "/CN=hanaya-shop.local/O=Hanaya Shop/C=VN" \
  -addext "subjectAltName=DNS:hanaya-shop.local,DNS:localhost,IP:127.0.0.1,IP:$SERVER_IP" \
  >/dev/null 2>&1

chmod 600 deployment/ssl/hanaya-shop.key
chmod 644 deployment/ssl/hanaya-shop.crt
echo "📝 Step 4.1: Creating Vite manifest and assets..."
mkdir -p public/build/assets
cat > public/build/manifest.json << 'MANIFEST'
{
  "resources/css/app.css": {
    "file": "assets/app-custom.css",
    "isEntry": true,
    "src": "resources/css/app.css"
  },
  "resources/js/app.js": {
    "file": "assets/app-custom.js",
    "isEntry": true,
    "src": "resources/js/app.js"
  }
}
MANIFEST
touch public/build/assets/app-custom.css
touch public/build/assets/app-custom.js

# Step 5: Pull and start containers
echo "🐳 Step 5: Pulling Docker image..."
docker pull assassincreed2k1/hanaya-shop:latest

echo "🚀 Step 6: Starting containers..."
docker-compose -f docker-compose.production.yml up -d

# Step 7: Wait and check
echo "⏳ Step 7: Waiting for services to start..."
sleep 20

# Check if containers are running
if docker-compose -f docker-compose.production.yml ps | grep -q "Up"; then
    echo "✅ Services are running!"
else
    echo "❌ Some services failed to start. Checking logs..."
    docker-compose -f docker-compose.production.yml logs
    exit 1
fi

# Step 8: Create management scripts
echo "📝 Step 8: Creating management scripts..."

# Update script
cat > update-hanaya.sh << 'EOF'
#!/bin/bash
echo "🔄 Updating Hanaya Shop..."
cd ~/Hanaya-Shop
docker-compose -f docker-compose.production.yml down
docker pull assassincreed2k1/hanaya-shop:latest
docker image prune -f
docker-compose -f docker-compose.production.yml up -d
echo "✅ Update completed!"
docker-compose -f docker-compose.production.yml ps
EOF

# Backup script
cat > backup-hanaya.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
echo "💾 Creating backup..."
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > ~/hanaya-backup-$DATE.sql
echo "✅ Backup created: ~/hanaya-backup-$DATE.sql"
# Keep only last 7 backups
ls -t ~/hanaya-backup-*.sql | tail -n +8 | xargs -r rm
EOF

# Status script
cat > status-hanaya.sh << 'EOF'
#!/bin/bash
echo "📊 Hanaya Shop Status"
echo "===================="
cd ~/Hanaya-Shop
docker-compose -f docker-compose.production.yml ps
echo ""
echo "💾 Disk Usage:"
docker system df
echo ""
echo "🔗 Access URLs:"
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')
echo "  HTTP:  http://$SERVER_IP"
echo "  HTTPS: https://$SERVER_IP"
EOF

chmod +x update-hanaya.sh backup-hanaya.sh status-hanaya.sh

# Final status check
echo ""
echo "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!"
echo "====================================="
echo ""
echo "✅ Hanaya Shop is now running at:"
echo "   📱 HTTP:  http://$SERVER_IP"
echo "   🔒 HTTPS: https://$SERVER_IP"
echo ""
echo "📋 Available management commands:"
echo "   📊 Status:  ./status-hanaya.sh"
echo "   🔄 Update:  ./update-hanaya.sh"
echo "   💾 Backup:  ./backup-hanaya.sh"
echo ""
echo "📝 Check logs: docker-compose -f docker-compose.production.yml logs -f"
echo ""
echo "⚠️  Note: SSL warnings are normal with self-signed certificates"
echo "   For production, replace with proper SSL certificates"
echo ""
echo "🎯 Project directory: ~/Hanaya-Shop"
echo "====================================="
