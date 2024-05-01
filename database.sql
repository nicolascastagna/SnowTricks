DROP SCHEMA IF EXISTS SnowTricks;
CREATE DATABASE IF NOT EXISTS `SnowTricks` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE SnowTricks;

DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `trick`;
DROP TABLE IF EXISTS `comment`;
DROP TABLE IF EXISTS `category`;
DROP TABLE IF EXISTS `picture`;
DROP TABLE IF EXISTS `video`;
DROP TABLE IF EXISTS `migration_versions`;

CREATE TABLE `user` (  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `token` VARCHAR(255) UNIQUE NULL,
    `role` ENUM('ROLE_USER', 'ROLE_ADMIN') NOT NULL
);
CREATE TABLE `category` (  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL
);

CREATE TABLE `trick` (  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_id` SMALLINT NOT NULL,
    `category_id` SMALLINT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `creation_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `update_date` DATETIME DEFAULT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON DELETE CASCADE
);

CREATE TABLE `picture` (  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `trick_id` SMALLINT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`trick_id`) REFERENCES `trick`(`id`) ON DELETE CASCADE
);

CREATE TABLE `video` (  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `trick_id` SMALLINT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`trick_id`) REFERENCES `trick`(`id`) ON DELETE CASCADE
);

CREATE TABLE `comment` (  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_id` SMALLINT NOT NULL,
    `trick_id` SMALLINT NOT NULL,
    `content` TEXT NOT NULL,
    `comment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`trick_id`) REFERENCES `trick`(`id`) ON DELETE CASCADE
);

CREATE TABLE `migration_versions` (
    `version` VARCHAR(191) NOT NULL PRIMARY KEY,
    `executed_at` DATETIME NOT NULL
);

INSERT INTO `user` (`username`, `password`, `email`, `token`, `role`) 
VALUES ('Admin', MD5('5f66a2f90e8b79adfc10d94e3923d7ab'), 'admin@nicolascastagna.com', NULL, 'ROLE_ADMIN'),
('User', MD5('5f66a2f90e8b79adfc10d94e3923d7ab'), 'user@nicolascastagna.com', NULL, 'ROLE_USER'),
('JohnDoe', MD5('51ab6c0f84d431e28f5a2f94d0b5f2d8'), 'john.doe@hotmail.com', NULL, 'ROLE_USER'),
('JeanDupont', MD5('3fbfa916d5c6c6140b87d33c8930dfae'), 'jeandupon@hotmail.com', NULL, 'ROLE_USER')
;

INSERT INTO `category` (`name`) VALUES
    ('Grabs'),
    ('Spins'),
    ('Flips'),
    ('Slides'),
    ('Straight Airs'),
    ('Tweaks et variations'),
    ('Stalls'),
    ('Inverted hand plants'),
    ('Autres'),
    ('Freestyle');

INSERT INTO `trick` (`user_id`, `category_id`, `name`, `description`, `creation_date`)
VALUES
    (1, 1, 'Mute Grab', 'Description du Mute Grab', CURRENT_TIMESTAMP),
    (1, 2, '720', 'Description du 720', CURRENT_TIMESTAMP),
    (1, 3, 'Backflip', 'Description du Backflip', CURRENT_TIMESTAMP),
    (1, 4, 'Boardslide', 'Description du Boardslide', CURRENT_TIMESTAMP),
    (1, 5, 'Indy', 'Description du Indy', CURRENT_TIMESTAMP),
    (1, 6, 'Butter 180', 'Description du Butter 180', CURRENT_TIMESTAMP),
    (1, 7, 'Method', 'Description du Method', CURRENT_TIMESTAMP),
    (1, 8, 'Switch Stance', 'Description du Switch Stance', CURRENT_TIMESTAMP),
    (1, 9, 'Nosepress', 'Description du Nosepress', CURRENT_TIMESTAMP),
    (1, 10, 'Ollie', 'Description du Ollie', CURRENT_TIMESTAMP)
;

INSERT INTO `picture` (`trick_id`, `name`) VALUES
    (1, 'image1.jpg'),
    (1, 'image2.jpg'),
    (2, 'image3.jpg'),
    (3, 'image4.jpg'),
    (4, 'image5.jpg'),
    (5, 'image6.jpg'),
    (6, 'image7.jpg'),
    (7, 'image8.jpg'),
    (8, 'image9.jpg'),
    (9, 'image10.jpg'),
    (10, 'image11.jpg');

INSERT INTO `video` (`trick_id`, `name`) VALUES
    (1, 'url/video'),
    (2, 'url/video2');

INSERT INTO `comment` (`user_id`, `trick_id`, `content`, `comment_date`) 
VALUES 
    (1, 1, 'Excellente figure !', '2024-04-19 17:34:19'),
    (1, 2, 'Très intéressant !', '2024-04-19 19:34:10'),
    (2, 4, 'Pas mal !', '2024-04-19 21:14:00'),
    (3, 5, 'Joli !', '2024-04-19 11:34:19'),
    (4, 6, 'J''adore !', '2024-04-21 19:34:10'),
    (3, 8, 'Pas mal !', '2024-04-20 21:14:00');
