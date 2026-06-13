-- Migration: complete admin panel setup
-- This migration file documents the database structure needed for the admin panel

-- The users table must have the following structure:
-- id (int, primary key, auto_increment)
-- login (varchar, unique)
-- password (varchar, should store hashed passwords)
-- role_id (int, foreign key to roles table)

-- The roles table should be created with:
-- id (int, primary key, auto_increment)
-- name (varchar, unique)
-- description (varchar, optional)

-- Example of what the tables should look like:
/*
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL UNIQUE,
  `description` varchar(255) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`name`, `description`) VALUES
('superadmin', 'Full access, can CRUD everything'),
('reception', 'Can create repairs, clients, contacts, devices and set reception statuses'),
('master', 'Repair master: update diagnostic and execution statuses');

ALTER TABLE `users` 
  ADD COLUMN IF NOT EXISTS `role_id` int(11) DEFAULT NULL;

ALTER TABLE `users` 
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
*/

-- Admin Panel Features:
-- 1. Users Management - superadmin only
--    - Create new users with role assignment
--    - Edit user login and role
--    - Delete users
--    - View all users with their roles

-- 2. Clients Management - superadmin
--    - Create new clients
--    - Edit client information
--    - Delete clients
--    - View all clients

-- 3. Contacts Management - superadmin
--    - Create new contacts
--    - Edit contact information
--    - Delete contacts
--    - View all contacts

-- 4. Devices Management - superadmin
--    - Create new devices with descriptions
--    - Edit device information
--    - Delete devices
--    - View all devices

-- 5. Repairs Management - superadmin
--    - View all repairs
--    - Delete repairs (if needed)
--    - Access repair editing through the repairs view

-- Access Control:
-- - Admin panel (/app/views/admin.php) is accessible only to superadmin users
-- - All CRUD operations require superadmin role
-- - Menu item "Адміністрація" is only shown to superadmin users
