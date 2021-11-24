-- ShopPhp - init schema

USE ShopDB;

CREATE TABLE `app_admin` (
    `user_name`       VARCHAR(128) PRIMARY KEY,
    `password_hash`   VARCHAR(256) NOT NULL
);

CREATE TABLE `page` (
    `path` VARCHAR(64) PRIMARY KEY,
    `data` TEXT NOT NULL,
    `mime_type` VARCHAR(256)
);

CREATE TABLE `image` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(256),
    `data` LONGBLOB NOT NULL
);

CREATE TABLE `profile` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(256)
);

CREATE TABLE `fittings` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(256)
);

CREATE TABLE `category` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(256)
);

CREATE TABLE `window` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(256),
    `profile_id` INT,
    `num_of_cam` INT,
    `glass_unit` VARCHAR(256),
    `fittings_id` INT,
    `colour` VARCHAR(256),
    `price` INT,
    `category_id` INT,
    

    FOREIGN KEY `window_FK_profile` (profile_id) REFERENCES `profile`(id),
    FOREIGN KEY `window_FK_fittings` (fittings_id) REFERENCES `fittings`(id),
    FOREIGN KEY `window_FK_category` (category_id) REFERENCES `category`(id)
);

CREATE TABLE `window_image` (
    `image_id` INTEGER NOT NULL,
    `window_id` INTEGER NOT NULL PRIMARY KEY,

    FOREIGN KEY `window_image_FK_image` (image_id) REFERENCES `image`(id),
    FOREIGN KEY `window_image_FK_window` (window_id) REFERENCES `window`(id) ON DELETE CASCADE
);