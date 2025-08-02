# Script cập nhật nhanh trên Ubuntu
# Sử dụng: ./update-ubuntu.sh

#!/bin/bash

echo "======================================================"
echo "     CẬP NHẬT HANAYA SHOP TRÊN UBUNTU"
echo "======================================================"

cd ~/Hanaya-Shop

# Dừng container
echo "Dừng container..."
docker-compose -f docker-compose.production.yml down

# Sao lưu dữ liệu trước khi cập nhật
echo "Sao lưu dữ liệu..."
docker exec hanaya-shop-db mysqldump -u hanaya_user -pTrungnghia2703 hanaya_shop > ~/backup-before-update-$(date +%Y%m%d_%H%M%S).sql 2>/dev/null || echo "Database chưa chạy, bỏ qua backup"

# Kéo image mới
echo "Kéo image mới từ Docker Hub..."
docker pull assassincreed2k1/hanaya-shop:latest

# Xóa image cũ
echo "Dọn dẹp image cũ..."
docker image prune -f

# Khởi động lại
echo "Khởi động container..."
docker-compose -f docker-compose.production.yml up -d

# Kiểm tra trạng thái
echo "Kiểm tra trạng thái..."
sleep 10
docker-compose -f docker-compose.production.yml ps

echo "======================================================"
echo "     CẬP NHẬT HOÀN TẤT"
echo "======================================================"
echo "Website đã được cập nhật với image mới!"
echo "Kiểm tra logs: docker-compose -f docker-compose.production.yml logs -f"
echo "======================================================"
