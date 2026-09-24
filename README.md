# Ananta Multi Trade Private Limited - Local Development & Operations Guide

This repository contains the web application code and database migration system for **anantamtptl.com**.

---

## 🚀 Quick Reference Commands

### 1. Local Database Management (MySQL via Homebrew)

#### Start MySQL Server:
```bash
brew services start mysql
```

#### Stop MySQL Server:
```bash
brew services stop mysql
```

#### Restart MySQL Server:
```bash
brew services restart mysql
```

#### Connect to Local MySQL CLI:
```bash
mysql -u root
```

---

### 2. Local Database & Migrations Setup

#### Create Local Database:
```sql
CREATE DATABASE u914531711_anantamtptl CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Import Local Database Dump:
```bash
mysql -u root u914531711_anantamtptl < ../u914531711_test.20260917132219.sql
```

#### Check Migration Status:
```bash
php database/migration.php status
```

#### Run Database Migrations:
```bash
php database/migration.php migrate
```

---

### 3. Local PHP Server & Frontend Testing

#### Start Local Development Web Server:
Run this command inside the `public_html/` folder to start the website locally:
```bash
php -S localhost:8000
```
Then open [http://localhost:8000](http://localhost:8000) in your browser.

#### Stop Local Web Server:
Press `Ctrl + C` in your terminal window running the server.

---
php -r "echo password_hash('Ananta@1290', PASSWORD_DEFAULT);"

### 4. Git Deployment Commands

#### Commit & Push Code Updates to GitHub:
```bash
git add .
git commit -m "Remaining of admin role ui and function fixes"
git push origin main
```
*(Hostinger automatically deploys updates pushed to the `main` branch, and GitHub Actions automatically runs pending database migrations safely).*

---

## ⚖️ Compliance & Legal Review Note

> [!IMPORTANT]
> If this system is used for real-money investment/returns or referral-based income, applicable Indian financial, tax, KYC/AML, direct-selling/MLM and securities laws should be reviewed with qualified legal/compliance professionals before launch.

