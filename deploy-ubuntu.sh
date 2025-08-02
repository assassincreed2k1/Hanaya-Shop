#!/bin/bash

# Script để triển khai Hanaya Shop trên Ubuntu server
# Sử dụng: ./deploy-ubuntu.sh (không cần sudo)

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
