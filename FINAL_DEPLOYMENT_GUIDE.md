# HƯỚNG DẪN TRIỂN KHAI HANAYA SHOP TRÊN UBUNTU SERVER - FINAL

## 🔥 TÓM TẮT CÁC THAY ĐỔI CHÍNH

✅ **Đã sửa lỗi Redis**: Cài đặt Predis v3.1.0 và cấu hình REDIS_CLIENT=predis
✅ **Đã chuyển sang Nginx**: Loại bỏ hoàn toàn Apache, sử dụng 100% Nginx + PHP-FPM
✅ **Đã cấu hình HTTPS**: SSL certificates và redirect HTTP → HTTPS
✅ **Đã dọn dẹp**: Xóa tất cả file development không cần thiết
✅ **Docker Image đã build**: assassincreed2k1/hanaya-shop:latest sẵn sàng sử dụng

## 🚀 TRIỂN KHAI TRÊN UBUNTU

### 1. Dừng bản cũ và dọn dẹp

```bash
# Di chuyển tới thư mục dự án (theo cấu trúc hiện tại)
cd ~/Hanaya-Shop

# Dừng container cũ (nếu có)
docker-compose -f docker-compose.production.yml down 2>/dev/null || true

# Xóa images cũ để tiết kiệm dung lượng
docker image prune -f

# Sao lưu dữ liệu (tùy chọn)
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > ~/backup-$(date +%Y%m%d).sql 2>/dev/null || echo "Database chưa tồn tại, bỏ qua sao lưu"
```

### 2. Dọn dẹp file không cần thiết trong Ubuntu

```bash
# Xóa file docker-compose.yml cũ ở thư mục home (nếu có)
rm -f ~/docker-compose.yml

# Xóa script get-docker.sh (đã cài Docker rồi)
rm -f ~/get-docker.sh

# Dọn dẹp cache Docker
docker system prune -f

# Dọn dẹp các file development không cần thiết trong dự án
cd ~/Hanaya-Shop
rm -rf node_modules/ .vite/ tests/ .vscode/ || true
rm -f package*.json vite.config.js tailwind.config.js postcss.config.js || true
rm -f phpunit.xml .gitignore .editorconfig || true
rm -f cleanup.* build-push-image.* check-redis-dependency.* || true
rm -f quick-deploy.* update-csp.* || true
rm -f README_DEV.md README_DEVOPP.md README_Fix.md || true
```

### 3. Cập nhật code mới

```bash
# Kéo code mới từ GitHub
git pull origin main

# Cấp quyền thực thi cho script
chmod +x deploy-ubuntu.sh
chmod +x renew-ssl.sh
```

### 4. Triển khai tự động

```bash
# Chạy script triển khai tự động
./deploy-ubuntu.sh
```

**Hoặc triển khai thủ công:**

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

### 5. Dọn dẹp hoàn toàn sau khi triển khai thành công

```bash
# Xóa các file build tools và development dependencies
rm -f artisan composer.json composer.lock || true
rm -rf bootstrap/ config/ database/ resources/ routes/ storage/framework/ || true
rm -rf vendor/ app/ tests/ || true

# Chỉ giữ lại những file cần thiết cho deployment
ls -la
# Kết quả mong muốn chỉ còn:
# - docker-compose.production.yml
# - .env.production  
# - deployment/
# - deploy-ubuntu.sh
# - renew-ssl.sh
# - FINAL_DEPLOYMENT_GUIDE.md
```

## 🔒 CẤU HÌNH SSL CHÍNH THỨC (PRODUCTION)

### Sử dụng Let's Encrypt (KHUYÊN DÙNG)

```bash
# Cài đặt Certbot
apt update
apt install -y certbot

# Dừng container tạm thời
docker-compose -f docker-compose.production.yml down

# Lấy chứng chỉ SSL
certbot certonly --standalone -d your-domain.com -d www.your-domain.com

# Sao chép chứng chỉ vào dự án
cp /etc/letsencrypt/live/your-domain.com/fullchain.pem deployment/ssl/hanaya-shop.crt
cp /etc/letsencrypt/live/your-domain.com/privkey.pem deployment/ssl/hanaya-shop.key

# Khởi động lại container
docker-compose -f docker-compose.production.yml up -d
```

### Thiết lập gia hạn SSL tự động

```bash
# Chỉnh sửa file renew-ssl.sh với domain của bạn
nano renew-ssl.sh
# Thay "your-domain.com" thành domain thật của bạn
# Thay PROJECT_DIR="/var/www/hanaya-shop" thành PROJECT_DIR="~/Hanaya-Shop"

# Thêm cron job để gia hạn tự động
crontab -e
# Thêm dòng: 0 3 1 * * ~/Hanaya-Shop/renew-ssl.sh >> /var/log/ssl-renew.log 2>&1
```
  -out deployment/ssl/hanaya-shop.crt \
  -subj "/CN=your-domain.com/O=Hanaya Shop/C=VN" \
  -addext "subjectAltName=DNS:your-domain.com,DNS:www.your-domain.com,IP:your-server-ip"

# Khởi động các container
sudo docker-compose -f docker-compose.production.yml up -d

# Kiểm tra trạng thái
sudo docker-compose -f docker-compose.production.yml ps
sudo docker-compose -f docker-compose.production.yml logs -f
```

## 🔒 CẤU HÌNH SSL CHÍNH THỨC (PRODUCTION)

### Sử dụng Let's Encrypt (KHUYÊN DÙNG)

```bash
# Cài đặt Certbot
sudo apt update
sudo apt install -y certbot

# Dừng container tạm thời
sudo docker-compose -f docker-compose.production.yml down

# Lấy chứng chỉ SSL
sudo certbot certonly --standalone -d your-domain.com -d www.your-domain.com

# Sao chép chứng chỉ vào dự án
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem deployment/ssl/hanaya-shop.crt
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem deployment/ssl/hanaya-shop.key

# Khởi động lại container
sudo docker-compose -f docker-compose.production.yml up -d
```

### Thiết lập gia hạn SSL tự động

```bash
# Chỉnh sửa file renew-ssl.sh với domain của bạn
sudo nano renew-ssl.sh
# Thay "your-domain.com" thành domain thật của bạn

# Thêm cron job để gia hạn tự động
sudo crontab -e
# Thêm dòng: 0 3 1 * * /var/www/hanaya-shop/renew-ssl.sh >> /var/log/ssl-renew.log 2>&1
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
sudo docker-compose -f docker-compose.production.yml logs redis

# Kiểm tra logs ứng dụng
sudo docker-compose -f docker-compose.production.yml logs app

# Khởi động lại container app
sudo docker-compose -f docker-compose.production.yml restart app
```

### Lỗi chứng chỉ SSL
```bash
# Kiểm tra chứng chỉ có tồn tại không
ls -la deployment/ssl/

# Kiểm tra quyền truy cập
sudo chmod 644 deployment/ssl/hanaya-shop.crt
sudo chmod 600 deployment/ssl/hanaya-shop.key
```

### Lỗi database
```bash
# Kiểm tra logs database
sudo docker-compose -f docker-compose.production.yml logs db

# Truy cập database để debug
sudo docker exec -it hanaya-shop-db mysql -u hanaya_user -pTrungnghia2703 hanaya_shop
```

## 📊 GIÁM SÁT & BẢO TRÌ

### Kiểm tra trạng thái
```bash
# Xem tất cả container
sudo docker-compose -f docker-compose.production.yml ps

# Xem logs realtime
sudo docker-compose -f docker-compose.production.yml logs -f

# Kiểm tra sử dụng tài nguyên
sudo docker stats
```

### Sao lưu dữ liệu
```bash
# Sao lưu database
sudo docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > backup-$(date +%Y%m%d).sql

# Sao lưu files
sudo tar -czf hanaya-backup-$(date +%Y%m%d).tar.gz /var/www/hanaya-shop
```

## ✨ TÍNH NĂNG MỚI

1. **100% Nginx**: Không còn Apache, hiệu suất tối ưu
2. **HTTPS by default**: Tất cả traffic được mã hóa
3. **Redis Predis**: Kết nối Redis ổn định hơn
4. **Auto SSL renewal**: Gia hạn chứng chỉ tự động
5. **Clean codebase**: Loại bỏ file development không cần thiết

## 🎯 HOÀN TẤT

Website của bạn giờ đã chạy hoàn toàn trên Nginx với HTTPS và Redis được cấu hình đúng cách!

Nếu có bất kỳ vấn đề nào, hãy kiểm tra logs và sử dụng các lệnh troubleshooting ở trên.
