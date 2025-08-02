# QUY TRÌNH TRIỂN KHAI HANAYA SHOP - DOCKER IMAGE

## 🔄 QUY TRÌNH HOÀN CHỈNH

### 📋 Tổng quan
1. **Windows (Development)**: Build Docker image → Push lên Docker Hub
2. **Ubuntu (Production)**: Pull Docker image → Chạy container

### 🏗️ BƯỚC 1: BUILD VÀ PUSH IMAGE (TRÊN WINDOWS)

```bash
# Đảm bảo đã đăng nhập Docker Hub
docker login

# Build và push image với script tự động
build-and-push.bat

# Hoặc thủ công:
docker build -t assassincreed2k1/hanaya-shop:latest .
docker push assassincreed2k1/hanaya-shop:latest
```

### 🚀 BƯỚC 2: TRIỂN KHAI LẦN ĐẦU (TRÊN UBUNTU)

#### Cách 1: Script tự động (khuyên dùng)
```bash
# Tạo và triển khai trong một lệnh
cd ~ && \
rm -f docker-compose.yml get-docker.sh && \
docker system prune -f && \
mkdir -p ~/Hanaya-Shop && \
cd ~/Hanaya-Shop && \
docker-compose -f docker-compose.production.yml down 2>/dev/null || true && \
wget -O docker-compose.production.yml https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/docker-compose.production.yml && \
docker pull assassincreed2k1/hanaya-shop:latest && \
mkdir -p deployment/ssl && \
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout deployment/ssl/hanaya-shop.key \
  -out deployment/ssl/hanaya-shop.crt \
  -subj "/CN=localhost/O=Hanaya Shop/C=VN" \
  -addext "subjectAltName=DNS:localhost,IP:127.0.0.1" && \
docker-compose -f docker-compose.production.yml up -d && \
echo "🎉 TRIỂN KHAI HOÀN TẤT!"
```

#### Cách 2: Thủ công từng bước
```bash
# 1. Dọn dẹp
cd ~
rm -f docker-compose.yml get-docker.sh
docker system prune -f

# 2. Tạo thư mục dự án
mkdir -p ~/Hanaya-Shop
cd ~/Hanaya-Shop

# 3. Tạo file docker-compose.production.yml
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
  redis_data:
  storage_data:
  logs_data:

networks:
  hanaya-network:
    driver: bridge
EOF

# 4. Pull image và khởi động
docker pull assassincreed2k1/hanaya-shop:latest
mkdir -p deployment/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout deployment/ssl/hanaya-shop.key \
  -out deployment/ssl/hanaya-shop.crt \
  -subj "/CN=localhost/O=Hanaya Shop/C=VN" \
  -addext "subjectAltName=DNS:localhost,IP:127.0.0.1"

docker-compose -f docker-compose.production.yml up -d
```

### 🔄 BƯỚC 3: CẬP NHẬT IMAGE (KHI CÓ THAY ĐỔI)

#### Trên Windows (sau khi có thay đổi code):
```bash
# Build và push image mới
build-and-push.bat
```

#### Trên Ubuntu (cập nhật website):
```bash
cd ~/Hanaya-Shop

# Dừng container
docker-compose -f docker-compose.production.yml down

# Sao lưu dữ liệu (tùy chọn)
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > ~/backup-$(date +%Y%m%d).sql 2>/dev/null || true

# Kéo image mới và khởi động
docker pull assassincreed2k1/hanaya-shop:latest
docker image prune -f
docker-compose -f docker-compose.production.yml up -d

# Kiểm tra trạng thái
docker-compose -f docker-compose.production.yml ps
docker-compose -f docker-compose.production.yml logs -f
```

## 🌐 TRUY CẬP WEBSITE

- **HTTP**: http://your-server-ip (tự động redirect HTTPS)
- **HTTPS**: https://your-server-ip

## 🔧 LỆNH HỮU ÍCH

### Kiểm tra logs
```bash
cd ~/Hanaya-Shop
docker-compose -f docker-compose.production.yml logs -f
docker-compose -f docker-compose.production.yml logs app
docker-compose -f docker-compose.production.yml logs db
docker-compose -f docker-compose.production.yml logs redis
```

### Kiểm tra trạng thái
```bash
docker-compose -f docker-compose.production.yml ps
docker stats
```

### Sao lưu database
```bash
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > backup-$(date +%Y%m%d).sql
```

### Khởi động lại một container cụ thể
```bash
docker-compose -f docker-compose.production.yml restart app
docker-compose -f docker-compose.production.yml restart db
docker-compose -f docker-compose.production.yml restart redis
```

## 🔒 SSL CHÍNH THỨC (TÙY CHỌN)

Nếu có domain thật, thay chứng chỉ tự ký bằng Let's Encrypt:

```bash
# Cài Certbot
apt update && apt install -y certbot

# Dừng container
docker-compose -f docker-compose.production.yml down

# Lấy chứng chỉ
certbot certonly --standalone -d your-domain.com

# Sao chép chứng chỉ
cp /etc/letsencrypt/live/your-domain.com/fullchain.pem deployment/ssl/hanaya-shop.crt
cp /etc/letsencrypt/live/your-domain.com/privkey.pem deployment/ssl/hanaya-shop.key

# Khởi động lại
docker-compose -f docker-compose.production.yml up -d
```

## ✨ ƯU ĐIỂM CỦA QUY TRÌNH NÀY

1. **Không cần Git trên server**: Chỉ cần Docker
2. **Image đã build sẵn**: Deploy nhanh, không cần build trên server
3. **Dễ dàng cập nhật**: Push image mới từ Windows, pull trên Ubuntu
4. **Độc lập hoàn toàn**: Ubuntu chỉ cần Docker và Internet
5. **Rollback dễ dàng**: Có thể pull về version cũ nếu cần

## 🎯 HOÀN TẤT

Quy trình này cho phép bạn:
- Phát triển và build trên Windows
- Deploy và chạy trên Ubuntu
- Cập nhật nhanh chóng khi cần
- Không phụ thuộc vào Git trên production server
