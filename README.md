
# Nhờ Anh Review Dự Án Hanaya Shop

Em chào anh ạ,

Em vừa hoàn thiện dự án sử dụng Laravel, Vite, Docker, Render.com và mong muốn nhờ anh review giúp em các vấn đề còn tồn tại hoặc những điểm cần tối ưu khi deploy thực tế.

---

## 1. Thông tin triển khai
- **Docker Image:** `assassincreed2k1/hanaya-shop:latest` (em đã push lên Docker Hub, chạy bình thường trên local)
- **Deploy trên Render.com:** Sử dụng Existed Images (Em push lên Docker Hub rồi sử dụng ạ: https://hub.docker.com/r/assassincreed2k1/hanaya-shop/tags)
- **Cấu hình server:**
  - RAM: 512 MB
  - CPU: 0.1 VCPU

---

## 2. Database
- **Production:** Tự động tạo SQLite khi deploy (chưa có DB public riêng)
- **Local:** Kết nối MySQL để phát triển
- **Câu hỏi:** Em có nên tạo DB public không, hay dùng SQLite như hiện tại là ổn ạ?

---

## 3. Vấn đề gặp phải
- **Lỗi Index DB** Em gặp lỗi SQLite không hỗ trợ Index như bên MySQL (Em chưa biết nên sửa sao ạ): [2025-07-30 10:20:35] production.ERROR: This database driver does not support fulltext index creation. {"exception":"[object] (RuntimeException(code: 0): This database driver does not support fulltext index creation. at /var/www/html/vendor/laravel/framework/src/Illuminate/Database/Schema/Grammars/Grammar.php:208)
- **Lỗi 500** Em liên tục gặp lỗi 500 mà không rõ nguyên nhân ạ (Em đã bật Debug = True nhưng mà không hiểu sao không hiện log lỗi ạ)

---

## 4. Những điều em chưa rõ khi deploy
- Khi deploy lên server, em không biết còn thiếu gì so với môi trường local?
- Có cần tối ưu gì thêm cho production không?
- Có nên cấu hình lại cache/session/queue cho phù hợp với production?
- Có cần bổ sung healthcheck, giám sát, backup DB, hoặc các best practice nào khác không?

---

## 5. Mong anh góp ý
- Anh có thể kiểm tra giúp em về bảo mật, hiệu năng, cấu trúc Dockerfile, CI/CD, hoặc bất kỳ vấn đề nào khác?
- Nếu có điểm nào chưa hợp lý hoặc có thể tối ưu, mong anh chỉ giúp em với ạ!

---

# Em cảm ơn anh rất nhiều!
