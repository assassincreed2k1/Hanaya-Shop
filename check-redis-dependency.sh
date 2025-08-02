#!/bin/bash

# Script kiểm tra và cài đặt Predis
# Sử dụng: ./check-redis-dependency.sh

echo "======================================================"
echo "     KIỂM TRA VÀ CÀI ĐẶT PREDIS"
echo "======================================================"

# Kiểm tra xem predis đã được cài đặt chưa
if grep -q "predis/predis" composer.json; then
    echo "Predis đã được cài đặt trong composer.json"
else
    echo "Thêm predis/predis vào composer.json..."
    composer require predis/predis
fi

# Kiểm tra cấu hình Redis trong .env
if grep -q "REDIS_CLIENT=" .env; then
    echo "Cập nhật REDIS_CLIENT trong .env..."
    sed -i 's/REDIS_CLIENT=.*/REDIS_CLIENT=predis/g' .env
else
    echo "Thêm REDIS_CLIENT vào .env..."
    echo "REDIS_CLIENT=predis" >> .env
fi

echo "======================================================"
echo "     HOÀN TẤT"
echo "======================================================"
echo "Predis đã được cài đặt và cấu hình"
echo "======================================================"
