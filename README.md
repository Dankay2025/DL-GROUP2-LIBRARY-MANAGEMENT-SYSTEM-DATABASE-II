# Cavendish University Uganda – Library Management System

**Database II CAT 2 | DL Group Two**

## Requirements
- Windows with XAMPP (Apache, MySQL/MariaDB and PHP 8.1 or later)
- Web browser and VS Code (recommended)

## Installation
1. Extract the `library-system` folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/`.
4. Create an empty database named `library_db` (collation `utf8mb4_general_ci`).
5. Select `library_db` → **Import** → choose `database/library_db.sql` → **Go**. This script creates the eight tables and sample data. Do not import it over populated tables.
6. Open `http://localhost/library-system/DL-GROUP2-LIBRARY-MANAGEMENT-SYSTEM-DATABASE-II/`.

XAMPP defaults are configured in `config/database.php`: host `localhost`, database `library_db`, user `root`, blank password. If your MySQL root account has a password, update `$dbPass` there. The project is expected at `C:\xampp\htdocs\library-system\DL-GROUP2-LIBRARY-MANAGEMENT-SYSTEM-DATABASE-II`; if you move it, update `APP_URL` in `config/database.php`.

## Demonstration login
- Username: `admin`
- Password: `password`

Change demonstration passwords before any use beyond a local classroom demonstration. The public member registration form creates library membership records; it does not create staff login accounts. Staff login accounts are maintained separately in the `users` table.

## Main features
- Staff login/logout with PHP sessions and password hashing.
- Public library membership registration.
- Dashboard counts and recent loan activity.
- Books: list, search, create, edit and delete (database constraints protect referenced books).
- Members: list, search, create, edit and delete (loan history is protected).
- Authors and categories: list, create, edit and delete.
- Loans: issue and return books; due date is 14 days after issue.
- Prepared statements, server-side validation, CSRF tokens and escaped output.
- Transactional issue/return operations; prevents issuing unavailable books, duplicate concurrent loans and borrowing by suspended members.

## Database deliverables
- `database/library_db.sql` – schema, constraints and sample records.
- `database/queries.sql` – JOIN and aggregate query examples.
- `database/erd.png` – database relationship diagram.

## Project structure
`auth/` authentication and membership registration; `books/`, `members/`, `loans/`, `authors/`, `categories/` application modules; `config/` database connection; `includes/` shared layout/security helpers; `assets/` CSS, JavaScript and university/library images; `database/` SQL deliverables.

## Notes
- The supplied sample dataset contains nine DL Group Two members and one demonstration member, with loan examples linked to group members.
- Phone numbers are left blank where verified numbers were not supplied.
- The application is intended for local XAMPP demonstration. Do not expose the development server directly to the public internet.
- `queries.sql` is a query demonstration file; it is not required to be imported to run the PHP application.
