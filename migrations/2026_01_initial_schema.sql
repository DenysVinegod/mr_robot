-- Migration: complete initial database schema for mr_robot
-- Creates database, all tables, indexes, and foreign keys for clean deployment
-- Includes device attributes columns from the start

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `mr_robot` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `mr_robot`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `repairs`;
DROP TABLE IF EXISTS `device_types`;
DROP TABLE IF EXISTS `devices`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `contact_types`;
DROP TABLE IF EXISTS `statuses`;
DROP TABLE IF EXISTS `clients`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

SET FOREIGN_KEY_CHECKS = 1;

-- Roles table: application role definitions
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL UNIQUE,
  `description` varchar(255) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Users table: system users with authentication
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL UNIQUE,
  `password` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Clients table: customer information
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Contact types: defines types of contact information
CREATE TABLE IF NOT EXISTS `contact_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Contacts table: client contact information
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `contact` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`,`client_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Device types: categories of devices
CREATE TABLE IF NOT EXISTS `device_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Devices table: customer devices with extended attributes
CREATE TABLE IF NOT EXISTS `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `color` varchar(100) DEFAULT '',
  `cosmetic_condition` varchar(255) DEFAULT '',
  `serial_number` varchar(100) DEFAULT '',
  `equipment` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`,`client_id`),
  KEY `client_id` (`client_id`),
  KEY `idx_serial_number` (`serial_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Statuses table: repair status definitions
CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL UNIQUE,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Repairs table: repair orders and work history
CREATE TABLE IF NOT EXISTS `repairs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `price` varchar(500) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `master_conclusion` varchar(1500) NOT NULL,
  `register_date` datetime NOT NULL DEFAULT current_timestamp(),
  `done_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`,`device_id`,`manager_id`),
  KEY `device_id` (`device_id`),
  KEY `manager_id` (`manager_id`),
  KEY `status_id` (`status_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Foreign key constraints
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `contacts`
  ADD CONSTRAINT `fk_contacts_type` FOREIGN KEY (`type_id`) REFERENCES `contact_types`(`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contacts_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON UPDATE CASCADE;

ALTER TABLE `devices`
  ADD CONSTRAINT `fk_devices_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devices_type` FOREIGN KEY (`type_id`) REFERENCES `device_types`(`id`) ON UPDATE CASCADE;

ALTER TABLE `repairs`
  ADD CONSTRAINT `fk_repairs_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repairs_device` FOREIGN KEY (`device_id`) REFERENCES `devices`(`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repairs_manager` FOREIGN KEY (`manager_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repairs_status` FOREIGN KEY (`status_id`) REFERENCES `statuses`(`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repairs_contact` FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON UPDATE CASCADE;
