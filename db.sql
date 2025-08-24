-- Create database
CREATE DATABASE ASMS;

-- Select database
USE ASMS;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','teacher','staff') DEFAULT 'student',
    profile TEXT,
    status ENUM('active','inactive') DEFAULT 'inactive',
    confirmed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

---------jjjj------------
-- Create Database
CREATE DATABASE IF NOT EXISTS ASMS;
USE ASMS;

-- ===============================
-- Users Table
-- ===============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('student','teacher','staff','admin') NOT NULL,
    profile VARCHAR(255) DEFAULT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    confirmed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===============================
-- Classes Table
-- ===============================
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===============================
-- Teachers Table
-- ===============================
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    class_id INT DEFAULT NULL,
    specialization VARCHAR(100),
    hire_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

-- ===============================
-- Students Table (with full details)
-- ===============================
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,              -- link to users table (account)
    class_id INT DEFAULT NULL,         -- link to class
    teacher_id INT DEFAULT NULL,       -- link to teacher/advisor
    
    roll_no VARCHAR(50) UNIQUE,
    dob DATE,
    gender ENUM('male','female','other'),
    
    profile VARCHAR(255) DEFAULT NULL, -- student photo
    
    address TEXT,
    admission_date DATE,
    
    -- Parents & Guardian Info
    father_name VARCHAR(100),
    father_mobile VARCHAR(20),
    
    mother_name VARCHAR(100),
    mother_mobile VARCHAR(20),
    
    guardian_name VARCHAR(100),
    guardian_mobile VARCHAR(20),
    guardian_relation VARCHAR(50),     -- e.g. uncle, aunt, grandparent
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
);


-- Subjects table
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    class_id INT NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    description TEXT,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Pivot table for multiple teachers
CREATE TABLE subject_teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);
