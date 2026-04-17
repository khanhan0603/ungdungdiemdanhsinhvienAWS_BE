# Ứng dụng điểm danh sinh viên sử dụng AWS (Backend)
## Giới thiệu
Đây là **Backend** của hệ thống **điểm danh sinh viên sử dụng AWS**, phục vụ cho việc:
- Quản lý sinh viên, giảng viên
- Điểm danh sinh viên
- Xác thực người dùng bằng JWT
- Gửi email (reset mật khẩu, thông báo)
- Tích hợp AWS (S3, Rekognition)

Backend được xây dựng bằng **Laravel Framework**, cung cấp API cho ứng dụng Frontend / Mobile.

## Công nghệ sử dụng
- **PHP** >= 8.3
- **Laravel Framework**
- **MySQL**
- **JWT Authentication**
- **AWS S3 / Rekognition**
- **Gmail SMTP**

## Setup môi trường (Backend Laravel)

### 1. Yêu cầu hệ thống
- PHP >= 8.3
- Composer
- MySQL
- Git
- WAMP / XAMPP / Laragon

### 2. Clone source
```bash
git clone https://github.com/khanhan0603/ungdungdiemdanhsinhvienAWS.git
cd ungdungdiemdanhsinhvienAWS/BE
```
### 3. Cài đặt thư viện
```bash
composer install
```

### 4. Tạo file cấu hình môi trường
```bash
cp .env.example .env
```

### 5. Sinh APP_KEY
```bash
php artisan key:generate
```

### 6. Cấu hình database
tạo database và import file sql trong thư mục database.
cấu hình lại trong .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ungdungdiemdanhsinhvien
DB_USERNAME=root
DB_PASSWORD=
```

## Cấu hình JWT
tạo JWT Secret
```bash
php artisan jwt:secret
```

## Cấu hình Email(Gmail SMTP)
MAIL_PASSWORD sử dụng Gmail App Password (không dùng mật khẩu Gmail chính). Cần bật 2-Step Verification cho tài khoản Gmail.
Ví dụ:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=a@gmail.com
MAIL_PASSWORD=gmail-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="a@gmail.com"
MAIL_FROM_NAME="Quản lý giảng viên"
```

## Cấu hình AWS (nếu có)
```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=ap-southeast-2
AWS_BUCKET=your_bucket_name
```

## Gọi API 
Ví dụ
```bash
http://localhost/ungdungdiemdanhsinhvienAWS/BE/public/api/lists
```

