-- Migration: add missing workflow statuses for reception and master roles
-- Run this SQL against your `mr_robot` database if these statuses are not present.

START TRANSACTION;

INSERT INTO `statuses` (`name`, `description`)
SELECT 'Узгоджено', ''
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `statuses` WHERE `name` = 'Узгоджено');

INSERT INTO `statuses` (`name`, `description`)
SELECT 'Скасовано', ''
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `statuses` WHERE `name` = 'Скасовано');

INSERT INTO `statuses` (`name`, `description`)
SELECT 'Очікує узгодження', ''
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `statuses` WHERE `name` = 'Очікує узгодження');

INSERT INTO `statuses` (`name`, `description`)
SELECT 'Очікує запчастин', ''
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `statuses` WHERE `name` = 'Очікує запчастин');

INSERT INTO `statuses` (`name`, `description`)
SELECT 'Відмовлено', ''
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `statuses` WHERE `name` = 'Відмовлено');

COMMIT;
