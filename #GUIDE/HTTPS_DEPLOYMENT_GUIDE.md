# Hướng dẫn triển khai HTTPS cho Hanaya Shop

## 1. Chuẩn bị SSL Certificate

### Tùy chọn 1: SSL Certificate miễn phí (Let's Encrypt)
```bash
# Cài đặt Certbot
sudo apt update
sudo apt install certbot python3-certbot-apache

# Tạo certificate cho domain
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

### Tùy chọn 2: SSL Certificate trả phí
- Mua SSL từ nhà cung cấp (GoDaddy, Namecheap, etc.)
- Upload certificate files lên server

## 2. Cấu hình Web Server

### Apache (.htaccess)
```bash
# Copy file example
cp public/.htaccess.https.example public/.htaccess
```

### Nginx
```bash
# Copy và chỉnh sửa config
cp deployment/nginx/https.conf.example /etc/nginx/sites-available/hanaya-shop
sudo ln -s /etc/nginx/sites-available/hanaya-shop /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 3. Cấu hình Laravel

### Environment Variables
```bash
# Copy và chỉnh sửa .env
cp .env.https.example .env

# Cập nhật các giá trị:
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
FORCE_HTTPS=true
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 4. Test HTTPS

### Kiểm tra redirect HTTP -> HTTPS
```bash
curl -I http://yourdomain.com
# Should return: HTTP/1.1 301 Moved Permanently
# Location: https://yourdomain.com/
```

### Kiểm tra SSL certificate
```bash
# Online tools:
# - https://www.ssllabs.com/ssltest/
# - https://www.digicert.com/help/
```

### Kiểm tra security headers
```bash
curl -I https://yourdomain.com
# Should include:
# Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
# X-Frame-Options: DENY
# X-Content-Type-Options: nosniff
```

## 5. Cập nhật DNS (nếu cần)

### Cloudflare
- Bật SSL/TLS mode: "Full (strict)"
- Bật "Always Use HTTPS"
- Bật "HTTP Strict Transport Security (HSTS)"

### Các DNS provider khác
- Đảm bảo A record trỏ đúng IP server
- Cập nhật CNAME nếu sử dụng CDN

## 6. Cập nhật ứng dụng

### Frontend URLs
- Kiểm tra tất cả hardcoded URLs trong views
- Sử dụng `{{ url('/') }}` thay vì hardcode domain
- Sử dụng `{{ secure_url('/') }}` nếu cần force HTTPS

### API Endpoints
- Cập nhật base URL trong JavaScript
- Kiểm tra CORS settings nếu có API external

### External Services
- Cập nhật webhook URLs (payment, social login, etc.)
- Cập nhật callback URLs cho OAuth

## 7. Monitoring

### Log Files
- Monitor error logs: `/var/log/nginx/error.log`
- Laravel logs: `storage/logs/laravel.log`

### Certificate Expiry
```bash
# Check certificate expiry
echo | openssl s_client -servername yourdomain.com -connect yourdomain.com:443 2>/dev/null | openssl x509 -noout -dates
```

### Auto-renewal (Let's Encrypt)
```bash
# Add to crontab
0 12 * * * /usr/bin/certbot renew --quiet
```

## 8. Rollback Plan

Nếu gặp vấn đề, có thể tạm thời tắt HTTPS:

```bash
# Disable HTTPS middleware
# Comment out line in bootstrap/app.php:
# $middleware->web(\App\Http\Middleware\ForceHttpsMiddleware::class);

# Update .env
FORCE_HTTPS=false
APP_URL=http://yourdomain.com
SESSION_SECURE_COOKIE=false

# Clear cache
php artisan config:clear
```

## Lưu ý quan trọng

1. **Backup trước khi triển khai HTTPS**
2. **Test trên staging environment trước**
3. **Thông báo cho users về việc chuyển đổi**
4. **Kiểm tra tất cả third-party integrations**
5. **Monitor traffic và error logs sau khi deploy**
