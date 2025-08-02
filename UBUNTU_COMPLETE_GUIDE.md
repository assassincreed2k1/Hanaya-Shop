# 🚀 HƯỚNG DẪN TRIỂN KHAI HANAYA SHOP TRÊN UBUNTU - HOÀN CHỈNH

## 📋 TỔNG QUAN

Hướng dẫn này sẽ giúp bạn triển khai Hanaya Shop trên Ubuntu server với:
- ✅ **100% Nginx + PHP-FPM** (không Apache)
- ✅ **HTTPS mặc định** với SSL certificates
- ✅ **Redis với Predis client** (đã fix lỗi kết nối)
- ✅ **Docker containerized** (dễ quản lý)
- ✅ **Auto-deployment script** (triển khai 1 lệnh)

## 🎯 TRIỂN KHAI NHANH (1 LỆNH)

```bash
# Tải và chạy script triển khai tự động
curl -fsSL https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/deploy-ubuntu-complete.sh -o deploy-hanaya.sh && chmod +x deploy-hanaya.sh && ./deploy-hanaya.sh
```

## 📝 TRIỂN KHAI THỦ CÔNG (TỪNG BƯỚC)

### Bước 1: Dọn dẹp và chuẩn bị

```bash
# Dọn dẹp file cũ
cd ~
rm -f docker-compose.yml get-docker.sh
docker system prune -f

# Tạo thư mục dự án
mkdir -p ~/Hanaya-Shop
cd ~/Hanaya-Shop

# Dừng container cũ (nếu có)
docker-compose -f docker-compose.production.yml down 2>/dev/null || echo "No existing containers"
```

### Bước 2: Tạo file cấu hình Docker

```bash
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
```

### Bước 3: Tạo SSL certificates

```bash
# Tạo thư mục SSL
mkdir -p deployment/ssl

# Lấy IP server
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')

# Tạo chứng chỉ SSL tự ký
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout deployment/ssl/hanaya-shop.key \
  -out deployment/ssl/hanaya-shop.crt \
  -subj "/CN=hanaya-shop.local/O=Hanaya Shop/C=VN" \
  -addext "subjectAltName=DNS:hanaya-shop.local,DNS:localhost,IP:127.0.0.1,IP:$SERVER_IP"

# Thiết lập quyền
chmod 600 deployment/ssl/hanaya-shop.key
chmod 644 deployment/ssl/hanaya-shop.crt
```

### Bước 4: Pull image và khởi động

```bash
# Pull Docker image từ Docker Hub
docker pull assassincreed2k1/hanaya-shop:latest

# Khởi động các container
docker-compose -f docker-compose.production.yml up -d

# Chờ khởi động và kiểm tra
sleep 15
docker-compose -f docker-compose.production.yml ps
```

## 🌐 TRUY CẬP WEBSITE

Sau khi triển khai thành công, bạn có thể truy cập:

- **HTTP**: `http://YOUR_SERVER_IP` → tự động chuyển sang HTTPS
- **HTTPS**: `https://YOUR_SERVER_IP`

*Thay `YOUR_SERVER_IP` bằng IP thật của server*

## 🔧 LỆNH QUẢN LÝ

### Kiểm tra trạng thái
```bash
cd ~/Hanaya-Shop
docker-compose -f docker-compose.production.yml ps
docker-compose -f docker-compose.production.yml logs -f
```

### Cập nhật website
```bash
cd ~/Hanaya-Shop
docker-compose -f docker-compose.production.yml down
docker pull assassincreed2k1/hanaya-shop:latest
docker image prune -f
docker-compose -f docker-compose.production.yml up -d
```

### Sao lưu dữ liệu
```bash
# Sao lưu database
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > ~/backup-$(date +%Y%m%d).sql
```

### Khởi động lại từng service
```bash
docker-compose -f docker-compose.production.yml restart app
docker-compose -f docker-compose.production.yml restart db
docker-compose -f docker-compose.production.yml restart redis
```

## 🔒 CẤU HÌNH SSL CHÍNH THỨC (PRODUCTION)

Nếu bạn có domain thật, thay chứng chỉ tự ký bằng Let's Encrypt:

```bash
# Cài đặt Certbot
apt update && apt install -y certbot

# Dừng container
docker-compose -f docker-compose.production.yml down

# Lấy chứng chỉ SSL
certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com

# Sao chép chứng chỉ
cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem deployment/ssl/hanaya-shop.crt
cp /etc/letsencrypt/live/yourdomain.com/privkey.pem deployment/ssl/hanaya-shop.key

# Khởi động lại
docker-compose -f docker-compose.production.yml up -d
```

## 🛠️ XỬ LÝ LỖI THƯỜNG GẶP

### 1. Lỗi kết nối Redis
```bash
# Kiểm tra logs
docker-compose -f docker-compose.production.yml logs redis
docker-compose -f docker-compose.production.yml logs app

# Khởi động lại
docker-compose -f docker-compose.production.yml restart app
```

### 2. Lỗi SSL Certificate
```bash
# Kiểm tra chứng chỉ
ls -la deployment/ssl/
chmod 600 deployment/ssl/hanaya-shop.key
chmod 644 deployment/ssl/hanaya-shop.crt
```

### 3. Lỗi Database
```bash
# Kiểm tra logs database
docker-compose -f docker-compose.production.yml logs db

# Truy cập database
docker exec -it hanaya-shop-db mysql -u hanaya_user -pTrungnghia2703 hanaya_shop
```

### 4. Website không truy cập được
```bash
# Kiểm tra port
netstat -tlnp | grep :80
netstat -tlnp | grep :443

# Kiểm tra firewall (nếu có)
ufw status
ufw allow 80
ufw allow 443
```

## 📊 GIÁM SÁT & BẢO TRÌ

### Script sao lưu tự động
```bash
# Tạo script backup hàng ngày
cat > ~/backup-daily.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > ~/backup_$DATE.sql
find ~ -name "backup_*.sql" -mtime +7 -delete
EOF

chmod +x ~/backup-daily.sh

# Thêm vào cron (chạy 2:00 AM hàng ngày)
(crontab -l 2>/dev/null; echo "0 2 * * * ~/backup-daily.sh") | crontab -
```

### Monitoring logs
```bash
# Theo dõi logs realtime
docker-compose -f docker-compose.production.yml logs -f

# Kiểm tra sử dụng tài nguyên
docker stats

# Kiểm tra dung lượng disk
df -h
docker system df
```

## ✨ TÍNH NĂNG ĐÃ TÍCH HỢP

1. **🔐 HTTPS mặc định**: Tất cả HTTP requests tự động redirect sang HTTPS
2. **⚡ Performance**: Nginx + PHP-FPM với cache tối ưu
3. **🛡️ Security**: CSP headers, SSL/TLS, security headers
4. **📊 Monitoring**: Health check endpoints, structured logging
5. **🔄 Auto-restart**: Container tự khởi động khi server reboot
6. **💾 Data persistence**: Database và storage được mount persistent

## 🎉 HOÀN TẤT

Website Hanaya Shop đã sẵn sàng phục vụ với:
- ✅ HTTPS security
- ✅ High performance
- ✅ Easy maintenance
- ✅ Scalable architecture

**Support**: Nếu gặp vấn đề, kiểm tra logs với lệnh `docker-compose logs -f`
