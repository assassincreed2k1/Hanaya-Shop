#!/bin/bash

# ========================================
# SCRIPT TRIỂN KHAI HOÀN CHỈNH HANAYA SHOP TRÊN UBUNTU
# ========================================

echo "🚀 STARTING HANAYA SHOP DEPLOYMENT ON UBUNTU"
echo "=============================================="

# Bước 1: Dọn dẹp file cũ
echo "📁 Step 1: Cleaning up old files..."
cd ~
rm -f docker-compose.yml get-docker.sh
docker system prune -f

# Bước 2: Tạo thư mục dự án
echo "📁 Step 2: Creating project directory..."
mkdir -p ~/Hanaya-Shop
cd ~/Hanaya-Shop

# Dừng container cũ nếu có
docker-compose -f docker-compose.production.yml down 2>/dev/null || echo "No existing containers"

# Bước 3: Tạo file docker-compose.production.yml
echo "📝 Step 3: Creating docker-compose.production.yml..."
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

# Bước 4: Tạo thư mục SSL và chứng chỉ tự ký
echo "🔒 Step 4: Creating SSL certificates..."
mkdir -p deployment/ssl

# Lấy IP server hiện tại
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout deployment/ssl/hanaya-shop.key \
  -out deployment/ssl/hanaya-shop.crt \
  -subj "/CN=hanaya-shop.local/O=Hanaya Shop/C=VN" \
  -addext "subjectAltName=DNS:hanaya-shop.local,DNS:localhost,IP:127.0.0.1,IP:$SERVER_IP"

# Thiết lập quyền truy cập
chmod 600 deployment/ssl/hanaya-shop.key
chmod 644 deployment/ssl/hanaya-shop.crt

# Bước 5: Pull Docker image và khởi động
echo "🐳 Step 5: Pulling Docker image and starting containers..."
docker pull assassincreed2k1/hanaya-shop:latest
docker-compose -f docker-compose.production.yml up -d

# Bước 6: Chờ và kiểm tra trạng thái
echo "⏳ Step 6: Waiting for services to start..."
sleep 15

echo "📊 Checking container status..."
docker-compose -f docker-compose.production.yml ps

# Bước 7: Tạo script cập nhật nhanh
echo "📝 Step 7: Creating update script..."
cat > update-hanaya.sh << 'EOF'
#!/bin/bash
echo "🔄 Updating Hanaya Shop..."
cd ~/Hanaya-Shop
docker-compose -f docker-compose.production.yml down
docker pull assassincreed2k1/hanaya-shop:latest
docker image prune -f
docker-compose -f docker-compose.production.yml up -d
echo "✅ Update completed!"
EOF

chmod +x update-hanaya.sh

# Hoàn thành
echo ""
echo "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!"
echo "=============================================="
echo "✅ Website is now running at:"
echo "   📱 HTTP:  http://$SERVER_IP"
echo "   🔒 HTTPS: https://$SERVER_IP"
echo ""
echo "📋 Useful commands:"
echo "   📊 Check status: docker-compose -f docker-compose.production.yml ps"
echo "   📝 View logs:    docker-compose -f docker-compose.production.yml logs -f"
echo "   🔄 Update:       ./update-hanaya.sh"
echo ""
echo "⚠️  Note: You may see SSL warnings as we're using self-signed certificates"
echo "   For production, replace with proper SSL certificates"
echo "=============================================="
