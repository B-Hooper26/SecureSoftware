-- Create database
CREATE DATABASE IF NOT EXISTS securedatabase;
USE securedatabase;

-- Create users table with security constraints
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    dob DATE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (username <> ''),
    CHECK (fullname <> ''),
    CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$'),
    CHECK (CHAR_LENGTH(password) >= 8),
    CHECK (phone REGEXP '^[0-9\\-\\+]{9,15}$'),
    CHECK (address <> '')
);

DELIMITER $$
CREATE TRIGGER dob_no_future BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.dob > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Date of birth cannot be in the future';
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER dob_no_future_update BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.dob > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Date of birth cannot be in the future';
    END IF;
END$$
DELIMITER ; 