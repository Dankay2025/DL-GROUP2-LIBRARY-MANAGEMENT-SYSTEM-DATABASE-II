-- Run once on an existing library_db database before deploying the registration change.
ALTER TABLE members
    ADD COLUMN student_id VARCHAR(7) DEFAULT NULL AFTER member_number,
    ADD UNIQUE KEY uq_members_student_id (student_id);
