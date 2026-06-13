-- Migration: add device attributes (color, cosmetic condition, serial number, equipment)
-- Adds extended information to devices table

ALTER TABLE `devices` ADD COLUMN `color` varchar(100) DEFAULT '' AFTER `description`;
ALTER TABLE `devices` ADD COLUMN `cosmetic_condition` varchar(255) DEFAULT '' AFTER `color`;
ALTER TABLE `devices` ADD COLUMN `serial_number` varchar(100) DEFAULT '' AFTER `cosmetic_condition`;
ALTER TABLE `devices` ADD COLUMN `equipment` varchar(500) DEFAULT '' AFTER `serial_number`;

-- Create index for serial number searches
CREATE INDEX `idx_serial_number` ON `devices`(`serial_number`);
