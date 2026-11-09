-- ============================================================
-- CAVENDISH UNIVERSITY UGANDA
-- DATABASE II CAT2 | DL GROUP TWO
-- LIBRARY MANAGEMENT SYSTEM
-- FINAL DATABASE CREATION AND SAMPLE DATA SCRIPT
--
-- Database: library_db
-- Engine: InnoDB
-- Character set: utf8mb4
-- Collation: utf8mb4_general_ci
--
-- IMPORTANT:
-- 1. In phpMyAdmin, first create an EMPTY database named library_db
--    with collation utf8mb4_general_ci.
-- 2. Select library_db, open Import, choose this file and click Go.
-- 3. This script does NOT drop or erase an existing database.
-- 4. If tables already exist, import into a fresh empty database.
--
-- Demo login accounts:
-- username: admin       password: password
-- username: librarian1  password: password
-- Change these demo credentials before any deployment beyond localhost.
-- ============================================================

CREATE DATABASE IF NOT EXISTS library_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE library_db;

-- ------------------------------------------------------------
-- TABLE 1: AUTHORS
-- ------------------------------------------------------------
CREATE TABLE authors (
    author_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    author_name VARCHAR(150) NOT NULL,
    country VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (author_id),
    UNIQUE KEY uq_authors_name_country (author_name, country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- TABLE 2: CATEGORIES
-- ------------------------------------------------------------
CREATE TABLE categories (
    category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (category_id),
    UNIQUE KEY uq_categories_name (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- TABLE 3: PUBLISHERS
-- ------------------------------------------------------------
CREATE TABLE publishers (
    publisher_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    publisher_name VARCHAR(150) NOT NULL,
    country VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (publisher_id),
    UNIQUE KEY uq_publishers_name (publisher_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- TABLE 4: BOOKS
-- ------------------------------------------------------------
CREATE TABLE books (
    book_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    isbn VARCHAR(20) NOT NULL,
    title VARCHAR(200) NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    publisher_id INT UNSIGNED DEFAULT NULL,
    year_published SMALLINT UNSIGNED DEFAULT NULL,
    total_copies INT UNSIGNED NOT NULL DEFAULT 1,
    available_copies INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (book_id),
    UNIQUE KEY uq_books_isbn (isbn),
    KEY idx_books_title (title),
    KEY idx_books_category (category_id),
    CONSTRAINT chk_books_available_nonnegative CHECK (available_copies >= 0),
    CONSTRAINT chk_books_available_lte_total CHECK (available_copies <= total_copies),
    CONSTRAINT fk_books_category
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_books_publisher
        FOREIGN KEY (publisher_id) REFERENCES publishers(publisher_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- TABLE 5: MEMBERS
-- ------------------------------------------------------------
CREATE TABLE members (
    member_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_number VARCHAR(30) NOT NULL,
    student_id VARCHAR(7) DEFAULT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    date_joined DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (member_id),
    UNIQUE KEY uq_members_number (member_number),
    UNIQUE KEY uq_members_student_id (student_id),
    UNIQUE KEY uq_members_email (email),
    KEY idx_members_name (full_name),
    KEY idx_members_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- TABLE 6: USERS (LIBRARIAN / ADMIN LOGIN ACCOUNTS)
-- ------------------------------------------------------------
CREATE TABLE users (
    user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(60) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role ENUM('admin', 'librarian') NOT NULL DEFAULT 'librarian',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    UNIQUE KEY uq_users_username (username),
    CONSTRAINT chk_users_active CHECK (is_active IN (0, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- TABLE 7: BOOK_AUTHORS (MANY-TO-MANY JUNCTION TABLE)
-- ------------------------------------------------------------
CREATE TABLE book_authors (
    book_id INT UNSIGNED NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (book_id, author_id),
    KEY idx_book_authors_author (author_id),
    CONSTRAINT fk_book_authors_book
        FOREIGN KEY (book_id) REFERENCES books(book_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_book_authors_author
        FOREIGN KEY (author_id) REFERENCES authors(author_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- TABLE 8: LOANS
-- ------------------------------------------------------------
CREATE TABLE loans (
    loan_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id INT UNSIGNED NOT NULL,
    book_id INT UNSIGNED NOT NULL,
    issued_by INT UNSIGNED NOT NULL,
    date_issued DATE NOT NULL,
    due_date DATE NOT NULL,
    date_returned DATE DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (loan_id),
    KEY idx_loans_member_book_returned (member_id, book_id, date_returned),
    KEY idx_loans_due_returned (due_date, date_returned),
    KEY idx_loans_book (book_id),
    CONSTRAINT chk_loans_due_after_issue CHECK (due_date >= date_issued),
    CONSTRAINT chk_loans_return_after_issue
        CHECK (date_returned IS NULL OR date_returned >= date_issued),
    CONSTRAINT fk_loans_member
        FOREIGN KEY (member_id) REFERENCES members(member_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_loans_book
        FOREIGN KEY (book_id) REFERENCES books(book_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_loans_user
        FOREIGN KEY (issued_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Password hashes generated by PHP password_hash('password', PASSWORD_DEFAULT).
INSERT INTO users (username, password_hash, full_name, role, is_active) VALUES
('admin', '$2y$12$8L//j6Q9IoxGeQmdv38dieDX/ybGcRhBCs7bG81.1hmu7MFQWBDlm', 'System Administrator', 'admin', 1),
('librarian1', '$2y$12$aGL6A4acJXpRZo11U82YteZ.xNRjGJy2amFFoZoshifJ1OTwKp.Ai', 'Demo Librarian', 'librarian', 1);

INSERT INTO authors (author_name, country) VALUES
('Chinua Achebe', 'Nigeria'),
('Ngugi wa Thiong''o', 'Kenya'),
('George Orwell', 'United Kingdom'),
('Jane Austen', 'United Kingdom'),
('Wole Soyinka', 'Nigeria');

INSERT INTO categories (category_name, description) VALUES
('Fiction', 'Novels and imaginative literature'),
('Technology', 'Computing, information technology and digital systems'),
('History', 'Historical studies and accounts'),
('Science', 'Scientific subjects and research'),
('Education', 'Teaching, learning and educational practice');

INSERT INTO publishers (publisher_name, country) VALUES
('Heinemann', 'United Kingdom'),
('East African Educational Publishers', 'Kenya'),
('Penguin Books', 'United Kingdom'),
('Oxford University Press', 'United Kingdom'),
('Longman', 'United Kingdom');

INSERT INTO books
(isbn, title, category_id, publisher_id, year_published, total_copies, available_copies) VALUES
('9780435905255', 'Things Fall Apart', 1, 1, 1958, 5, 4),
('9780141187761', '1984', 1, 3, 1949, 4, 3),
('9780141439518', 'Pride and Prejudice', 1, 3, 1813, 3, 3),
('9789966460001', 'A Grain of Wheat', 1, 2, 1967, 4, 3),
('9780199535675', 'The River Between', 1, 4, 1965, 3, 3),
('9780131103627', 'The C Programming Language', 2, 5, 1988, 3, 3),
('9780132350884', 'Clean Code', 2, 4, 2008, 2, 2),
('9780262033848', 'Introduction to Algorithms', 2, 4, 2009, 2, 1),
('9780199535569', 'The Lion and the Jewel', 1, 4, 1959, 3, 3),
('9780199535560', 'Animal Farm', 1, 3, 1945, 4, 4);

INSERT INTO members
(member_number, full_name, email, phone, status, date_joined) VALUES
('DLGT-001', 'Kayiza Daniel Tusubira', 'kd252304@students.cavendish.ac.ug', NULL, 'active', '2026-01-10'),
('DLGT-002', 'Kiwummulo Victoria Yunia', 'vy244564@students.cavendish.ac.ug', NULL, 'active', '2026-01-12'),
('DLGT-003', 'Baker Mukasa', 'bm244934@students.cavendish.ac.ug', NULL, 'active', '2026-01-15'),
('DLGT-004', 'Kelvin Likwelile', 'kl252759@students.cavendish.ac.ug', NULL, 'active', '2026-02-01'),
('DLGT-005', 'Phillmon Musisi Mugumya', 'pm263074@students.cavendish.ac.ug', NULL, 'active', '2026-02-03'),
('DLGT-006', 'Gloria Nyanzi Nyafwono', 'gn259146@students.cavendish.ac.ug', NULL, 'active', '2026-02-10'),
('DLGT-007', 'Anoreen Muzoora Aine', 'am241966@students.cavendish.ac.ug', NULL, 'active', '2026-02-15'),
('DLGT-008', 'Kayombya Robin Smith', 'kr261198@students.cavendish.ac.ug', NULL, 'active', '2026-03-01'),
('DLGT-009', 'Jacques Iradukunda', 'ji238539@students.cavendish.ac.ug', NULL, 'active', '2026-03-05'),
('CUU-LIB-010', 'Sample Library Member', 'sample.member@example.com', NULL, 'suspended', '2026-03-08');

INSERT INTO book_authors (book_id, author_id) VALUES
(1, 1),
(2, 3),
(3, 4),
(4, 2),
(5, 2),
(9, 5),
(10, 3);

-- Loan dates intentionally provide returned, active and overdue examples.
-- NULL date_returned means the book has not yet been returned.
-- The available_copies values above match these active sample loans.
INSERT INTO loans
(member_id, book_id, issued_by, date_issued, due_date, date_returned) VALUES
(1, 1, 1, '2026-10-01', '2026-10-15', NULL),
(2, 2, 1, '2026-10-25', '2026-11-08', NULL),
(3, 4, 2, '2026-10-20', '2026-11-03', NULL),
(4, 5, 2, '2026-09-01', '2026-09-15', '2026-09-12'),
(5, 6, 1, '2026-08-10', '2026-08-24', '2026-08-20'),
(6, 8, 2, '2026-10-28', '2026-11-11', NULL),
(7, 10, 1, '2026-07-01', '2026-07-15', '2026-07-10'),
(8, 1, 2, '2026-06-01', '2026-06-15', '2026-06-14'),
(9, 2, 1, '2026-05-01', '2026-05-15', '2026-05-12'),
(1, 3, 2, '2026-04-01', '2026-04-15', '2026-04-13');

-- ============================================================
-- END OF SCRIPT
-- ============================================================
