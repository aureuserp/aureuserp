CREATE DATABASE IF NOT EXISTS `aureus`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'aureus'@'127.0.0.1'
    IDENTIFIED WITH caching_sha2_password BY 'aureus';

CREATE USER IF NOT EXISTS 'aureus'@'localhost'
    IDENTIFIED WITH caching_sha2_password BY 'aureus';

GRANT ALL PRIVILEGES ON `aureus`.* TO 'aureus'@'127.0.0.1';
GRANT ALL PRIVILEGES ON `aureus`.* TO 'aureus'@'localhost';

FLUSH PRIVILEGES;
