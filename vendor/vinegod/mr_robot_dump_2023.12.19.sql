-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Гру 19 2023 р., 16:34
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
-- Очистити таблицю перед вставкою `clients`
--

TRUNCATE TABLE `clients`;
-- --------------------------------------------------------

--
-- Структура таблиці `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(9) NOT NULL,
  `type_id` int(9) NOT NULL,
  `client_id` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Очистити таблицю перед вставкою `contacts`
--

TRUNCATE TABLE `contacts`;
-- --------------------------------------------------------

--
-- Структура таблиці `contact_types`
--

DROP TABLE IF EXISTS `contact_types`;
CREATE TABLE `contact_types` (
  `id` int(9) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Очистити таблицю перед вставкою `contact_types`
--

TRUNCATE TABLE `contact_types`;
-- --------------------------------------------------------

--
-- Структура таблиці `devices`
--

DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` int(9) NOT NULL,
  `type_id` int(9) NOT NULL,
  `client_id` int(9) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Очистити таблицю перед вставкою `devices`
--

TRUNCATE TABLE `devices`;
-- --------------------------------------------------------

--
-- Структура таблиці `device_types`
--

DROP TABLE IF EXISTS `device_types`;
CREATE TABLE `device_types` (
  `id` int(9) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Очистити таблицю перед вставкою `device_types`
--

TRUNCATE TABLE `device_types`;
--
-- Дамп даних таблиці `device_types`
--

INSERT INTO `device_types` (`id`, `name`) VALUES
(2, 'Ноутбук'),
(1, 'Телефон');

-- --------------------------------------------------------

--
-- Структура таблиці `repairs`
--

DROP TABLE IF EXISTS `repairs`;
CREATE TABLE `repairs` (
  `id` int(9) NOT NULL,
  `client_id` int(9) NOT NULL,
  `device_id` int(9) NOT NULL,
  `description` varchar(500) NOT NULL,
  `manager_id` int(9) NOT NULL,
  `register_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Очистити таблицю перед вставкою `repairs`
--

TRUNCATE TABLE `repairs`;
-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(9) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  ADD KEY `manager_id` (`manager_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `contact_types`
--
ALTER TABLE `contact_types`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `device_types`
--
ALTER TABLE `device_types`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблиці `repairs`
--
ALTER TABLE `repairs`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `contact_types` (`id`),
  ADD CONSTRAINT `contacts_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `devices_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `device_types` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `repairs`
--
ALTER TABLE `repairs`
  ADD CONSTRAINT `repairs_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `repairs_ibfk_2` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`),
  ADD CONSTRAINT `repairs_ibfk_3` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
