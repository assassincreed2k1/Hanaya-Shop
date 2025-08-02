# HƯỚNG DẪN TRIỂN KHAI HANAYA SHOP TRÊN UBUNTU SERVER

## 🔥 TÓM TẮT CÁC THAY ĐỔI CHÍNH

✅ **Đã sửa lỗi Redis**: Cài đặt Predis v3.1.0 và cấu hình REDIS_CLIENT=predis  
✅ **Đã chuyển sang Nginx**: Loại bỏ hoàn toàn Apache, sử dụng 100% Nginx + PHP-FPM  
✅ **Đã cấu hình HTTPS**: SSL certificates và redirect HTTP → HTTPS  
✅ **Đã dọn dẹp**: Xóa tất cả file development không cần thiết  
✅ **Docker Image đã build**: assassincreed2k1/hanaya-shop:latest sẵn sàng sử dụng  

## 📁 CẤU TRÚC THỨ MỤC UBUNTU HIỆN TẠI

```bash
root@vmi2735328:~# ls -la
total 104
drwx------  7 root root  4096 Jul 31 12:31 .
drwxr-xr-x 22 root root  4096 Jul 31 06:48 ..
-rw-------  1 root root 20906 Aug  1 17:10 .bash_history
-rw-r--r--  1 root root  3106 Apr 22  2024 .bashrc
drwx------  2 root root  4096 Jul 31 07:03 .cache
drwx------  3 root root  4096 Jul 31 07:41 .docker
-rw-r--r--  1 root root    68 Jul 31 12:04 .gitconfig
drwxr-xr-x  3 root root  4096 Jul 31 07:25 .local
-rw-r--r--  1 root root   161 Apr 22  2024 .profile
drwx------  2 root root  4096 Jul 31 06:48 .ssh
-rw-------  1 root root  4978 Jul 31 11:43 .viminfo
drwxr-xr-x 13 root root  4096 Jul 31 12:06 Hanaya-Shop         # ← Thư mục dự án
-rw-r--r--  1 root root  2548 Jul 31 07:41 docker-compose.yml  # ← Cần xóa
-rw-r--r--  1 root root 20554 Jul 31 11:37 get-docker.sh       # ← Cần xóa
```

## 🚀 TRIỂN KHAI TRÊN UBUNTU

### Bước 1: Dọn dẹp file không cần thiết

```bash
# Di chuyển tới thư mục home
cd ~

# Xóa file docker-compose.yml cũ ở thư mục home  
rm -f docker-compose.yml

# Xóa script get-docker.sh (đã cài Docker rồi)
rm -f get-docker.sh

# Dọn dẹp cache Docker để tiết kiệm dung lượng
docker system prune -f

echo "✅ Đã dọn dẹp file không cần thiết trong home directory"
```

### Bước 2: Tạo thư mục dự án và tải file cần thiết

```bash
# Tạo thư mục dự án (hoặc dọn dẹp nếu đã tồn tại)
mkdir -p ~/Hanaya-Shop
cd ~/Hanaya-Shop

# Dừng container cũ (nếu có)
docker-compose -f docker-compose.production.yml down 2>/dev/null || echo "Không có container nào đang chạy"

# Xóa images cũ để tiết kiệm dung lượng
docker image prune -f

# Sao lưu dữ liệu (tùy chọn)
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > ~/backup-$(date +%Y%m%d).sql 2>/dev/null || echo "Database chưa tồn tại, bỏ qua sao lưu"

echo "✅ Đã chuẩn bị thư mục dự án"
```

### Bước 3: Tải các file cấu hình cần thiết

```bash
# Tạo file docker-compose.production.yml
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

echo "✅ Đã tạo file docker-compose.production.yml"
```

### Bước 4: Tạo script triển khai

```bash
# Tạo script deploy-ubuntu.sh
cat > deploy-ubuntu.sh << 'EOF'
#!/bin/bash

echo "======================================================"
echo "     TRIỂN KHAI HANAYA SHOP TRÊN UBUNTU SERVER"
echo "======================================================"

# Dừng các container đang chạy
echo "Dừng các container đang chạy..."
docker-compose -f docker-compose.production.yml down 2>/dev/null || echo "Không có container nào đang chạy"

# Xóa images cũ
echo "Xóa images cũ..."
docker image prune -f

# Cập nhật từ Docker Hub
echo "Kéo image mới nhất từ Docker Hub..."
docker pull assassincreed2k1/hanaya-shop:latest

# Tạo thư mục SSL nếu chưa tồn tại
echo "Kiểm tra thư mục SSL..."
mkdir -p deployment/ssl

# Kiểm tra chứng chỉ SSL
if [ ! -f "deployment/ssl/hanaya-shop.crt" ]; then
  echo "Tạo chứng chỉ tự ký nếu chưa tồn tại..."
  openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout deployment/ssl/hanaya-shop.key \
    -out deployment/ssl/hanaya-shop.crt \
    -subj "/CN=localhost/O=Hanaya Shop/C=VN" \
    -addext "subjectAltName=DNS:localhost,DNS:hanaya.local,IP:127.0.0.1"
fi

# Khởi động các container
echo "Khởi động các container..."
docker-compose -f docker-compose.production.yml up -d

# Kiểm tra trạng thái
echo "Kiểm tra trạng thái các container..."
docker-compose -f docker-compose.production.yml ps

echo "======================================================"
echo "     TRIỂN KHAI HOÀN TẤT"
echo "======================================================"
echo "Website có thể truy cập tại:"
echo "- HTTP: http://your-server-ip (sẽ chuyển hướng sang HTTPS)"
echo "- HTTPS: https://your-server-ip"
echo ""
echo "Lưu ý:"
echo "- Thay 'your-server-ip' bằng IP thật của server"
echo "- Sử dụng chứng chỉ SSL chính thức cho production"
echo "- Kiểm tra logs: docker-compose -f docker-compose.production.yml logs -f"
echo "======================================================"
EOF

chmod +x deploy-ubuntu.sh
echo "✅ Đã tạo script deploy-ubuntu.sh"
```

### Bước 5: Triển khai website

```bash
# Chạy script triển khai tự động
./deploy-ubuntu.sh
```

**🔧 Hoặc triển khai thủ công:**

```bash
# Kéo Docker image mới từ Docker Hub
docker pull assassincreed2k1/hanaya-shop:latest

# Tạo thư mục SSL nếu chưa có
mkdir -p deployment/ssl

# Tạo chứng chỉ SSL tự ký (development)
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout deployment/ssl/hanaya-shop.key \
  -out deployment/ssl/hanaya-shop.crt \
  -subj "/CN=your-domain.com/O=Hanaya Shop/C=VN" \
  -addext "subjectAltName=DNS:your-domain.com,DNS:www.your-domain.com,IP:your-server-ip"

# Khởi động các container
docker-compose -f docker-compose.production.yml up -d

# Kiểm tra trạng thái
docker-compose -f docker-compose.production.yml ps
docker-compose -f docker-compose.production.yml logs -f
```

## 💻 QUY TRÌNH HOÀN CHỈNH

### Trên máy Windows (Development):

1. **Build và push Docker image:**
```bash
# Build image mới với code hiện tại
docker build -t assassincreed2k1/hanaya-shop:latest .

# Push lên Docker Hub
docker push assassincreed2k1/hanaya-shop:latest
```

### Trên Ubuntu Server (Production):

2. **Thực hiện các bước 1-5 ở trên để tạo file cấu hình và triển khai**

3. **Cập nhật image mới (khi có thay đổi):**
```bash
cd ~/Hanaya-Shop
docker-compose -f docker-compose.production.yml down
docker pull assassincreed2k1/hanaya-shop:latest
docker-compose -f docker-compose.production.yml up -d
```

## 🔒 CẤU HÌNH SSL CHÍNH THỨC (PRODUCTION)

### Sử dụng Let's Encrypt (KHUYÊN DÙNG)

```bash
# Cài đặt Certbot
apt update
apt install -y certbot

# Dừng container tạm thời
docker-compose -f docker-compose.production.yml down

# Lấy chứng chỉ SSL (thay your-domain.com bằng domain thật)
certbot certonly --standalone -d your-domain.com -d www.your-domain.com

# Sao chép chứng chỉ vào dự án
cp /etc/letsencrypt/live/your-domain.com/fullchain.pem deployment/ssl/hanaya-shop.crt
cp /etc/letsencrypt/live/your-domain.com/privkey.pem deployment/ssl/hanaya-shop.key

# Khởi động lại container
docker-compose -f docker-compose.production.yml up -d

echo "✅ Đã cấu hình SSL chính thức"
```

### Thiết lập gia hạn SSL tự động

```bash
# Chỉnh sửa file renew-ssl.sh
nano renew-ssl.sh

# Thay đổi các dòng sau:
# DOMAIN="your-domain.com" → DOMAIN="domain-that-cua-ban.com"
# PROJECT_DIR="/var/www/hanaya-shop" → PROJECT_DIR="~/Hanaya-Shop"

# Thêm cron job để gia hạn tự động (chạy vào 3:00 AM ngày 1 hàng tháng)
crontab -e
# Thêm dòng: 0 3 1 * * ~/Hanaya-Shop/renew-ssl.sh >> /var/log/ssl-renew.log 2>&1

echo "✅ Đã thiết lập gia hạn SSL tự động"
```

## 🌐 TRUY CẬP WEBSITE

Sau khi triển khai thành công:

- **HTTP**: http://your-server-ip (tự động chuyển hướng sang HTTPS)
- **HTTPS**: https://your-server-ip 
- **Với domain**: https://your-domain.com

## 🔧 XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi kết nối Redis
```bash
# Kiểm tra logs Redis
docker-compose -f docker-compose.production.yml logs redis

# Kiểm tra logs ứng dụng
docker-compose -f docker-compose.production.yml logs app

# Khởi động lại container app
docker-compose -f docker-compose.production.yml restart app
```

### Lỗi chứng chỉ SSL
```bash
# Kiểm tra chứng chỉ có tồn tại không
ls -la deployment/ssl/

# Kiểm tra quyền truy cập
chmod 644 deployment/ssl/hanaya-shop.crt
chmod 600 deployment/ssl/hanaya-shop.key
```

### Lỗi database
```bash
# Kiểm tra logs database
docker-compose -f docker-compose.production.yml logs db

# Truy cập database để debug
docker exec -it hanaya-shop-db mysql -u hanaya_user -pTrungnghia2703 hanaya_shop
```

### Lỗi quyền truy cập (Permission denied)

Nếu gặp lỗi permission denied, thêm `sudo` vào trước lệnh:

```bash
# Thay vì:
docker-compose -f docker-compose.production.yml up -d

# Sử dụng:
sudo docker-compose -f docker-compose.production.yml up -d
```

## 📊 GIÁM SÁT & BẢO TRÌ

### Kiểm tra trạng thái
```bash
# Xem tất cả container
docker-compose -f docker-compose.production.yml ps

# Xem logs realtime
docker-compose -f docker-compose.production.yml logs -f

# Kiểm tra sử dụng tài nguyên
docker stats
```

### Sao lưu dữ liệu định kỳ
```bash
# Tạo script sao lưu tự động
cat > ~/backup-daily.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > ~/backup_$DATE.sql
find ~ -name "backup_*.sql" -mtime +7 -delete  # Xóa backup cũ hơn 7 ngày
EOF

chmod +x ~/backup-daily.sh

# Thêm vào cron để chạy hàng ngày lúc 2:00 AM
crontab -e
# Thêm dòng: 0 2 * * * ~/backup-daily.sh
```

## ✨ TÍNH NĂNG MỚI

1. **100% Nginx**: Không còn Apache, hiệu suất tối ưu
2. **HTTPS by default**: Tất cả traffic được mã hóa  
3. **Redis Predis**: Kết nối Redis ổn định hơn
4. **Auto SSL renewal**: Gia hạn chứng chỉ tự động
5. **Clean deployment**: Chỉ giữ file cần thiết cho production

## 🎯 SCRIPT TRIỂN KHAI NHANH CHO UBUNTU

Để triển khai tất cả trong một lệnh (không cần Git):

```bash
# Script tạo và triển khai hoàn chỉnh
cd ~ && \
rm -f docker-compose.yml get-docker.sh && \
docker system prune -f && \
mkdir -p ~/Hanaya-Shop && \
cd ~/Hanaya-Shop && \
docker-compose -f docker-compose.production.yml down 2>/dev/null || true && \
curl -O https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/docker-compose.production.yml && \
docker pull assassincreed2k1/hanaya-shop:latest && \
mkdir -p deployment/ssl && \
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout deployment/ssl/hanaya-shop.key -out deployment/ssl/hanaya-shop.crt -subj "/CN=localhost/O=Hanaya Shop/C=VN" -addext "subjectAltName=DNS:localhost,IP:127.0.0.1" && \
docker-compose -f docker-compose.production.yml up -d && \
echo "🎉 TRIỂN KHAI HOÀN TẤT!"
```

**Hoặc thực hiện từng bước thủ công như hướng dẫn ở trên.**

## 🔥 HOÀN TẤT

Website của bạn giờ đã chạy hoàn toàn trên Nginx với HTTPS và Redis được cấu hình đúng cách!

**Để truy cập:**
- Thay `your-server-ip` bằng IP server thật của bạn
- Thay `your-domain.com` bằng domain thật của bạn (nếu có)

**Nếu có vấn đề:** Kiểm tra logs với `docker-compose -f docker-compose.production.yml logs -f`
