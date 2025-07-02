-- Quick setup script for MySQL database
-- Run this in your MySQL client if you need to create the database manually

-- Create database
CREATE DATABASE IF NOT EXISTS tire_services 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Show created database
SHOW DATABASES LIKE 'tire_services';

-- Instructions for usage:
-- 1. Open MySQL command line: mysql -u root -p
-- 2. Run this script: source create-mysql-db.sql
-- 3. Or copy and paste the CREATE DATABASE command
-- 4. Then run deploy.bat again 