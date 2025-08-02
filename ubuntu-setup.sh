#!/bin/bash

# Hanaya Shop Ubuntu Deployment Script
# This script sets up and runs Hanaya Shop on Ubuntu using Docker

set -e

echo "🚀 Starting Hanaya Shop deployment on Ubuntu..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Installing Docker..."
    
    # Update package list
    sudo apt-get update
    
    # Install required packages
    sudo apt-get install -y \
        ca-certificates \
        curl \
        gnupg \
        lsb-release
    
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
    
    echo "✅ Docker installed successfully!"
    echo "⚠️  Please log out and log back in to use Docker without sudo"
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Installing Docker Compose..."
    
    # Download Docker Compose
    sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    
    # Make it executable
    sudo chmod +x /usr/local/bin/docker-compose
    
    echo "✅ Docker Compose installed successfully!"
fi

# Create project directory
PROJECT_DIR="$HOME/hanayashop"
echo "📁 Creating project directory: $PROJECT_DIR"
mkdir -p "$PROJECT_DIR"
cd "$PROJECT_DIR"

# Download docker-compose.yml
echo "📥 Downloading docker-compose.yml..."
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

echo "✅ docker-compose.yml created successfully!"

# Pull the latest image
echo "📥 Pulling latest Hanaya Shop image..."
docker-compose pull

# Stop and remove existing containers
echo "🛑 Stopping existing containers..."
docker-compose down --remove-orphans

# Start the application
echo "🚀 Starting Hanaya Shop..."
docker-compose up -d

# Wait for services to be healthy
echo "⏳ Waiting for services to start..."
sleep 30

# Check service status
echo "📊 Checking service status..."
docker-compose ps

# Show logs
echo "📝 Recent logs:"
docker-compose logs --tail=20

echo ""
echo "🎉 Hanaya Shop deployment completed!"
echo ""
echo "🌐 Access your application at: http://localhost"
echo "📊 View logs: docker-compose logs -f"
echo "🛑 Stop application: docker-compose down"
echo "🔄 Update application: docker-compose pull && docker-compose up -d"
echo ""
echo "📁 Project directory: $PROJECT_DIR"
echo ""
