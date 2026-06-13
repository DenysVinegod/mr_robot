-- Migration: seed initial demo data for mr_robot
-- Provides a clean, generic dataset for deployment and testing.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

USE `mr_robot`;

SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM `repairs`;
DELETE FROM `devices`;
DELETE FROM `contacts`;
DELETE FROM `clients`;
DELETE FROM `statuses`;
DELETE FROM `device_types`;
DELETE FROM `contact_types`;
DELETE FROM `users`;
DELETE FROM `roles`;

-- Reset AUTO_INCREMENT counters for a clean seed
ALTER TABLE `repairs` AUTO_INCREMENT = 1;
ALTER TABLE `devices` AUTO_INCREMENT = 1;
ALTER TABLE `contacts` AUTO_INCREMENT = 1;
ALTER TABLE `clients` AUTO_INCREMENT = 1;
ALTER TABLE `statuses` AUTO_INCREMENT = 1;
ALTER TABLE `device_types` AUTO_INCREMENT = 1;
ALTER TABLE `contact_types` AUTO_INCREMENT = 1;
ALTER TABLE `users` AUTO_INCREMENT = 1;
ALTER TABLE `roles` AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;

START TRANSACTION;

INSERT INTO `roles` (`name`, `description`) VALUES
  ('superadmin', 'Full access, can manage all application data');

INSERT INTO `users` (`login`, `password`, `role_id`) VALUES
  ('admin', '$2y$10$MU.lgByKx9k1P4dF2LjQauv7n9jUtk3gj/72SmFklZ2iWF4cn/EHC', 1);

INSERT INTO `statuses` (`name`, `description`) VALUES
  ('Нове замовлення', 'Ремонт тільки зареєстровано'),
  ('Діагностика', 'Проводиться діагностика пристрою'),
  ('Виконано', 'Ремонт завершено'),
  ('Видано', 'Пристрій видано клієнту після ремонту'),
  ('Видано без ремонту', 'Пристрій повернено без ремонту');

INSERT INTO `contact_types` (`name`, `description`) VALUES
  ('Мобільний', 'Мобільний телефон'),
  ('e-Mail', 'Електронна пошта'),
  ('Telegram', 'Профіль Telegram');

INSERT INTO `device_types` (`name`) VALUES
  ('Ноутбук'),
  ('Телефон'),
  ('Планшет');

INSERT INTO `clients` (`first_name`, `surname`, `last_name`, `created_at`, `updated_at`) VALUES
  ('Оксана', 'Данилюк', 'Тарасівна', '2024-06-01 09:00:00', '2024-06-01 09:00:00'),
  ('Іван', 'Петренко', 'Сергійович', '2024-06-02 10:25:00', '2024-06-02 10:25:00'),
  ('Марія', 'Гончар', 'Петрівна', '2024-06-03 08:45:00', '2024-06-03 08:45:00'),
  ('Сергій', 'Коваленко', 'Андрійович', '2024-06-04 14:10:00', '2024-06-04 14:10:00');

INSERT INTO `contacts` (`type_id`, `client_id`, `contact`) VALUES
  (1, 1, '0670000001'),
  (2, 1, 'oksana@example.com'),
  (1, 2, '0670000002'),
  (3, 2, '@ivan.support'),
  (1, 3, '0670000003'),
  (2, 3, 'maria@example.com'),
  (1, 4, '0670000004'),
  (3, 4, '@sergey.fix');

INSERT INTO `devices` (`type_id`, `client_id`, `description`, `color`, `cosmetic_condition`, `serial_number`, `equipment`) VALUES
  (2, 1, 'Ноутбук з повільним завантаженням', 'Чорний', 'Добрий стан, легкі подряпини на кришці', 'LP-123456-A', 'Коробка, ЗУ, батарея, мишка'),
  (1, 2, 'Телефон не тримає заряд', 'Білий', 'Задряпаний, битий корпус ліворуч', 'SM-A515-8804', 'Коробка, ЗУ'),
  (3, 3, 'Планшет гальмує при відкритті додатків', 'Срібний', 'Новий стан, немає дефектів', 'TB-IPD-PRO-2024', 'Коробка, ЗУ, кабель, рукав'),
  (2, 4, 'Ноутбук із заїданням клавіатури', 'Сріблясто-сірий', 'Зношений, видимі подряпини', 'DELL-XPS-987654', 'Коробка, ЗУ'),
  (1, 3, 'Телефон із тріснутим екраном', 'Космічна чорнь', 'Тріснутий екран справа, корпус в порядку', 'SM-G991-5600', 'Без коробки, ЗУ');

INSERT INTO `repairs` (
  `status_id`,
  `client_id`,
  `contact_id`,
  `device_id`,
  `description`,
  `price`,
  `manager_id`,
  `master_conclusion`,
  `register_date`,
  `done_date`,
  `created_at`,
  `updated_at`
) VALUES
  (1, 1, 1, 1, 'Не вмикається після тривалого використання', '600 грн.', 1, '', '2024-06-01 09:30:00', NULL, '2024-06-01 09:30:00', '2024-06-01 09:30:00'),
  (2, 1, 2, 1, 'Потрібно перевірити систему охолодження', '450 грн.', 1, 'Знайдено перегрів, чистка та заміна термопасти рекомендована', '2024-06-01 10:00:00', NULL, '2024-06-01 10:00:00', '2024-06-01 10:00:00'),
  (3, 2, 4, 2, 'Тривалий розряд батареї', '400 грн.', 1, 'Замінено батарею, тест пройдений успішно', '2024-06-02 11:00:00', '2024-06-03 16:20:00', '2024-06-02 11:00:00', '2024-06-03 16:20:00'),
  (4, 3, 5, 5, 'Смартфон отримав тріщину на екрані', '1200 грн.', 1, 'Екран замінено, пристрій працює коректно', '2024-06-03 09:20:00', '2024-06-04 13:05:00', '2024-06-03 09:20:00', '2024-06-04 13:05:00'),
  (2, 4, 8, 4, 'Клавіатура не реагує на натискання кількох клавіш', '550 грн.', 1, 'Очищено механізм, перевірено - клавіші працюють', '2024-06-04 14:30:00', NULL, '2024-06-04 14:30:00', '2024-06-04 14:30:00'),
  (1, 3, 6, 3, 'Планшет повільно запускає програми', '700 грн.', 1, '', '2024-06-05 09:10:00', NULL, '2024-06-05 09:10:00', '2024-06-05 09:10:00'),
  (5, 2, 4, 2, 'Клієнт відмовився від ремонту телефону', '0 грн.', 1, 'Повернено без ремонту за проханням клієнта', '2024-06-05 11:45:00', '2024-06-05 12:30:00', '2024-06-05 11:45:00', '2024-06-05 12:30:00'),
  (3, 4, 8, 4, 'Несправний USB-порт, перевірка виконана', '300 грн.', 1, 'USB-порт замінено, пристрій працює', '2024-06-06 08:55:00', '2024-06-06 15:00:00', '2024-06-06 08:55:00', '2024-06-06 15:00:00'),
  (2, 1, 2, 1, 'Додаткове оновлення BIOS', '250 грн.', 1, 'BIOS оновлено, система стабільна', '2024-06-06 09:30:00', NULL, '2024-06-06 09:30:00', '2024-06-06 09:30:00'),
  (1, 4, 7, 4, 'Потрібно перевірити шум вентилятора', '350 грн.', 1, '', '2024-06-06 10:20:00', NULL, '2024-06-06 10:20:00', '2024-06-06 10:20:00');

COMMIT;
