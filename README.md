

# 🛒 Hanaya Shop - E-commerce Platform

[![Docker](https://img.shields.io/badge/Docker-Ready-blue)](https://hub.docker.com/r/assassincreed2k1/hanaya-shop)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-red)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-purple)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)](https://mysql.com/)
[![Redis](https://img.shields.io/badge/Redis-7.0-red)](https://redis.io/)

Modern e-commerce platform built with Laravel, featuring a complete shopping experience with Docker deployment.

## ✨ Features

- 🛒 Complete shopping cart functionality
- 👤 User authentication and profiles
- 📦 Product catalog and management
- 📋 Order management system
- 💳 Payment integration ready
- 📧 Email notifications
- 🔍 Search and filtering
- 📱 Responsive design
- 🚀 Docker deployment ready

## 🚀 Quick Deployment on Ubuntu

### One-line installation:
```bash
curl -fsSL https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-setup.sh | bash
```

### Manual installation:
```bash
wget https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-setup.sh
chmod +x ubuntu-setup.sh
./ubuntu-setup.sh
```

## 🏗️ Architecture

- **Frontend**: Laravel Blade templates with Alpine.js
- **Backend**: Laravel 10.x with PHP 8.2
- **Database**: MySQL 8.0
- **Cache/Sessions**: Redis 7.0
- **Web Server**: Nginx
- **Process Manager**: Supervisor
- **Containerization**: Docker & Docker Compose

## 📦 Installation

### Requirements
- Docker & Docker Compose
- Ubuntu 20.04+ (recommended)
- 2GB RAM minimum
- 10GB free disk space

### Development Setup
```bash
# Clone repository
git clone https://github.com/assassincreed2k1/Hanaya-Shop.git
cd Hanaya-Shop

# Copy environment file
cp .env.example .env

# Install dependencies
composer install
npm install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

### Production Deployment
Use the provided Ubuntu deployment scripts for production setup.

## � Management

### Update application:
```bash
cd ~/hanayashop
wget https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-update.sh
chmod +x ubuntu-update.sh
./ubuntu-update.sh
```

### Backup data:
```bash
cd ~/hanayashop
wget https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-backup.sh
chmod +x ubuntu-backup.sh
./ubuntu-backup.sh
```

### View logs:
```bash
cd ~/hanayashop
docker-compose logs -f
```

## 📚 Documentation

- [Ubuntu Deployment Guide](UBUNTU_DEPLOYMENT_GUIDE_FINAL.md)
- [Deployment Success](DEPLOYMENT_SUCCESS.md)

## 🔧 Configuration

### Environment Variables
Key environment variables for production:

```bash
APP_NAME="Hanaya Shop"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=hanaya_shop
DB_USERNAME=hanaya_user
DB_PASSWORD=your_secure_password

REDIS_HOST=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## � Performance

- **Caching**: Redis for session and cache storage
- **Optimization**: OPcache enabled, Laravel optimizations applied
- **Database**: MySQL with optimized configuration
- **Static Assets**: Nginx serving with proper caching headers

## 🔒 Security

- Security headers configured
- Input validation and sanitization
- CSRF protection
- Password hashing with bcrypt
- Rate limiting implemented

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**assassincreed2k1**
- GitHub: [@assassincreed2k1](https://github.com/assassincreed2k1)

## 🙏 Acknowledgments

- Laravel Framework
- Docker Community
- Open Source Contributors

---

**⭐ Star this repository if you find it helpful!**

<details>
<summary><strong>🇻🇳 Tiếng Việt</strong></summary>

## Giới thiệu

**Hanaya Shop** là ứng dụng web bán hoa online hiện đại, giúp người dùng dễ dàng lựa chọn, đặt mua và thanh toán hoa tươi qua giao diện tối ưu, thân thiện.
Dự án này thể hiện kinh nghiệm thực tế xây dựng nền tảng thương mại điện tử mở rộng.

---

## 🎯 Mục tiêu dự án

- Xây dựng nền tảng thương mại điện tử đơn giản, dễ mở rộng cho cửa hàng hoa
- Quản lý sản phẩm (hoa), giỏ hàng, đơn hàng hiệu quả
- Tích hợp giao diện quản trị cho admin
- Triển khai nhanh bằng **Docker** (không cần chỉnh `.env`)

---

## 🌟 Tính năng chính

### 👤 Dành cho khách hàng
- Xem danh sách hoa, lọc theo loại/dịp/giá
- Xem chi tiết sản phẩm, hình ảnh, giá
- Thêm vào giỏ hàng, tạo đơn hàng
- Xem lịch sử mua hàng (nếu đã đăng ký)

### 🛠️ Dành cho quản trị viên
- Quản lý danh mục hoa
- CRUD sản phẩm: thêm, sửa, xóa, bật/tắt hiển thị
- Quản lý đơn hàng: xác nhận, huỷ, cập nhật trạng thái
- Quản lý khách hàng

---

## 🛠️ Công nghệ sử dụng & Hiệu quả

- **PHP 8.2**: Phiên bản mới nhất, tăng bảo mật, hiệu năng và dễ bảo trì.
- **Laravel 12.2**: Framework hiện đại, phát triển nhanh, xác thực/ủy quyền mạnh mẽ, API RESTful, dễ kiểm thử.
- **MySQL**: CSDL quan hệ, xử lý dữ liệu lớn, quản lý giao dịch hiệu quả.
- **Blade template**: Giao diện server-side, tối ưu SEO, hiệu năng, tái sử dụng UI.
- **Docker Compose**: Tự động hóa môi trường, quản lý phụ thuộc, loại bỏ lỗi môi trường, sẵn sàng CI/CD.
- **Tailwind CSS**: UI hiện đại, responsive, nâng cao trải nghiệm người dùng.
- **PHPUnit**: Đảm bảo chất lượng qua kiểm thử đơn vị và chức năng.

Những công nghệ này giúp dự án đạt hiệu quả cao về tốc độ phát triển, bảo trì, mở rộng, bảo mật và hiệu năng.

---

## � Hướng dẫn triển khai Ubuntu

### Cài đặt một dòng:
```bash
curl -fsSL https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-setup.sh | bash
```

### Cài đặt thủ công:
```bash
wget https://raw.githubusercontent.com/assassincreed2k1/Hanaya-Shop/main/ubuntu-setup.sh
chmod +x ubuntu-setup.sh
./ubuntu-setup.sh
```

</details>