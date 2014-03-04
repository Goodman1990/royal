-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 04 2014 г., 23:47
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `categories_product`
--

INSERT INTO `categories_product` (`id`, `title`, `visible`, `number`) VALUES
(1, 'покрытие для стен', 1, 1),
(2, 'покрытие для пола', 1, 2),
(3, 'двери', 1, 3),
(4, 'двери', 1, 4),
(5, 'мебель', 1, 5),
(6, 'системы для дома', 1, 6);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `category_pages`
--

INSERT INTO `category_pages` (`id`, `title`, `visible`, `number`) VALUES
(1, 'Главная', 1, 1),
(2, 'О нас', 1, 2),
(3, 'Портфолио', 1, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `colors`
--

CREATE TABLE IF NOT EXISTS `colors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `color` varchar(20) NOT NULL,
  `image_color` text NOT NULL,
  `id_product` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Дамп данных таблицы `colors`
--

INSERT INTO `colors` (`id`, `color`, `image_color`, `id_product`) VALUES
(1, '70331c', '/tmp/383005-1366x768_5314f095d5575.jpg', 1),
(2, '42338b', '/tmp/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_5314f095e7e5a.jpg', 1),
(3, '', '', 2),
(4, '', '', 3),
(5, '', '', 4),
(6, '', '', 5),
(7, '', '', 6),
(8, '', '', 7),
(9, '', '', 8),
(10, '', '', 9),
(11, '', '/siteDir/product/color/', 10),
(12, '', '/siteDir/product/color/', 11),
(13, '3f3f3e', '/siteDir/product/color/sfasfasfas_5314f660ef6c2.jpg', 12),
(14, '47358d', '/siteDir/product/color/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_5314f66100a74.jpg', 12),
(15, '3f3f3e', '/siteDir/product/color/sfasfasfas_5314f660ef6c2.jpg', 13),
(16, '47358d', '/siteDir/product/color/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_5314f66100a74.jpg', 13),
(17, '3f3f3e', '/siteDir/product/color/sfasfasfas_5314f660ef6c2.jpg', 14),
(18, '47358d', '/siteDir/product/color/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_5314f66100a74.jpg', 14),
(19, '3f3f3e', '/siteDir/product/color/sfasfasfas_5314f660ef6c2.jpg', 15),
(20, '47358d', '/siteDir/product/color/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_5314f66100a74.jpg', 15),
(21, '3f3f3e', '/siteDir/product/color/sfasfasfas_5314f660ef6c2.jpg', 16),
(22, '47358d', '/siteDir/product/color/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_5314f66100a74.jpg', 16),
(23, '131314', '/siteDir/product/color/sfasfasfas_5316367c60c85.jpg', 1),
(24, '433294', '/siteDir/product/color/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_5316368bc4d9e.jpg', 1),
(25, '131151156', '/siteDir/product/color/275667-1366x768_5316391f99932.jpg', 1),
(26, '1648443', '/siteDir/product/color/383005-1366x768 - копия_5316392a7ab88.jpg', 1),
(27, '999', '/siteDir/product/color/sfasfasfas_53163932d9cf0.jpg', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `main_pages`
--

INSERT INTO `main_pages` (`id`, `id_category_pages`, `decription`, `content`) VALUES
(1, 1, '23', '<p>\r\n	<span class="alegreto-font">В</span> XXIвеке, когда самые выдающиеся открытия, инновации и изобретения остались в далеком прошлом, казалось бы, уже нет места чему-то новому, чему-то, что может удивить и добавить ярких красок в нашу жизнь. Но, из года в год, на рынке появляется что-то, без чего мы уже не представляем нашу с Вами жизнь, будь то очередной девайс от Apple или другой новомодный гаджет.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<span class="alegreto-font">Т</span>ак происходит и в области дизайна интерьеров. Чтобы реализовать даже самый неординарный замысел дизайнера или удовлетворить взыскательного покупателя производителям приходится идти в ногу со временем и выводить на рынок новые материалы, которые позволят подчеркнуть Вашу индивидуальность и создать неповторимый интерьер.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<span class="alegreto-font">М</span>ы тщательно следим за интерьерной модой и представляем Вашему вниманию не только декоративные материалы, которые остаются популярными на протяжении десятков лет, но и новинки, которые только набирают свою популярность и выводят на новый качественный уровень дизайн интерьеров.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<span class="alegreto-font">В</span>месте с нами Вы воплотите свои самые смелые творческие идеи.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<span class="alegreto-font">М</span>ы работаем для Вас!</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<span class="alegreto-font">С</span> уважением, коммерческий директор RoyalBuildingGroup</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Тимошенко Анна <img class="podpis" src="/image/podpis.png" /></p>\r\n'),
(2, 2, '23', '<p>\r\n	фывдфыждвфжыв&nbsp;</p>\r\n<p>\r\n	фвы</p>\r\n<p>\r\n	фы</p>\r\n<p>\r\n	в</p>\r\n<p>\r\n	фы</p>\r\n<p>\r\n	в</p>\r\n'),
(3, 3, '23', '<ol>\r\n	<li>\r\n		<p>\r\n			рррррррррррррррррррррррррр</p>\r\n	</li>\r\n	<li>\r\n		<p>\r\n			фывфывфыв</p>\r\n	</li>\r\n	<li>\r\n		<p>\r\n			фывфыв</p>\r\n	</li>\r\n</ol>\r\n<p>\r\n	фывфывфыв</p>\r\n<p>\r\n	ф</p>\r\n<p>\r\n	ыв</p>\r\n<p>\r\n	фы</p>\r\n<p>\r\n	в</p>\r\n');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `manufacturers`
--

INSERT INTO `manufacturers` (`id`, `id_subcategories_product`, `visible`, `number`, `title`, `image`) VALUES
(1, 1, 1, 1, 'wango', 'logo-proiz_53060b6ccb72e.png'),
(2, 1, 1, 2, 'хороший производитель', 'slider-image_5306422ccb045.jpg'),
(3, 5, 1, 1, 'еуыыевфыв', 'Untitled-1_53076c7a03ee9.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_subcategories_product` int(11) unsigned NOT NULL,
  `id_categories_product` int(11) unsigned NOT NULL,
  `id_manufacturers` int(11) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `technical_description` text NOT NULL,
  `price` int(6) NOT NULL,
  `addres_buy` text,
  `video` varchar(32) DEFAULT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file` text NOT NULL,
  `count_buy` int(11) NOT NULL,
  `image` text NOT NULL,
  `main_image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `product`
--

INSERT INTO `product` (`id`, `id_subcategories_product`, `id_categories_product`, `id_manufacturers`, `title`, `description`, `technical_description`, `price`, `addres_buy`, `video`, `date_create`, `file`, `count_buy`, `image`, `main_image`) VALUES
(1, 1, 1, 1, 'test product 1', '<p style="margin: 0.4em 0px 0.5em; line-height: 19.200000762939453px; color: rgb(0, 0, 0); font-family: sans-serif; font-size: 13px;">\r\n	<b><a href="https://ru.wikipedia.org/wiki/%D0%AF%D0%BD%D1%8C_%D0%A4%D1%83" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Янь Фу">Янь Фу</a></b>&nbsp;(1854&mdash;1921) &mdash; китайский философ, общественный деятель и переводчик. Идеолог реформаторского движения в&nbsp;<a class="mw-redirect" href="https://ru.wikipedia.org/wiki/%D0%94%D0%B8%D0%BD%D0%B0%D1%81%D1%82%D0%B8%D1%8F_%D0%A6%D0%B8%D0%BD" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Династия Цин">имперском Китае</a>, первым ознакомил соотечественников с достижениями западных общественных наук; перевёл на китайский язык произведения&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%9C%D0%BE%D0%BD%D1%82%D0%B5%D1%81%D0%BA%D1%8C%D1%91,_%D0%A8%D0%B0%D1%80%D0%BB%D1%8C-%D0%9B%D1%83%D0%B8_%D0%B4%D0%B5_%D0%A1%D0%B5%D0%BA%D0%BE%D0%BD%D0%B4%D0%B0" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Монтескьё, Шарль-Луи де Секонда">Шарля Монтескьё</a>,&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%A1%D0%BC%D0%B8%D1%82,_%D0%90%D0%B4%D0%B0%D0%BC" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Смит, Адам">Адама Смита</a>,&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%9C%D0%B8%D0%BB%D0%BB%D1%8C,_%D0%94%D0%B6%D0%BE%D0%BD_%D0%A1%D1%82%D1%8E%D0%B0%D1%80%D1%82" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Милль, Джон Стюарт">Джона Стюарта Милля</a>,&nbsp;<a class="mw-redirect" href="https://ru.wikipedia.org/wiki/%D0%93%D0%B5%D0%BA%D1%81%D0%BB%D0%B8,_%D0%A2%D0%BE%D0%BC%D0%B0%D1%81" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Гексли, Томас">Томаса Гексли</a>. Получил образование в Англии, в 1909 году одним из последних получил высшую конфуцианскую учёную степень&nbsp;<a class="mw-redirect" href="https://ru.wikipedia.org/wiki/%D0%A6%D0%B7%D0%B8%D0%BD%D1%8C%D1%88%D0%B8" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Цзиньши">цзиньши</a>. Автор слов&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%97%D0%BE%D0%BB%D0%BE%D1%82%D0%BE%D0%B9_%D0%BA%D1%83%D0%B1%D0%BE%D0%BA_(%D0%B3%D0%B8%D0%BC%D0%BD)" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Золотой кубок (гимн)">государственного гимна Цинской империи</a>, утверждённого за 6 дней до начала&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%A1%D0%B8%D0%BD%D1%8C%D1%85%D0%B0%D0%B9%D1%81%D0%BA%D0%B0%D1%8F_%D1%80%D0%B5%D0%B2%D0%BE%D0%BB%D1%8E%D1%86%D0%B8%D1%8F" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Синьхайская революция">Синьхайской революции</a>.</p>\r\n<p style="margin: 0.4em 0px 0.5em; line-height: 19.200000762939453px; color: rgb(0, 0, 0); font-family: sans-serif; font-size: 13px;">\r\n	Его семейство происходило из&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%A5%D1%8D%D0%BD%D0%B0%D0%BD%D1%8C" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Хэнань">Хэнани</a>, переселившись в&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%A4%D1%83%D1%86%D0%B7%D1%8F%D0%BD%D1%8C" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Фуцзянь">Фуцзянь</a>&nbsp;ещё во времена<a href="https://ru.wikipedia.org/wiki/%D0%A2%D0%B0%D0%BD_(%D0%B4%D0%B8%D0%BD%D0%B0%D1%81%D1%82%D0%B8%D1%8F)" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Тан (династия)">династии Тан</a>. Отец &mdash; Янь Чжэнсянь &mdash; был потомственным сельским врачом. В 12-летнем возрасте Янь Фу отдали в обучение Хуан Шаояню, который одинаковое внимание уделял и ханьскому учению, и сунскому неоконфуцианству; обучение продлилось всего два года, но ученик оказывал знаки внимания учителю до конца жизни. Хотя в 14 лет Янь Фу пришлось прервать классическое образование (в общей сложности оно продлилось 7 лет), но, обладая отличными способностями и памятью, он многое усвоил из арсенала древнекитайской культуры и в зрелые годы всегда выказывал к ней уважение, особенно к поэзии и каллиграфии. После скоропостижной смерти отца в 1866 году семья &mdash; вдова и трое детей &mdash; оказались в крайне стеснённом материальном положении; мать еле сводила концы с концами, занимаясь рукоделием. По воле матери в 1868 году 14-летний Янь женился на девице Ван, которая родила ему сына Янь Цю. Она скончалась в 1892 году.</p>\r\n', '<p style="margin: 0.4em 0px 0.5em; line-height: 19.200000762939453px; color: rgb(0, 0, 0); font-family: sans-serif; font-size: 13px;">\r\n	<b><a href="https://ru.wikipedia.org/wiki/%D0%AF%D0%BD%D1%8C_%D0%A4%D1%83" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Янь Фу">Янь Фу</a></b>&nbsp;(1854&mdash;1921) &mdash; китайский философ, общественный деятель и переводчик. Идеолог реформаторского движения в&nbsp;<a class="mw-redirect" href="https://ru.wikipedia.org/wiki/%D0%94%D0%B8%D0%BD%D0%B0%D1%81%D1%82%D0%B8%D1%8F_%D0%A6%D0%B8%D0%BD" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Династия Цин">имперском Китае</a>, первым ознакомил соотечественников с достижениями западных общественных наук; перевёл на китайский язык произведения&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%9C%D0%BE%D0%BD%D1%82%D0%B5%D1%81%D0%BA%D1%8C%D1%91,_%D0%A8%D0%B0%D1%80%D0%BB%D1%8C-%D0%9B%D1%83%D0%B8_%D0%B4%D0%B5_%D0%A1%D0%B5%D0%BA%D0%BE%D0%BD%D0%B4%D0%B0" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Монтескьё, Шарль-Луи де Секонда">Шарля Монтескьё</a>,&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%A1%D0%BC%D0%B8%D1%82,_%D0%90%D0%B4%D0%B0%D0%BC" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Смит, Адам">Адама Смита</a>,&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%9C%D0%B8%D0%BB%D0%BB%D1%8C,_%D0%94%D0%B6%D0%BE%D0%BD_%D0%A1%D1%82%D1%8E%D0%B0%D1%80%D1%82" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Милль, Джон Стюарт">Джона Стюарта Милля</a>,&nbsp;<a class="mw-redirect" href="https://ru.wikipedia.org/wiki/%D0%93%D0%B5%D0%BA%D1%81%D0%BB%D0%B8,_%D0%A2%D0%BE%D0%BC%D0%B0%D1%81" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Гексли, Томас">Томаса Гексли</a>. Получил образование в Англии, в 1909 году одним из последних получил высшую конфуцианскую учёную степень&nbsp;<a class="mw-redirect" href="https://ru.wikipedia.org/wiki/%D0%A6%D0%B7%D0%B8%D0%BD%D1%8C%D1%88%D0%B8" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Цзиньши">цзиньши</a>. Автор слов&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%97%D0%BE%D0%BB%D0%BE%D1%82%D0%BE%D0%B9_%D0%BA%D1%83%D0%B1%D0%BE%D0%BA_(%D0%B3%D0%B8%D0%BC%D0%BD)" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Золотой кубок (гимн)">государственного гимна Цинской империи</a>, утверждённого за 6 дней до начала&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%A1%D0%B8%D0%BD%D1%8C%D1%85%D0%B0%D0%B9%D1%81%D0%BA%D0%B0%D1%8F_%D1%80%D0%B5%D0%B2%D0%BE%D0%BB%D1%8E%D1%86%D0%B8%D1%8F" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Синьхайская революция">Синьхайской революции</a>.</p>\r\n<p style="margin: 0.4em 0px 0.5em; line-height: 19.200000762939453px; color: rgb(0, 0, 0); font-family: sans-serif; font-size: 13px;">\r\n	Его семейство происходило из&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%A5%D1%8D%D0%BD%D0%B0%D0%BD%D1%8C" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Хэнань">Хэнани</a>, переселившись в&nbsp;<a href="https://ru.wikipedia.org/wiki/%D0%A4%D1%83%D1%86%D0%B7%D1%8F%D0%BD%D1%8C" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Фуцзянь">Фуцзянь</a>&nbsp;ещё во времена<a href="https://ru.wikipedia.org/wiki/%D0%A2%D0%B0%D0%BD_(%D0%B4%D0%B8%D0%BD%D0%B0%D1%81%D1%82%D0%B8%D1%8F)" style="text-decoration: none; color: rgb(11, 0, 128); background-image: none; background-position: initial initial; background-repeat: initial initial;" title="Тан (династия)">династии Тан</a>. Отец &mdash; Янь Чжэнсянь &mdash; был потомственным сельским врачом. В 12-летнем возрасте Янь Фу отдали в обучение Хуан Шаояню, который одинаковое внимание уделял и ханьскому учению, и сунскому неоконфуцианству; обучение продлилось всего два года, но ученик оказывал знаки внимания учителю до конца жизни. Хотя в 14 лет Янь Фу пришлось прервать классическое образование (в общей сложности оно продлилось 7 лет), но, обладая отличными способностями и памятью, он многое усвоил из арсенала древнекитайской культуры и в зрелые годы всегда выказывал к ней уважение, особенно к поэзии и каллиграфии. После скоропостижной смерти отца в 1866 году семья &mdash; вдова и трое детей &mdash; оказались в крайне стеснённом материальном положении; мать еле сводила концы с концами, занимаясь рукоделием. По воле матери в 1868 году 14-летний Янь женился на девице Ван, которая родила ему сына Янь Цю. Она скончалась в 1892 году.</p>\r\n', 123, '', 'IuGEAb6hNWw,IuGEAb6hNWw,IuGEAb6h', '2014-03-04 20:36:16', 'public/siteDir//product//siteDir/product/275667-1366x768_531638f8c344d.jpg,public/siteDir//product//siteDir/product/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_531638f8c7e86.jpg', 0, 'public/siteDir//product//siteDir/product/275667-1366x768_531638f8c344d.jpg,public/siteDir//product//siteDir/product/554980_abstraktsiya_3d_art_1920x1080_(www.GdeFon.ru)_531638f8c7e86.jpg', '/siteDir/product/35079-1280x1024_531638f8c1cdd.jpg');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `subcategories_product`
--

INSERT INTO `subcategories_product` (`id`, `id_categories_product`, `title`, `visible`, `image`, `number`) VALUES
(1, 1, 'Декоративные  штукатурки', 1, 'Untitled-1_530609f8aa2d1.jpg', 1),
(2, 1, 'Фрески', 1, 'freski_53060a3899bc2.jpg', 2),
(3, 1, 'Обои', 1, 'oboi_53060a5e1012f.jpg', 3),
(4, 1, '3-D панели', 1, '3dpanel_53060a8977b53.jpg', 4),
(5, 2, 'пол', 1, 'Untitled-1_53076c477c520.jpg', 1);

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
