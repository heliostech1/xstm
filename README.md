# Xsense Truck Maintenance

A Laravel development environment under Docker.

- Laravel 8.x
- PHP 7.4
- Nginx 1.17
- MongoDB 4.4

วิธีติดตั้ง
1.git clone
2.สร้างไฟล์.env ไว้ใน src
#ทุกครั้งที่แก้ .env หรือ config/app.php ต้องเรียก "php artisan config:cache" หรือยกเลิกใช้ cache "php artisan config:clear"
#ไฟล์ config สำหรับ cache อยู่ที่ "bootstrap/cache/config.php" , php artisan key:generate

APP_NAME=xstm
APP_ENV=local
APP_KEY=base64:qXNVgqcQjmxF33Ht6v0+xcndAnzqUt1I85hQKh9kFr0=
APP_DEBUG=true
#APP_URL=http://localhost
LOG_LEVEL=debug

DB_CONNECTION="mongodb"
DB_HOST="localhost"
DB_PORT="27017"
DB_DATABASE="xstm"
DB_USERNAME=""
DB_PASSWORD=""

ROOT_STORAGE="D:/xstm_storage"

REST_USERNAME="helios" 
REST_PASSWORD="xstm@abcd1234"

#session expire หน่วยวินาที
APP_SESSION_EXPIRE="0"

JWT_SECRET=AjIYf9bV7llGyXNBdnY6aK5a46d7hOVVeYj0u8aQXARh0JjCGH3FQm9Oi9nIoh5j

3.composer install




