.bail on
PRAGMA foreign_keys = ON;
BEGIN;
CREATE TABLE users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    email TEXT COLLATE NOCASE NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL CHECK (role IN ('Student', 'Administrator')),
    status TEXT NOT NULL DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE students ADD COLUMN user_id INTEGER REFERENCES users(user_id);
CREATE UNIQUE INDEX students_user_id_unique ON students(user_id);
CREATE INDEX tutoring_requests_student_id ON tutoring_requests(student_id);
COMMIT;
