# ZINOU Tv - Installation & Setup Guide

This guide describes how to install, set up, and run the ZINOU Tv backend (Laravel 11) and client application (Android Native).

---

## 🛠️ Requirements & Prerequisites

To run this project locally, you will need:
1. **PHP 8.2+** (with openssl, pdo_mysql, mbstring, openssl extensions enabled)
2. **Composer 2.x** (PHP package manager)
3. **MySQL / MariaDB** (database server)
4. **Android Studio Koala (or newer)** (for editing and compiling the Android client)

> [!TIP]
> The easiest way to install PHP, Composer, and MySQL together on Windows is to use **Laragon** (recommended) or **XAMPP**.
> - Download Laragon from [laragon.org](https://laragon.org)
> - Laragon will set up local domains (e.g. `sport-iptv.test`) and install Composer and MySQL automatically.

---

## 🚀 Step 1: Backend Setup (Laravel 11)

### 1. Install PHP & Composer
If you are using Laragon:
- Start Laragon.
- It will automatically initialize PHP and database servers.
- Open Laragon Terminal.

### 2. Prepare Environment Config
- In the `/backend/` directory, copy `.env.example` to `.env`.
- Open `.env` and fill in:
  - `DB_DATABASE=sport_iptv` (Create this empty database in phpMyAdmin/MySQL first)
  - `DB_USERNAME=root` (or your MySQL username)
  - `DB_PASSWORD=` (your MySQL password)

### 3. Install Dependencies
In the `/backend/` folder, run:
```bash
composer install
```
This will download Laravel, JWT Auth, and other packages.

### 4. Generate Application Secrets
Generate the Laravel app key and JWT secret:
```bash
php artisan key:generate
php artisan jwt:secret
```

### 5. Run Database Migrations and Seeders
This will create all 8 tables and insert the default admin user and channels/categories:
```bash
php artisan migrate --seed
```

### 6. Start the Local Server
Start Laravel development server:
```bash
php artisan serve
```
By default, the server runs on `http://127.0.0.1:8000`.

---

## 📱 Step 2: Client Setup (Android Native)

### 1. Open in Android Studio
- Open Android Studio.
- Choose **Open Project** and select the `/android/` directory.

### 2. Verify API URL
- Open `/android/app/src/main/java/com/sportiptv/app/util/Constants.kt`.
- Locate `BASE_URL`.
  - If running on a physical Android device, replace `10.0.2.2` with your host computer's local IP address (e.g., `192.168.1.50`).
  - If running on Android Studio Emulator, keep `http://10.0.2.2:8000/api/` (matches localhost port 8000 of backend server).

### 3. Sync & Build Project
- Let Android Studio perform a Gradle sync to download libraries.
- Click **Run** button to launch the app on your phone or emulator.

---

## 🔑 Login Credentials (Defaults)

### Admin Panel
- **URL**: `http://127.0.0.1:8000/admin/login`
- **Email**: `admin@sportiptv.com`
- **Password**: `password`

### Client License Codes
- Access the Admin panel.
- Go to **Activation Codes** -> Click **Generate Codes**.
- Choose a duration (e.g. 6 months) and generate.
- Copy a generated code (e.g. `ABCD-1234-EFGH-5678`).
- Open the Android app, enter the activation code, and hit **Activate**. The app will automatically connect and log you in!
