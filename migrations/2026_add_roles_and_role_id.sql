-- Migration: add roles table and role_id to users
-- Run this SQL against your `mr_robot` database to add roles support.

START TRANSACTION;

-- Create roles table
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL UNIQUE,
  `description` varchar(255) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert predefined roles
INSERT INTO `roles` (`name`, `description`) VALUES
('superadmin', 'Full access, can CRUD everything'),
('reception', 'Can create repairs, clients, contacts, devices and set reception statuses'),
('master', 'Repair master: update diagnostic and execution statuses');

-- Add role_id column to users table if not exists
ALTER TABLE `users` 
  ADD COLUMN IF NOT EXISTS `role_id` int(11) DEFAULT NULL;

-- Add foreign key constraint if possible (silently ignore errors if fails)
-- Note: some MySQL versions do not support IF NOT EXISTS for FK; run manually if needed.
ALTER TABLE `users` 
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Optionally set existing first user as superadmin (uncomment if you have an admin user with id=1)
-- UPDATE `users` SET `role_id` = (SELECT `id` FROM `roles` WHERE `name`='superadmin' LIMIT 1) WHERE `id` = 1;

COMMIT;
