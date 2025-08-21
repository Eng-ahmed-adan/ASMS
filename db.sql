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
