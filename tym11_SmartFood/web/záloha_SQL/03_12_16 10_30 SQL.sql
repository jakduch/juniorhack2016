-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Sob 03. pro 2016, 10:29
-- Verze serveru: 10.1.13-MariaDB
-- Verze PHP: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `hackathon`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `automats`
--

CREATE TABLE `automats` (
  `id` int(11) NOT NULL,
  `name` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `locality` varchar(30) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `automats`
--

INSERT INTO `automats` (`id`, `name`, `locality`) VALUES
(1, 'Automat A3', 'Sokolovská 149, Plzeň'),
(2, 'Automat A1', 'Jáchymova 29, Ostrava'),
(3, 'Automat A1', 'Dobříšská 38, Brno');

-- --------------------------------------------------------

--
-- Struktura tabulky `credit_log`
--

CREATE TABLE `credit_log` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `description` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `total` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `credit_log`
--

INSERT INTO `credit_log` (`id`, `date`, `description`, `total`, `user_id`) VALUES
(24, '2016-12-03 10:15:08', 'Kredity odečteny (-0)', 49294, 1),
(25, '2016-12-03 10:16:17', 'Kredity odečteny (-255)', 49039, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `groups`
--

INSERT INTO `groups` (`id`, `name`) VALUES
(1, 'Uživatel'),
(13, 'Administrátor');

-- --------------------------------------------------------

--
-- Struktura tabulky `item_storage`
--

CREATE TABLE `item_storage` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `automat_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `item_storage`
--

INSERT INTO `item_storage` (`id`, `product_id`, `automat_id`, `amount`) VALUES
(7, 4, 1, 70),
(8, 4, 1, 70),
(9, 4, 1, 70),
(10, 4, 1, 70);

-- --------------------------------------------------------

--
-- Struktura tabulky `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `order_number` tinyint(3) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `automat_id` int(11) NOT NULL,
  `getted` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `orders`
--

INSERT INTO `orders` (`id`, `date`, `order_number`, `customer_id`, `total_price`, `automat_id`, `getted`) VALUES
(36, '2016-12-03 10:15:07', 46, 1, 0, 2, 0),
(37, '2016-12-03 10:16:16', 227, 1, 255, 2, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `amount`, `price`) VALUES
(80, 36, 3, 0, 0),
(81, 36, 2, 0, 0),
(82, 36, 1, 0, 0),
(83, 36, 4, 0, 0),
(84, 37, 3, 0, 0),
(85, 37, 2, 0, 0),
(86, 37, 1, 0, 0),
(87, 37, 4, 3, 255);

-- --------------------------------------------------------

--
-- Struktura tabulky `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `presenter` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `action` varchar(300) COLLATE utf8_czech_ci DEFAULT '',
  `text` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `icon` varchar(20) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `pages`
--

INSERT INTO `pages` (`id`, `presenter`, `action`, `text`, `sort`, `icon`) VALUES
(1, 'Settings', '', 'Nastavení', 3, 'fa-wrench'),
(2, 'Settings', 'changeAccount', 'Změna účtu', 1, ''),
(3, 'Settings', 'users', 'Uživatelé', 2, ''),
(10, 'Credit', '', 'Kredity', 2, 'fa-money'),
(11, 'Credit', 'buyCredit', 'Koupit', 1, ''),
(12, 'Settings', 'products', 'Seznam produktů', 3, ''),
(13, 'Settings', 'automats', 'Seznam automatů', 4, ''),
(14, 'Buying', '', 'Nákup', 1, 'fa-shopping-cart'),
(15, 'Buying', 'buy', 'Provést nákup', 1, ''),
(16, 'Credit', 'creditHistory', 'Historie', 2, ''),
(17, 'Buying', 'myBuys', 'Moje nákupy', 2, '');

-- --------------------------------------------------------

--
-- Struktura tabulky `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `products`
--

INSERT INTO `products` (`id`, `name`, `price`) VALUES
(1, 'Jablko', 8),
(2, 'Hruška', 12),
(3, 'CocaCola', 25),
(4, 'Pizza', 85);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `password` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `salt` varchar(16) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `firstname` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `lastname` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `user_image_name` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `credit` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `group_id`, `password`, `salt`, `email`, `firstname`, `lastname`, `user_image_name`, `credit`) VALUES
(1, 13, '754e8f0f878da804b41c42d8cd4d0848c39cc33821c2e51121232318078afe3b615fb3b7457a9782b226f88883def1b517eaf6a1f7608e25e880ff4e693e5773', '2stop5j7krnogd2m', 'Kufner007@seznam.cz', 'Michael', 'Kufner', '3c49396bef544c5a9f78f07ee06d6c0c.png', 49039),
(20, 1, '56777b8a9d952f423ab37a896d980e3cca854a5d37407984d4a2aa4ff41a94eeee6b260045502d4f409e5f786b1de4ed767905ecd7f9afb6904e350113defeca', 'qybak9ky7a93hdh3', 'tester@nsdfjasdl.cz', 'karel', 'tester', 'default-avatar.png', 0),
(21, 13, '754e8f0f878da804b41c42d8cd4d0848c39cc33821c2e51121232318078afe3b615fb3b7457a9782b226f88883def1b517eaf6a1f7608e25e880ff4e693e5773', '2stop5j7krnogd2m', 'a@a.cz', 'David', 'Vlasák', 'default-avatar.png', 0);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `automats`
--
ALTER TABLE `automats`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `credit_log`
--
ALTER TABLE `credit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Klíče pro tabulku `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `item_storage`
--
ALTER TABLE `item_storage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `automat_id` (`automat_id`);

--
-- Klíče pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `automat_id` (`automat_id`);

--
-- Klíče pro tabulku `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Klíče pro tabulku `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `automats`
--
ALTER TABLE `automats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pro tabulku `credit_log`
--
ALTER TABLE `credit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT pro tabulku `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT pro tabulku `item_storage`
--
ALTER TABLE `item_storage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pro tabulku `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT pro tabulku `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;
--
-- AUTO_INCREMENT pro tabulku `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT pro tabulku `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `credit_log`
--
ALTER TABLE `credit_log`
  ADD CONSTRAINT `credit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `item_storage`
--
ALTER TABLE `item_storage`
  ADD CONSTRAINT `item_storage_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `item_storage_ibfk_2` FOREIGN KEY (`automat_id`) REFERENCES `automats` (`id`);

--
-- Omezení pro tabulku `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`automat_id`) REFERENCES `automats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_5` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
