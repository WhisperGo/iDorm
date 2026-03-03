<div align="center">
  <h1>🏨 iDorm - Dormitory Management System</h1>
  <p>An integrated dormitory management system built with Laravel to streamline and digitize operations, from facility booking to centralized building maintenance reporting.</p>
</div>

## 📑 Table of Contents
- [📖 Overview](#-overview)
- [🚀 Key Features](#-key-features)
- [💻 Tech Stack](#-tech-stack)
- [📋 Requirements](#-requirements)
- [🛠️ Local Setup & Installation](#️-local-setup--installation)
  - [1. Clone the Repository](#1-clone-the-repository)
  - [2. Install Dependencies](#2-install-dependencies)
  - [3. Environment Configuration](#3-environment-configuration)
  - [4. Database Setup (XAMPP)](#4-database-setup-xampp)
  - [5. Google Maps API Key Setup](#5-google-maps-api-key-setup)
  - [6. Access the Application (Laravel Herd)](#6-access-the-application-laravel-herd)
- [🌐 Live Deployment](#-live-deployment)
- [⚖️ License](#️-license)

---

## 📖 Overview
iDorm is a comprehensive, centralized dormitory management platform designed to modernize the administrative workflow of student or employee housing. By digitizing daily operations ranging from secure access management and conflict-free facility scheduling to a fully-documented maintenance reporting system, iDorm ensures a seamless, structured, and efficient living experience for both residents and management staff.

## 🚀 Key Features

- **🔐 Custom Authentication System**: Secure login using a unique **4-Digit Card ID** seamlessly integrated with the resident database.
- **📅 Facility Booking Engine (Anti-Clash)**:
  - Precision booking based on **15-minute time slots**.
  - Advanced **Anti-Clash logic** to prevent scheduling conflicts for the same item.
  - **Early Release** functionality to optimize availability and release unused slots for other residents.
- **🛠️ Building Maintenance Reporting**: Empower residents to easily report building damages, complete with photo evidence uploads for quick resolution.
- **🚫 Targeted Facility Suspension**: The ability to restrict access to a specific facility (e.g., suspending a user from using the washing machine) to maintain discipline while keeping other privileges intact.
- **📢 Announcement Hub**: A digital, centralized information center for official announcements from dormitory management.

## 💻 Tech Stack

| Layer | Technology |
| :---: | :--- |
| **Backend Framework** | Laravel 12.x |
| **Templating Engine** | Blade |
| **Frontend Framework** | Bootstrap 5 |
| **Database** | MySQL 8.0 |
| **Libraries/Tools** | `barryvdh/laravel-dompdf` (PDF Report Generation)<br>`maatwebsite/excel` (Data Exporting)<br>`filepond` (Smooth File Uploads) |
| **Security Mechanism** | Soft Deletes implementation for secure data management |

## 📋 Requirements

Before getting started with local installation, make sure the following tools are installed on your machine:

| Tool | Version | Description |
| :---: | :---: | :--- |
| **[PHP](https://www.php.net/)** | 8.4 | Server-side scripting language |
| **[Python](https://www.python.org/)** | 3.11 | Used for ML integration modules |
| **[Laravel Herd](https://herd.laravel.com/)** | Latest | Local PHP web server for serving the application |
| **[XAMPP](https://www.apachefriends.org/index.html)** | Latest | Provides the MySQL 8.0 database server |
| **[Composer](https://getcomposer.org/)** | Latest | PHP dependency manager |
| **[Git](https://git-scm.com/)** | Latest | Version control for cloning the repository |

## 🛠️ Local Setup & Installation

### 1. Clone the Repository
Open your terminal and clone the repository using Git into your `Herd` directory.

**For Mac / Linux**:
```bash
cd ~/Herd
git clone https://github.com/WhisperGo/iDorm.git
cd iDorm
```

**For Windows**:
```bash
cd C:\Users\%USERNAME%\Herd
git clone https://github.com/WhisperGo/iDorm.git
cd iDorm
```

### 2. Install Dependencies
Install the required PHP packages:
```bash
composer install
```

### 3. Environment Configuration
Duplicate the example environment file to create your own:
```bash
cp .env.example .env
```
Generate the application key:
```bash
php artisan key:generate
```

### 4. Database Setup (XAMPP)
1. Open XAMPP Control Panel and **Start XAMPP's MySQL service**.
2. Open phpMyAdmin (usually `http://localhost/phpmyadmin`) and create a new database called `idorm`.
3. Open the `.env` file and update your database credentials. For a default XAMPP setup, it should look like this:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=idorm
DB_USERNAME=root
DB_PASSWORD=
```
4. Run migrations and seed the database with required initial data (Roles, Statuses, and Users):
```bash
php artisan migrate --seed
```

### 5. Google Maps API Key Setup
iDorm uses the Google Maps JavaScript API. You need to generate your own API key to enable map features.

1. Go to the [Google Cloud Console](https://console.cloud.google.com/) and create a new project (or select an existing one).
2. Navigate to **APIs & Services > Credentials** 
3. Click **"+ Create Credentials"** and select **"API key"**.
4. Once the key is generated, click on it to configure restrictions:
   - Under **Application restrictions**, select **"Websites"**.
   - Under **Website restrictions**, add the following URLs:
     ```
     http://127.0.0.1:8000/*
     http://iDorm.test
     ```
   - Click **Save**.
5. Copy the API key and paste it into your `.env` file:
```env
GOOGLE_MAPS_API_KEY="your_api_key_here"
```

### 6. Access the Application (Laravel Herd)
Since you are using Laravel Herd, there is no need to run `php artisan serve`. Simply open your web browser and navigate directly to:
**[http://idorm.test](http://idorm.test)**

You can log in using the credentials generated by the seeders. **The default password for all accounts is `password`**.

Here are the available 4-Digit Card ID ranges for each role:
- **🪪 Manager / Superadmin**: `0001`
- **🧑‍💼 Admin**: `1001` - `1010`
- **🎓 Resident / Penghuni**: `3001` - `3100`

## 🌐 Live Deployment

iDorm is deployed and accessible online at:

**🔗 [https://idorm.site](https://idorm.site)**

## ⚖️ License

This project is licensed under the [MIT License](LICENSE).

---
🌟 *iDorm - Simplifying Dormitory Life*