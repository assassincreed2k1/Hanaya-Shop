#!/bin/bash

# Script để tự động gia hạn chứng chỉ SSL và cập nhật vào container
# Sử dụng với cron job: 0 3 1 * * ~/Hanaya-Shop/renew-ssl.sh >> /var/log/ssl-renew.log 2>&1

PROJECT_DIR="~/Hanaya-Shop"  # Cập nhật đường dẫn cho Ubuntu
DOMAIN="your-domain.com"     # Thay thế bằng domain của bạn

echo "======================================================"
echo "     GIA HẠN CHỨNG CHỈ SSL TỰ ĐỘNG"
echo "     $(date)"
echo "======================================================"

# Dừng container
echo "Dừng các container..."
cd $PROJECT_DIR
docker-compose -f docker-compose.production.yml down

# Gia hạn chứng chỉ
echo "Gia hạn chứng chỉ SSL..."
certbot renew --non-interactive

# Kiểm tra kết quả
if [ $? -eq 0 ]; then
  echo "Gia hạn thành công. Cập nhật chứng chỉ cho container..."
  
  # Sao chép chứng chỉ mới
  cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem $PROJECT_DIR/deployment/ssl/hanaya-shop.crt
  cp /etc/letsencrypt/live/$DOMAIN/privkey.pem $PROJECT_DIR/deployment/ssl/hanaya-shop.key
  
  echo "Chứng chỉ đã được cập nhật."
else
  echo "Gia hạn thất bại hoặc chưa đến hạn gia hạn."
fi

# Khởi động lại container
echo "Khởi động lại các container..."
docker-compose -f docker-compose.production.yml up -d

echo "======================================================"
echo "     HOÀN TẤT"
echo "======================================================"
