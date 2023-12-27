-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Гру 27 2023 р., 22:49
-- Версія сервера: 10.4.28-MariaDB
-- Версія PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `mr_robot`
--
CREATE DATABASE IF NOT EXISTS `mr_robot` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `mr_robot`;

-- --------------------------------------------------------

--
-- Структура таблиці `clients`
--
-- Створення: Гру 25 2023 р., 23:00
-- Останнє оновлення: Гру 27 2023 р., 15:43
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- ЗВ'ЯЗКИ ТАБЛИЦІ `clients`:
--

--
-- Очистити таблицю перед вставкою `clients`
--

TRUNCATE TABLE `clients`;
--
-- Дамп даних таблиці `clients`
--

INSERT INTO `clients` (`id`, `first_name`, `surname`, `last_name`, `created_at`, `updated_at`) VALUES
(1, 'Денис', 'Мельничук', 'Ігорович', '2023-12-25 23:31:35', '2023-12-25 23:31:35'),
(2, 'Василь', 'Ковальчук', 'Миколайович', '2023-12-27 12:04:09', '2023-12-27 12:04:09'),
(3, 'Ігор', 'Мельничук', 'Васильович', '2023-12-27 17:16:32', '2023-12-27 17:16:32'),
(4, 'Оксана', 'Мельничук', 'Миколаївна', '2023-12-27 17:43:39', '2023-12-27 17:43:39');

-- --------------------------------------------------------

--
-- Структура таблиці `contacts`
--
-- Створення: Гру 25 2023 р., 23:00
-- Останнє оновлення: Гру 27 2023 р., 15:43
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(9) NOT NULL,
  `type_id` int(9) NOT NULL,
  `client_id` int(9) NOT NULL,
  `contact` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- ЗВ'ЯЗКИ ТАБЛИЦІ `contacts`:
--   `type_id`
--       `contact_types` -> `id`
--   `client_id`
--       `clients` -> `id`
--

--
-- Очистити таблицю перед вставкою `contacts`
--

TRUNCATE TABLE `contacts`;
--
-- Дамп даних таблиці `contacts`
--

INSERT INTO `contacts` (`id`, `type_id`, `client_id`, `contact`) VALUES
(1, 1, 1, '0673010362'),
(2, 2, 1, 'denys.vinegod@gmail.com'),
(3, 2, 2, 'fake@mail.domain'),
(4, 3, 2, '@awesome_man'),
(5, 1, 3, '0674987165'),
(6, 1, 4, '0963973631');

-- --------------------------------------------------------

--
-- Структура таблиці `contact_types`
--
-- Створення: Гру 19 2023 р., 17:53
-- Останнє оновлення: Гру 27 2023 р., 15:47
--

DROP TABLE IF EXISTS `contact_types`;
CREATE TABLE `contact_types` (
  `id` int(9) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- ЗВ'ЯЗКИ ТАБЛИЦІ `contact_types`:
--

--
-- Очистити таблицю перед вставкою `contact_types`
--

TRUNCATE TABLE `contact_types`;
--
-- Дамп даних таблиці `contact_types`
--

INSERT INTO `contact_types` (`id`, `name`, `description`) VALUES
(1, 'Мобільний', ''),
(2, 'e-Mail', ''),
(3, 'Telegram', ''),
(4, 'Instagram', ''),
(5, 'Viber', '');

-- --------------------------------------------------------

--
-- Структура таблиці `devices`
--
-- Створення: Гру 25 2023 р., 22:54
-- Останнє оновлення: Гру 27 2023 р., 15:43
--

DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` int(9) NOT NULL,
  `type_id` int(9) NOT NULL,
  `client_id` int(9) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- ЗВ'ЯЗКИ ТАБЛИЦІ `devices`:
--   `client_id`
--       `clients` -> `id`
--   `type_id`
--       `device_types` -> `id`
--

--
-- Очистити таблицю перед вставкою `devices`
--

TRUNCATE TABLE `devices`;
--
-- Дамп даних таблиці `devices`
--

INSERT INTO `devices` (`id`, `type_id`, `client_id`, `description`) VALUES
(1, 2, 1, ''),
(2, 4, 1, ''),
(3, 2, 2, ''),
(4, 3, 3, ''),
(5, 1, 4, '');

-- --------------------------------------------------------

--
-- Структура таблиці `device_types`
--
-- Створення: Гру 19 2023 р., 17:53
-- Останнє оновлення: Гру 27 2023 р., 16:47
--

DROP TABLE IF EXISTS `device_types`;
CREATE TABLE `device_types` (
  `id` int(9) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- ЗВ'ЯЗКИ ТАБЛИЦІ `device_types`:
--

--
-- Очистити таблицю перед вставкою `device_types`
--

TRUNCATE TABLE `device_types`;
--
-- Дамп даних таблиці `device_types`
--

INSERT INTO `device_types` (`id`, `name`) VALUES
(6, 'Монітор'),
(2, 'Ноутбук'),
(3, 'Планшет'),
(4, 'Системний блок'),
(5, 'Телевізор'),
(1, 'Телефон');

-- --------------------------------------------------------

--
-- Структура таблиці `repairs`
--
-- Створення: Гру 27 2023 р., 15:41
-- Останнє оновлення: Гру 27 2023 р., 21:46
--

DROP TABLE IF EXISTS `repairs`;
CREATE TABLE `repairs` (
  `id` int(9) NOT NULL,
  `status_id` int(9) NOT NULL,
  `client_id` int(9) NOT NULL,
  `contact_id` int(9) NOT NULL,
  `device_id` int(9) NOT NULL,
  `description` varchar(500) NOT NULL,
  `price` varchar(500) NOT NULL,
  `manager_id` int(9) NOT NULL,
  `master_conclusion` varchar(1500) NOT NULL,
  `register_date` datetime NOT NULL DEFAULT current_timestamp(),
  `done_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- ЗВ'ЯЗКИ ТАБЛИЦІ `repairs`:
--   `client_id`
--       `clients` -> `id`
--   `device_id`
--       `devices` -> `id`
--   `manager_id`
--       `users` -> `id`
--   `status_id`
--       `statuses` -> `id`
--   `contact_id`
--       `contacts` -> `id`
--

--
-- Очистити таблицю перед вставкою `repairs`
--

TRUNCATE TABLE `repairs`;
--
-- Дамп даних таблиці `repairs`
--

INSERT INTO `repairs` (`id`, `status_id`, `client_id`, `contact_id`, `device_id`, `description`, `price`, `manager_id`, `master_conclusion`, `register_date`, `done_date`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 1, 1, 'Діагностика, профілактика', 'Відсапає', 1, 'Косметичні дефекти корпусу, стан апаратного забезпечення відмінний', '2023-12-26 00:57:22', '2023-12-27 22:43:05', '2023-12-26 00:57:44', '2023-12-27 22:43:05'),
(2, 3, 1, 2, 2, 'Гудить, виключається', '400 грн.', 1, 'Необхідне обслуговування системи охолодження', '2023-12-26 00:59:44', NULL, '2023-12-26 00:59:04', '2023-12-27 22:43:26'),
(3, 1, 2, 3, 3, 'Встановити свіжу ОС', 'Поміняє тормозні колодки у машині.', 1, '', '2023-12-27 14:04:52', NULL, '2023-12-27 14:04:36', '2023-12-27 14:04:36'),
(4, 5, 3, 5, 4, 'Артефакти на дисплеї підчас деформації корпусу. Оцінити витрати на ремонт.', 'Безкоштовно', 1, 'Некро-Asus на чипі nVidia. Денчік не потяне гемор з такою шляпою', '2023-12-27 17:16:54', NULL, '2023-12-27 17:35:04', '2023-12-27 22:46:48'),
(5, 1, 4, 6, 5, 'Не працює вібромотор', 'Денис відсапає', 1, '', '2023-12-27 17:42:37', NULL, '2023-12-27 17:43:40', '2023-12-27 22:22:25');

-- --------------------------------------------------------

--
-- Структура таблиці `statuses`
--
-- Створення: Гру 27 2023 р., 13:12
-- Останнє оновлення: Гру 27 2023 р., 21:45
--

DROP TABLE IF EXISTS `statuses`;
CREATE TABLE `statuses` (
  `id` int(9) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- ЗВ'ЯЗКИ ТАБЛИЦІ `statuses`:
--

--
-- Очистити таблицю перед вставкою `statuses`
--

TRUNCATE TABLE `statuses`;
--
-- Дамп даних таблиці `statuses`
--

INSERT INTO `statuses` (`id`, `name`, `description`) VALUES
(1, 'Нове замовлення', ''),
(2, 'Діагностика', ''),
(3, 'Виконано', ''),
(4, 'Видано', ''),
(5, 'Видано без ремонту', '');

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--
-- Створення: Гру 17 2023 р., 17:19
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(9) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- ЗВ'ЯЗКИ ТАБЛИЦІ `users`:
--

--
-- Очистити таблицю перед вставкою `users`
--

TRUNCATE TABLE `users`;
--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id`, `login`, `password`) VALUES
(1, 'vinegod', '$2y$10$wyKUn3.NLDod1VStW9z4KOb35/wMNGYP6SDGDNkn2i6lMsya5bQ0i');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`,`client_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Індекси таблиці `contact_types`
--
ALTER TABLE `contact_types`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`,`client_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Індекси таблиці `device_types`
--
ALTER TABLE `device_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Індекси таблиці `repairs`
--
ALTER TABLE `repairs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`,`device_id`,`manager_id`),
  ADD KEY `device_id` (`device_id`),
  ADD KEY `manager_id` (`manager_id`),
  ADD KEY `repairs_ibfk_4` (`status_id`),
  ADD KEY `repairs_ibfk_5` (`contact_id`);

--
-- Індекси таблиці `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблиці `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблиці `contact_types`
--
ALTER TABLE `contact_types`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблиці `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблиці `device_types`
--
ALTER TABLE `device_types`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблиці `repairs`
--
ALTER TABLE `repairs`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблиці `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `contact_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `contacts_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `devices_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `device_types` (`id`) ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `repairs`
--
ALTER TABLE `repairs`
  ADD CONSTRAINT `repairs_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `repairs_ibfk_2` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `repairs_ibfk_3` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `repairs_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `repairs_ibfk_5` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
