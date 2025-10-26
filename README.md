# 🎓 BrieflyLearn Backend API

Laravel 11 backend API และ Filament Admin Panel สำหรับ BrieflyLearn - แพลตฟอร์มเรียนรู้เพื่อการพัฒนาตนเอง

![Laravel](https://img.shields.io/badge/Laravel-11-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![Filament](https://img.shields.io/badge/Filament-3.x-orange)
![MySQL](https://img.shields.io/badge/MySQL-8.0-blue)

---

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Deployment](#deployment)
- [API Documentation](#api-documentation)
- [Admin Panel](#admin-panel)

---

## ✨ Features

### Backend Features
- **RESTful API** สำหรับ Next.js Frontend
- **Filament Admin Panel** สำหรับจัดการข้อมูล
- **Authentication** ด้วย Laravel Sanctum
- **Course Management** จัดการคอร์สเรียนและบทเรียน
- **User Progress Tracking** ติดตามความก้าวหน้าผู้เรียน
- **Garden System** ระบบ gamification
- **Achievement System** ระบบความสำเร็จและรางวัล

### Admin Panel Features
- 🎨 **Custom Theme** - Claude Orange (#f97316)
- 👥 **User Management** - จัดการผู้ใช้งาน
- 📚 **Course Management** - จัดการคอร์สและบทเรียน
- 🏆 **Achievement Tracking** - ติดตามความสำเร็จ
- 🌱 **Garden Analytics** - วิเคราะห์ระบบ Garden
- 📊 **Dashboard Widgets** - Dashboard แสดงสถิติ

---

## 🔧 Requirements

- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0 or higher
- Node.js 18+ (สำหรับ build assets)
- Git

---

## 📦 Installation

### 1. Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/brieflylearn-backend.git
cd brieflylearn-backend
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install NPM Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
```

แก้ไข `.env`:

```env
APP_NAME="BrieflyLearn"
APP_URL=http://localhost:8001

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitness_lms
DB_USERNAME=fitness_user
DB_PASSWORD=fitness_pass_2024

FRONTEND_URL=http://localhost:3000
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Migrations

```bash
php artisan migrate --seed
```

### 7. Build Assets

```bash
npm run build
```

### 8. Start Development Server

```bash
php artisan serve --host=0.0.0.0 --port=8001
```

---

## 🗄️ Database Setup

### Local Development (MySQL)

```bash
# Start MySQL
brew services start mysql  # macOS
# sudo systemctl start mysql  # Linux

# Create Database
mysql -u root -p -e "CREATE DATABASE fitness_lms;"
mysql -u root -p -e "CREATE USER 'fitness_user'@'localhost' IDENTIFIED BY 'fitness_pass_2024';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON fitness_lms.* TO 'fitness_user'@'localhost';"

# Run Migrations
php artisan migrate:fresh --seed
```

### Production (Ubuntu/Vultr)

```bash
# Create Database
mysql -u root -p << EOF
CREATE DATABASE brieflylearn_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'brieflylearn_user'@'localhost' IDENTIFIED BY 'YOUR_SECURE_PASSWORD';
GRANT ALL PRIVILEGES ON brieflylearn_production.* TO 'brieflylearn_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Run Migrations
php artisan migrate --force
```

---

## 🚀 Deployment

### ดูคู่มือ Deployment แบบละเอียด:

- [VULTR_DEPLOYMENT_GUIDE.md](../VULTR_DEPLOYMENT_GUIDE.md) - คู่มือ deploy บน Vultr Ubuntu
- [SERVER_SETUP_COMMANDS.md](../SERVER_SETUP_COMMANDS.md) - รวมคำสั่งทั้งหมดสำหรับ setup server

### Quick Deploy Script

```bash
# บน Production Server
cd /var/www/brieflylearn
./deploy.sh
```

---

## 🔐 Admin Panel

### Access URL

**Local**: http://localhost:8001/admin
**Production**: https://admin.brieflylearn.com/admin

### Default Credentials

```
Email: admin@brieflylearn.com
Password: admin123
```

**⚠️ เปลี่ยน password ทันทีหลัง deploy production!**

### สร้าง Admin User ใหม่

```bash
php artisan tinker
```

```php
$admin = new \App\Models\User();
$admin->name = 'Admin';
$admin->email = 'your-email@example.com';
$admin->full_name = 'Your Name';
$admin->password = Hash::make('your-password');
$admin->password_hash = Hash::make('your-password');
$admin->role = 'admin';
$admin->email_verified_at = now();
$admin->save();
exit
```

---

## 📡 API Documentation

### Base URL

**Local**: http://localhost:8001/api/v1
**Production**: https://api.brieflylearn.com/api/v1

### Authentication

```bash
# Login
POST /api/v1/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}

# Response
{
  "user": {...},
  "token": "1|xxxxx..."
}
```

### Courses

```bash
# Get All Courses
GET /api/v1/courses

# Get Course with Lessons
GET /api/v1/courses/{id}/lessons

# Get Lesson Detail
GET /api/v1/lessons/{id}
Authorization: Bearer {token}
```

### Garden System

```bash
# Get My Garden
GET /api/v1/garden/my-garden
Authorization: Bearer {token}

# Water Garden
PUT /api/v1/garden/water-garden
Authorization: Bearer {token}

# Plant Seed
POST /api/v1/garden/plant/{plantTypeId}
Authorization: Bearer {token}
```

---

## 🏗️ Project Structure

```
brieflylearn-backend/
├── app/
│   ├── Filament/           # Filament Admin Resources
│   │   ├── Resources/      # CRUD Resources
│   │   ├── Pages/          # Custom Pages
│   │   └── Widgets/        # Dashboard Widgets
│   ├── Http/
│   │   ├── Controllers/    # API Controllers
│   │   └── Middleware/     # Custom Middleware
│   ├── Models/             # Eloquent Models
│   └── Providers/
│       └── Filament/       # Filament Configuration
├── config/                 # Configuration Files
├── database/
│   ├── migrations/         # Database Migrations
│   └── seeders/            # Database Seeders
├── public/                 # Public Assets
├── resources/
│   └── views/
│       └── filament/       # Filament Custom Views
├── routes/
│   ├── api.php            # API Routes
│   └── web.php            # Web Routes
├── storage/               # Storage & Logs
├── deploy.sh              # Deployment Script
└── .env.production.example # Production Environment Template
```

---

## 🛠️ Development

### Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

### Run Tests

```bash
php artisan test
```

---

## 🔒 Security

### Production Security Checklist

- [ ] Change default admin credentials
- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS (SSL certificates)
- [ ] Configure CORS properly
- [ ] Set up Firewall (UFW)
- [ ] Regular database backups
- [ ] Keep dependencies updated

---

## 📝 Environment Variables

### Development (.env)

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8001
DB_CONNECTION=mysql
DB_DATABASE=fitness_lms
DB_USERNAME=fitness_user
DB_PASSWORD=fitness_pass_2024
FRONTEND_URL=http://localhost:3000
```

### Production (.env)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.brieflylearn.com
DB_CONNECTION=mysql
DB_DATABASE=brieflylearn_production
DB_USERNAME=brieflylearn_user
DB_PASSWORD=YOUR_SECURE_PASSWORD
FRONTEND_URL=https://brieflylearn.com
SESSION_SECURE_COOKIE=true
```

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 📞 Support

- **Email**: support@brieflylearn.com
- **Documentation**: [Full Documentation](../VULTR_DEPLOYMENT_GUIDE.md)

---

## 🙏 Acknowledgments

- Laravel Framework
- Filament Admin Panel
- Claude Orange Theme

---

**Last Updated**: 2025-10-25
**Version**: 1.0.0
