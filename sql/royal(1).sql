-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Фев 17 2014 г., 08:55
-- Версия сервера: 5.6.14
-- Версия PHP: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `royal`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories_product`
--

CREATE TABLE IF NOT EXISTS `categories_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `number` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `categories_product`
--

INSERT INTO `categories_product` (`id`, `title`, `visible`, `number`) VALUES
(1, 'Двери', 0, 2),
(2, 'Окна', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `category_pages`
--

CREATE TABLE IF NOT EXISTS `category_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `number` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `category_pages`
--

INSERT INTO `category_pages` (`id`, `title`, `visible`, `number`) VALUES
(1, 'О нас', 1, 3),
(2, 'портфолио', 1, 6),
(3, 'контакты', 1, 7),
(4, 'Новости', 1, 8),
(5, 'dasdasdasd', 1, 9),
(6, 'asdasdasd', 1, 5),
(7, 'dasdasd', 1, 1),
(8, 'ssssssssssssssssss', 1, 4),
(9, 'sdfsdf', 1, 10),
(10, 'asdad', 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `colors`
--

CREATE TABLE IF NOT EXISTS `colors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `group` tinyint(4) NOT NULL,
  `typeImage` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `contacts_pages`
--

CREATE TABLE IF NOT EXISTS `contacts_pages` (
  `id` int(10) unsigned NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact_name` varchar(50) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `main_pages`
--

CREATE TABLE IF NOT EXISTS `main_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_category_pages` int(10) unsigned NOT NULL,
  `decription` text NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `manufacturers`
--

CREATE TABLE IF NOT EXISTS `manufacturers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_subcategories_product` int(11) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `number` int(2) NOT NULL,
  `title` varchar(50) NOT NULL,
  `image` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `manufacturers`
--

INSERT INTO `manufacturers` (`id`, `id_subcategories_product`, `visible`, `number`, `title`, `image`) VALUES
(1, 5, 1, 1, 'dd', '275667-1366x768_52ff36f8591b3.jpg'),
(2, 3, 1, 2, 'test 2', 'Space_Planet_fly_in_space_042277_ (1)_52ff45d0d18f3.jpg'),
(3, 4, 1, 2, 'dd', 'logo_52fdfd85e1320.png'),
(4, 3, 1, 1, 'ddddd', '383005-1366x768_52ff45de53c57.jpg'),
(5, 5, 1, 2, 'tttt', '554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_52ff37c17e56d.jpg'),
(6, 3, 1, 3, 'dddd', '35079-1280x1024_52ff470526155.jpg'),
(7, 3, 1, 4, 'dddd', '275667-1366x768_52ff47d8868a8.jpg'),
(8, 3, 1, 5, 'dddsdasdasd', '35079-1280x1024_52ff48481903c.jpg'),
(9, 3, 1, 6, 'ddddadafadasfas', '275667-1366x768_52ff4896aa664.jpg'),
(10, 3, 1, 7, 'dasdas', 'Space_Planet_fly_in_space_042277_ (1)_52ff48e02ee9f.jpg'),
(11, 3, 1, 8, 'dasdas', '554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_52ff4a3c2bf5f.jpg'),
(12, 2, 1, 3, 'dddddddddddddd', 'Space_Planet_fly_in_space_042277__52ffb291313fd.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_subcategories_product` int(11) unsigned NOT NULL,
  `id_categories_product` int(11) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `count` int(5) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file` text NOT NULL,
  `image` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `subcategories_product`
--

CREATE TABLE IF NOT EXISTS `subcategories_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_categories_product` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `image` text NOT NULL,
  `number` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `subcategories_product`
--

INSERT INTO `subcategories_product` (`id`, `id_categories_product`, `title`, `visible`, `image`, `number`) VALUES
(1, 2, 'test 4', 1, 'default_52fcdc4f252ec.png', 2),
(2, 2, 'test', 1, 'default_52fcd9668cb03.png', 1),
(3, 2, 'test2', 1, 'default_52fcda0fa7d7f.png', 3),
(4, 2, 'test 3', 0, 'default_52fcda622926d.png', 4),
(5, 2, 'test 4', 0, 'default_52fcdc4f252ec.png', 5),
(6, 1, 'dor 1', 1, '383005-1366x768_52ffb18b4f0a2.jpg', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
