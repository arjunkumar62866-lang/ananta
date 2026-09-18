# Database Architecture & Migration Documentation

Website: **anantamtptl.com**  
Database Engine: MySQL / MariaDB (PDO)

---

## 1. Existing Database Architecture & Table Inventory

The baseline database for this application consists of **73 tables**, managed via PDO MySQL queries in custom PHP.

### Core Tables:
- **User & Account Management:** `user`, `user1`, `admin`, `kyc`, `tree`, `tbl_bank`, `tbl_beneficiary_acount`
- **Transactions & Wallets:** `tbl_transaction`, `tbl_payment`, `tbl_deposit_withdrawal_history`, `tbl_coin`, `tbl_cart`, `tbl_order`
- **Income & Rewards:** `tbl_income`, `tbl_income2`, `tbl_income3`, `tbl_directinc`, `tbl_levelinc`, `tbl_daily_levelinc`, `tbl_roiinc`, `tbl_rewardinc`, `tbl_royalty_user`
- **Pins & Packages:** `pin_generate_detail`, `pin_list`, `pin_transfer`, `tbl_package`
- **Content & Support:** `tbl_banner`, `tbl_news`, `gallery`, `tbl_query`

---

## 2. Production Safety Rules

> [!CAUTION]
> **ABSOLUTE RULE:** Never run `DROP DATABASE`, `DROP TABLE`, `TRUNCATE`, or unapproved `DELETE` statements on Hostinger production.

1. **Hostinger Database Preservation:** The Hostinger production database contains live user accounts, wallet transactions, and KYC records. It MUST NOT be overwritten or reset.
2. **No Automatic Web Execution:** Migration scripts MUST NOT be included or called from `index.php`, `header.php`, or any page load route. Migrations are executed via CLI commands only.
3. **No Secrets in Git:** Database connection files (`common/connection.php`) are strictly excluded via `.gitignore`.

---

## 3. Database Migration Architecture

The migration runner tracks executed SQL scripts using a dedicated MySQL table: `schema_migrations`.

```text
database/
├── migrations/
│   ├── 0001_baseline.sql        (Baseline adoption marker)
│   └── README.md                (Migration guidelines)
├── migration.php                (CLI migration runner)
└── README.md                    (This documentation file)
```

### How Baseline Adoption Works:
- `0001_baseline.sql` acts as an initial baseline marker and **contains no DDL/DROP statements**.
- Running `php database/migration.php migrate` marks `0001_baseline.sql` as applied without modifying any existing production tables or customer data.

---

## 4. Local Development Database Setup

1. **Create Local Database:**
   ```sql
   CREATE DATABASE ananta_local_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. **Import Local Baseline Data:**
   Import the reference backup dump file `u914531711_test.20260917132219.sql` into `ananta_local_db`.
3. **Configure Local Connection:**
   Copy `common/connection.example.php` to `common/connection.php` and set your local MySQL credentials:
   ```php
   $host = "localhost";
   $dbname = "ananta_local_db";
   $username = "root";
   $password = "your_local_password";
   ```
4. **Initialize Migration Tracking:**
   Run CLI status to verify:
   ```bash
   php database/migration.php status
   ```

---

## 5. Running Database Migrations

From your terminal inside `public_html/`:

### Check Status:
```bash
php database/migration.php status
```

### Apply Pending Migrations:
```bash
php database/migration.php migrate
```

---

## 6. Hostinger Deployment Procedure

1. Code updates (PHP/HTML/JS) and safe SQL migration files are pushed to GitHub `main` branch.
2. Hostinger pulls the latest code to `public_html/`.
3. If database migrations are required, connect via SSH to Hostinger and run:
   ```bash
   php database/migration.php migrate
   ```
4. Or perform the structural DDL changes safely via Hostinger phpMyAdmin using the exact SQL statements from the new `database/migrations/*.sql` file.
