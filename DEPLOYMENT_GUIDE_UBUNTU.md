# HƯỚNG DẪN TRIỂN KHAI HANAYA SHOP TRÊN UBUNTU SERVER

## 1. Yêu cầu hệ thống

- Ubuntu 20.04 LTS hoặc mới hơn
- Docker và Docker Compose đã được cài đặt
- Quyền sudo để thực hiện lệnh
- Git để clone repository (nếu cần)

## 2. Chuẩn bị

### Đăng nhập vào server

```bash
ssh username@server_ip
```

### Tạo thư mục cho dự án (nếu chưa có)

```bash
sudo mkdir -p /var/www/hanaya-shop
cd /var/www/hanaya-shop
```

### Clone repository (nếu cần)

```bash
sudo git clone https://github.com/assassincreed2k1/Hanaya-Shop.git .
# Hoặc
sudo git pull origin main # Cập nhật code mới
```

### Chuẩn bị file môi trường

```bash
sudo cp .env.example .env
sudo nano .env # Chỉnh sửa các thông số môi trường
```

## 3. Triển khai với HTTPS

### Sử dụng script triển khai tự động

```bash
sudo chmod +x deploy-ubuntu.sh
sudo ./deploy-ubuntu.sh
```

### Hoặc thực hiện thủ công

```bash
# Dừng các container đang chạy
sudo docker-compose -f docker-compose.production.yml down

# Xóa images cũ
sudo docker image prune -f

# Kéo image mới nhất từ Docker Hub
sudo docker pull assassincreed2k1/hanaya-shop:latest

# Tạo thư mục SSL nếu chưa tồn tại
sudo mkdir -p deployment/ssl

# Tạo chứng chỉ tự ký (nếu chưa có)
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout deployment/ssl/hanaya-shop.key \
  -out deployment/ssl/hanaya-shop.crt \
  -subj "/CN=localhost/O=Hanaya Shop/C=VN" \
  -addext "subjectAltName=DNS:your-domain.com,DNS:www.your-domain.com,IP:your-server-ip"

# Khởi động các container
sudo docker-compose -f docker-compose.production.yml up -d

# Kiểm tra trạng thái
sudo docker-compose -f docker-compose.production.yml ps
```

## 4. Cấu hình SSL cho Domain thật

Để sử dụng với domain thật và chứng chỉ SSL chính thức:

### Cài đặt Certbot

```bash
sudo apt update
sudo apt install -y certbot
```

### Dừng container trước khi lấy chứng chỉ

```bash
sudo docker-compose -f docker-compose.production.yml down
```

### Lấy chứng chỉ SSL với Certbot

```bash
sudo certbot certonly --standalone -d your-domain.com -d www.your-domain.com
```

### Sao chép chứng chỉ vào thư mục dự án

```bash
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem deployment/ssl/hanaya-shop.crt
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem deployment/ssl/hanaya-shop.key
```

### Khởi động lại container

```bash
sudo docker-compose -f docker-compose.production.yml up -d
```

## 5. Cấu hình tự động gia hạn SSL

Tạo script gia hạn SSL:

```bash
sudo nano renew-ssl.sh
```

Thêm nội dung:

```bash
#!/bin/bash
# Dừng container
docker-compose -f /var/www/hanaya-shop/docker-compose.production.yml down

# Gia hạn chứng chỉ
certbot renew

# Sao chép chứng chỉ mới
cp /etc/letsencrypt/live/your-domain.com/fullchain.pem /var/www/hanaya-shop/deployment/ssl/hanaya-shop.crt
cp /etc/letsencrypt/live/your-domain.com/privkey.pem /var/www/hanaya-shop/deployment/ssl/hanaya-shop.key

# Khởi động lại container
docker-compose -f /var/www/hanaya-shop/docker-compose.production.yml up -d
```

Thêm quyền thực thi và thiết lập cron job:

```bash
sudo chmod +x renew-ssl.sh
sudo crontab -e
```

Thêm dòng:

```
0 3 1 * * /var/www/hanaya-shop/renew-ssl.sh >> /var/log/ssl-renew.log 2>&1
```

## 6. Xử lý lỗi

### Lỗi kết nối Redis

Nếu bạn gặp lỗi "php_network_getaddresses: getaddrinfo for redis failed":

1. Kiểm tra logs:
```bash
sudo docker-compose -f docker-compose.production.yml logs app
```

2. Chỉnh sửa cấu hình Redis trong docker-compose.production.yml:
```yaml
environment:
  - REDIS_HOST=redis
  - REDIS_CLIENT=predis
```

3. Khởi động lại container:
```bash
sudo docker-compose -f docker-compose.production.yml restart app
```

### Lỗi chứng chỉ SSL

Nếu trình duyệt báo lỗi về chứng chỉ không an toàn:

1. Đảm bảo bạn đã cấu hình đúng domain trong Nginx
2. Kiểm tra chứng chỉ đã được cấu hình đúng trong container
3. Nếu sử dụng chứng chỉ tự ký, bạn sẽ luôn thấy cảnh báo này (chỉ nên dùng cho môi trường phát triển)

## 7. Bảo trì

### Sao lưu dữ liệu

```bash
# Sao lưu database
sudo docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > backup-$(date +%Y%m%d).sql
```

### Kiểm tra logs

```bash
sudo docker-compose -f docker-compose.production.yml logs -f
```
