# HƯỚNG DẪN TRIỂN KHAI NHANH

## Trên máy phát triển (Windows)

1. **Cài đặt Predis**
   ```
   check-redis-dependency.bat
   ```

2. **Build và đẩy Docker image**
   ```
   build-push-image.bat [version]
   ```

3. **Dọn dẹp file không cần thiết**
   ```
   cleanup-for-production.bat
   ```

## Trên server Ubuntu

1. **Chuẩn bị thư mục**
   ```bash
   cd /var/www/hanaya-shop
   ```

2. **Tải về code mới nhất**
   ```bash
   sudo git pull origin main
   ```

3. **Triển khai với script tự động**
   ```bash
   sudo chmod +x deploy-ubuntu.sh
   sudo ./deploy-ubuntu.sh
   ```

4. **Kiểm tra logs**
   ```bash
   sudo docker-compose -f docker-compose.production.yml logs -f
   ```

Xem hướng dẫn đầy đủ trong file `DEPLOYMENT_GUIDE_UBUNTU.md`
