-- CAVENDISH UNIVERSITY UGANDA | DATABASE II CAT2 | DL GROUP TWO
USE library_db;

-- 1. Books currently on loan with member names
SELECT l.loan_id, m.member_number, m.full_name, b.isbn, b.title,
       l.date_issued, l.due_date
FROM loans AS l
JOIN members AS m ON m.member_id = l.member_id
JOIN books AS b ON b.book_id = l.book_id
WHERE l.date_returned IS NULL
ORDER BY l.due_date;

-- 2. Overdue loans
SELECT l.loan_id, m.full_name, b.title, l.due_date,
       DATEDIFF(CURRENT_DATE, l.due_date) AS days_overdue
FROM loans AS l
JOIN members AS m ON m.member_id = l.member_id
JOIN books AS b ON b.book_id = l.book_id
WHERE l.date_returned IS NULL
  AND l.due_date < CURRENT_DATE
ORDER BY l.due_date;

-- 3. Number of loans per category (aggregate report)
SELECT c.category_name, COUNT(l.loan_id) AS total_loans
FROM categories AS c
LEFT JOIN books AS b ON b.category_id = c.category_id
LEFT JOIN loans AS l ON l.book_id = b.book_id
GROUP BY c.category_id, c.category_name
ORDER BY total_loans DESC;

-- 4. Total and available copies by category (additional aggregate report)
SELECT c.category_name,
       SUM(b.total_copies) AS total_copies,
       SUM(b.available_copies) AS available_copies
FROM categories AS c
JOIN books AS b ON b.category_id = c.category_id
GROUP BY c.category_id, c.category_name
ORDER BY c.category_name;
