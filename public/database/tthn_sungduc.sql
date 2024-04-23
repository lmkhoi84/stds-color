-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 01, 2022 at 10:05 AM
-- Server version: 5.7.26
-- PHP Version: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tthn_sungduc`
--

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `sort`, `status`, `created_at`, `updated_at`) VALUES
(1, 'vi', 1, 1, NULL, '2020-03-04 07:49:45'),
(2, 'en', 2, 1, NULL, '2022-11-25 13:33:12'),
(4, 'cn', 3, 0, '2022-11-25 14:15:11', '2022-11-25 16:17:51');

-- --------------------------------------------------------

--
-- Table structure for table `languages_translations`
--

DROP TABLE IF EXISTS `languages_translations`;
CREATE TABLE IF NOT EXISTS `languages_translations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `languages_id` bigint(20) UNSIGNED NOT NULL,
  `languages_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `languages_translations_languages_id_locale_unique` (`languages_id`,`locale`),
  KEY `languages_translations_locale_index` (`locale`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages_translations`
--

INSERT INTO `languages_translations` (`id`, `languages_id`, `languages_name`, `locale`) VALUES
(1, 1, 'Tiếng Việt', 'vi'),
(2, 1, 'Vietnamese', 'en'),
(3, 2, 'Tiếng Anh', 'vi'),
(4, 2, 'English', 'en'),
(10, 4, 'Tiếng Trung Quốc', 'vi'),
(11, 4, 'Chinese', 'en');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_16_025642_create_structure_table', 1),
(4, '2019_09_16_072116_create_sessions_table', 1),
(5, '2019_10_09_113802_create_products_hn', 1),
(6, '2019_10_09_115008_create_products', 1),
(7, '2019_10_09_115133_create_products_hcm', 1),
(8, '2019_10_09_115701_create_products_translations', 1),
(9, '2019_10_11_102132_create_languages_table', 1),
(10, '2019_10_14_213326_create_users_group_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_about` tinyint(4) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_user` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_user` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`set_about`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_translations`
--

DROP TABLE IF EXISTS `news_translations`;
CREATE TABLE IF NOT EXISTS `news_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `news_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `news_content` text COLLATE utf8_unicode_ci,
  `trans_page` text COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_News_ID` (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `old_id` int(11) NOT NULL,
  `product_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `images` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `sort` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `processing` tinyint(4) NOT NULL,
  `sale_off_type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `show_in_stock` tinyint(4) NOT NULL,
  `calculate_type` tinyint(4) NOT NULL,
  `created_user` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_parent_id_index` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `payload` text COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  UNIQUE KEY `sessions_id_unique` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `structure`
--

DROP TABLE IF EXISTS `structure`;
CREATE TABLE IF NOT EXISTS `structure` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `structure_url` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `page_type` tinyint(4) NOT NULL,
  `sort` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `icon` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_user` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `structure_structure_url_index` (`structure_url`),
  KEY `structure_parent_id_index` (`parent_id`),
  KEY `structure_page_type_index` (`page_type`),
  KEY `structure_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `structure`
--

INSERT INTO `structure` (`id`, `structure_url`, `parent_id`, `page_type`, `sort`, `level`, `status`, `icon`, `created_user`, `created_at`, `updated_at`) VALUES
(1, 'home', 0, 1, 1, 1, 1, 'bx-home-circle', 1, NULL, '2022-11-25 06:20:41'),
(2, 'structure', 0, 1, 0, 1, 1, 'bx-list-check', 1, NULL, '2022-11-26 14:59:49'),
(3, 'multi-languages', 0, 1, 2, 1, 1, 'bx-globe', 1, NULL, '2022-11-26 15:00:13'),
(4, 'article-categories', 0, 1, 5, 1, 1, 'bx-category', 1, NULL, '2022-11-26 16:02:43'),
(5, 'about', 0, 1, 4, 1, 1, 'bx bx-info-circle', 1, NULL, '2022-11-26 16:02:33'),
(6, 'users', 0, 1, 98, 1, 1, 'bx-user', 1, NULL, '2022-12-01 03:38:06'),
(7, 'account', 0, 1, 99, 1, 1, 'bx-user-circle', 1, NULL, '2022-11-25 07:14:58'),
(8, 'change-language', 0, 2, 901, 1, 1, 'mdi-translate', 1, NULL, '2022-12-01 07:06:33'),
(9, 'login', 0, 2, 902, 1, 1, 'mdi-account-check', 1, NULL, '2022-12-01 07:08:10'),
(10, 'register', 0, 2, 903, 1, 1, 'mdi-account-plus', 1, NULL, '2022-12-01 07:05:54'),
(11, 'forgot-password', 0, 2, 904, 1, 1, 'mdi-account-key', 1, NULL, '2022-12-01 07:06:06'),
(12, 'add', 2, 3, 2, 2, 1, 'mdi-file', 1, NULL, NULL),
(13, 'edit', 2, 3, 3, 2, 1, '', 1, NULL, NULL),
(14, 'delete', 2, 3, 4, 2, 1, '', 1, NULL, NULL),
(15, 'languages', 3, 1, 1, 2, 1, 'mdi-earth', 1, NULL, NULL),
(16, 'translations', 3, 1, 2, 2, 1, '', 1, NULL, '2022-11-25 07:06:07'),
(17, 'edit', 16, 3, 1, 3, 1, '', 1, NULL, NULL),
(18, 'add', 15, 3, 1, 3, 1, 'mdi-file', 1, NULL, NULL),
(19, 'edit', 15, 3, 2, 3, 1, '', 1, NULL, NULL),
(20, 'delete', 15, 3, 3, 3, 1, '', 1, NULL, NULL),
(21, 'users-group', 6, 1, 1, 2, 1, 'mdi-folder-account', 1, NULL, NULL),
(22, 'users-list', 6, 1, 2, 2, 1, 'mdi-account-multiple-plus', 1, NULL, NULL),
(23, 'add', 21, 3, 1, 3, 1, '', 1, NULL, NULL),
(24, 'edit', 21, 3, 2, 3, 1, '', 1, NULL, '2019-10-25 02:42:38'),
(25, 'delete', 21, 3, 3, 3, 1, '', 1, NULL, NULL),
(26, 'add', 22, 3, 1, 3, 1, '', 1, NULL, NULL),
(27, 'edit', 22, 3, 2, 3, 1, '', 1, NULL, NULL),
(28, 'delete', 22, 3, 3, 3, 1, '', 1, NULL, NULL),
(29, 'en', 8, 3, 1, 3, 1, '', 1, NULL, NULL),
(30, 'vi', 8, 3, 3, 2, 1, '', 1, NULL, '2021-09-11 15:39:46'),
(31, 'change-status', 21, 3, 4, 3, 1, '', 1, NULL, NULL),
(32, 'change-status', 22, 3, 4, 3, 1, '', 1, NULL, NULL),
(33, 'change-status', 15, 3, 4, 3, 1, '', 1, NULL, NULL),
(34, 'edit', 7, 3, 1, 2, 1, '', 1, '2019-10-28 07:02:25', '2019-10-28 07:02:25'),
(45, 'add', 5, 3, 1, 2, 1, '', 1, '2020-02-21 08:26:15', '2020-02-21 08:26:15'),
(46, 'edit', 5, 3, 2, 2, 1, '', 1, '2020-02-21 08:26:29', '2020-02-21 08:26:29'),
(47, 'delete', 5, 3, 3, 2, 1, '', 1, '2020-02-21 08:26:40', '2020-02-21 08:28:14'),
(49, 'change-status', 5, 3, 4, 2, 1, '', 1, '2020-03-10 03:06:30', '2020-03-10 03:06:30'),
(50, 'students', 0, 1, 14, 1, 1, 'bx-user-pin', 1, '2020-03-10 12:22:36', '2022-12-01 03:43:46'),
(51, 'add', 50, 3, 1, 2, 1, '', 1, '2020-03-10 12:24:10', '2020-03-10 12:42:30'),
(52, 'edit', 50, 3, 2, 2, 1, '', 1, '2020-03-10 12:24:24', '2020-03-10 12:42:41'),
(53, 'delete', 50, 3, 3, 2, 1, '', 1, '2020-03-10 12:24:40', '2020-03-10 12:25:50'),
(54, 'export', 0, 1, 15, 1, 0, 'mdi-export', 1, '2020-03-10 12:27:19', '2022-11-26 15:02:35'),
(55, 'add', 54, 3, 1, 2, 1, '', 1, '2020-03-10 12:31:22', '2020-03-10 12:31:22'),
(56, 'edit', 54, 3, 2, 2, 1, '', 1, '2020-03-10 12:31:47', '2020-03-10 12:31:47'),
(57, 'delete', 54, 3, 3, 2, 1, '', 1, '2020-03-10 12:32:02', '2020-03-10 12:32:02'),
(59, 'ajax', 0, 3, 905, 1, 1, '', 1, '2020-05-11 15:30:58', '2022-12-01 07:07:14'),
(60, 'import-export', 59, 3, 1, 2, 1, '', 1, '2020-05-11 15:32:06', '2020-05-13 02:39:51'),
(63, 'add', 4, 3, 91, 2, 1, '', 1, '2021-07-02 09:36:40', '2022-11-26 16:21:22'),
(69, 'class', 0, 1, 13, 1, 1, 'bx-building-house', 1, '2021-09-07 13:03:12', '2022-12-01 03:34:33'),
(73, 'customers', 0, 1, 91, 1, 0, 'mdi-account-card-details', 1, '2022-03-21 02:07:39', '2022-11-26 15:02:44'),
(74, 'staffs', 0, 1, 92, 1, 0, 'mdi-account-multiple-outline', 1, '2022-03-21 02:09:25', '2022-11-26 15:03:06'),
(75, 'add', 73, 3, 1, 2, 1, '', 1, '2022-03-21 02:10:44', '2022-03-21 02:11:33'),
(76, 'edit', 73, 3, 2, 2, 1, '', 1, '2022-03-21 02:10:59', '2022-03-21 02:10:59'),
(77, 'delete', 73, 3, 3, 2, 1, '', 1, '2022-03-21 02:11:10', '2022-03-21 02:11:10'),
(78, 'add', 74, 3, 1, 2, 1, '', 1, '2022-03-21 02:11:53', '2022-03-21 02:11:53'),
(79, 'edit', 74, 3, 2, 2, 1, '', 1, '2022-03-21 02:12:03', '2022-03-21 02:12:03'),
(80, 'delete', 74, 3, 3, 2, 1, '', 1, '2022-03-21 02:12:14', '2022-03-21 02:12:14'),
(84, 'change-status', 74, 3, 4, 2, 1, '', 1, '2022-04-27 15:04:07', '2022-04-27 15:04:07'),
(85, 'articles', 0, 1, 6, 1, 1, 'bx-news', 1, '2022-05-17 07:03:09', '2022-11-26 16:02:51'),
(86, 'add', 85, 3, 1, 2, 1, '', 1, '2022-05-17 07:05:10', '2022-05-17 07:05:10'),
(87, 'edit', 85, 3, 2, 2, 1, '', 1, '2022-05-17 07:05:21', '2022-05-17 07:05:21'),
(88, 'delete', 85, 3, 3, 2, 1, '', 1, '2022-05-17 07:05:49', '2022-05-17 07:05:49'),
(89, 'change-status', 85, 3, 4, 2, 1, '', 1, '2022-05-17 08:01:54', '2022-05-17 08:01:54'),
(90, 'add', 69, 3, 1, 2, 1, '', 1, '2022-06-01 02:50:35', '2022-12-01 06:33:38'),
(91, 'edit', 69, 3, 2, 2, 1, '', 1, '2022-06-07 06:49:13', '2022-12-01 06:33:51'),
(98, 'edit', 4, 3, 92, 2, 1, '', 1, '2022-11-26 16:05:42', '2022-11-26 16:22:34'),
(99, 'delete', 4, 3, 93, 2, 1, '', 1, '2022-11-26 16:06:03', '2022-11-26 16:22:20'),
(105, 'notification', 4, 2, 1, 2, 1, '', 1, '2022-11-29 07:11:44', '2022-11-29 07:13:07'),
(106, 'delete', 69, 3, 3, 2, 1, '', 1, '2022-12-01 06:34:46', '2022-12-01 06:34:46'),
(107, 'change-status', 69, 3, 4, 2, 1, '', 1, '2022-12-01 06:35:13', '2022-12-01 06:35:13');

-- --------------------------------------------------------

--
-- Table structure for table `structure_translations`
--

DROP TABLE IF EXISTS `structure_translations`;
CREATE TABLE IF NOT EXISTS `structure_translations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `structure_id` bigint(20) UNSIGNED NOT NULL,
  `structure_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trans_page` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `structure_translations_structure_id_locale_unique` (`structure_id`,`locale`),
  KEY `structure_translations_locale_index` (`locale`)
) ENGINE=InnoDB AUTO_INCREMENT=218 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `structure_translations`
--

INSERT INTO `structure_translations` (`id`, `structure_id`, `structure_name`, `trans_page`, `locale`) VALUES
(1, 1, 'Home', 'a:7:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"YfJgyBTsHjqvrs7mstezSWJ8cuqRy6JyhM4tv6cm\";s:4:\"lang\";s:2:\"en\";s:5:\"title\";s:4:\"Home\";s:11:\"chart_title\";s:6:\"Orders\";s:11:\"statistical\";s:11:\"Statistical\";s:10:\"create_new\";s:10:\"Create New\";}', 'en'),
(2, 1, 'Trang Chủ', 'a:7:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"YfJgyBTsHjqvrs7mstezSWJ8cuqRy6JyhM4tv6cm\";s:4:\"lang\";s:2:\"vi\";s:5:\"title\";s:11:\"Trang Chủ\";s:11:\"chart_title\";s:11:\"Đơn Hàng\";s:11:\"statistical\";s:11:\"Thống Kê\";s:10:\"create_new\";s:11:\"Thêm mới\";}', 'vi'),
(3, 2, 'Structure', 'a:42:{s:10:\"list_title\";s:14:\"Structure List\";s:4:\"root\";s:4:\"Root\";s:3:\"add\";s:3:\"Add\";s:4:\"edit\";s:4:\"Edit\";s:6:\"delete\";s:6:\"Delete\";s:10:\"create_new\";s:10:\"Create New\";s:6:\"parent\";s:6:\"Parent\";s:14:\"structure_name\";s:4:\"Name\";s:9:\"menu_icon\";s:4:\"Icon\";s:4:\"sort\";s:4:\"Sort\";s:21:\"structure_name_holder\";s:20:\"Input name of menu !\";s:16:\"menu_icon_holder\";s:20:\"Input icon of menu !\";s:11:\"sort_holder\";s:19:\"Input Sort Number !\";s:9:\"page_type\";s:9:\"Page Type\";s:4:\"menu\";s:4:\"Menu\";s:8:\"category\";s:8:\"Category\";s:6:\"action\";s:6:\"Action\";s:6:\"status\";s:6:\"Status\";s:7:\"disable\";s:7:\"Disable\";s:6:\"enable\";s:6:\"Enable\";s:6:\"update\";s:6:\"Update\";s:10:\"ask_delete\";s:33:\"Are you sure to delete this item?\";s:12:\"back_to_list\";s:12:\"Back to List\";s:26:\"structure_name_vi_required\";s:18:\"Name is required !\";s:18:\"parent_id_required\";s:20:\"Parent is required !\";s:17:\"parent_id_numeric\";s:28:\"Parent ID must be a number !\";s:13:\"sort_required\";s:18:\"Sort is required !\";s:12:\"sort_numeric\";s:23:\"Sort must be a number !\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:12:\"delete_error\";s:30:\"have one or more child items !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:8:\"is_exist\";s:19:\"is already exists !\";s:9:\"not_exist\";s:17:\"does not exists !\";s:16:\"error_permission\";s:40:\"You have not permissions for this page !\";s:10:\"my_profile\";s:10:\"My Profile\";s:6:\"logout\";s:6:\"Logout\";s:6:\"search\";s:6:\"Search\";s:13:\"search_holder\";s:27:\"Input keywords for search !\";}', 'en'),
(4, 2, 'Cấu Trúc Web', 'a:42:{s:10:\"list_title\";s:15:\"Cấu Trúc Web\";s:4:\"root\";s:16:\"Thư Mục Gốc\";s:3:\"add\";s:5:\"Thêm\";s:4:\"edit\";s:5:\"Sửa\";s:6:\"delete\";s:4:\"Xóa\";s:10:\"create_new\";s:11:\"Thêm Mới\";s:6:\"parent\";s:3:\"Cha\";s:14:\"structure_name\";s:4:\"Tên\";s:9:\"menu_icon\";s:15:\"Biểu Tượng\";s:4:\"sort\";s:11:\"Sắp Xếp\";s:21:\"structure_name_holder\";s:18:\"Nhập tên menu !\";s:16:\"menu_icon_holder\";s:24:\"Nhập Biểu Tượng !\";s:11:\"sort_holder\";s:31:\"Nhập Thứ Tự Sắp Xếp !\";s:9:\"page_type\";s:6:\"Kiểu\";s:4:\"menu\";s:4:\"Menu\";s:8:\"category\";s:10:\"Danh Mục\";s:6:\"action\";s:9:\"Tác Vụ\";s:6:\"status\";s:13:\"Trạng Thái\";s:7:\"disable\";s:15:\"Vô Hiệu Hóa\";s:6:\"enable\";s:14:\"Hoạt Động\";s:6:\"update\";s:12:\"Cập Nhật\";s:10:\"ask_delete\";s:34:\"Bạn có muốn xóa mục này ?\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:26:\"structure_name_vi_required\";s:23:\"Tên là bắt buộc !\";s:18:\"parent_id_required\";s:27:\"Menu Cha là bắt buộc !\";s:17:\"parent_id_numeric\";s:29:\"Id Menu Cha phải là số !\";s:13:\"sort_required\";s:41:\"Thứ Tự Sắp Xếp là bắt buộc !\";s:12:\"sort_numeric\";s:40:\"Thứ Tự Sắp Xếp phải là số !\";s:7:\"success\";s:14:\"Thành Công !\";s:5:\"error\";s:7:\"Lỗi !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:12:\"delete_error\";s:35:\"có một hoặc nhiều item con !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:9:\"not_exist\";s:20:\"không tồn tại !\";s:16:\"error_permission\";s:58:\"Bạn chưa được phân quyền truy cập trang này !\";s:10:\"my_profile\";s:10:\"Thông tin\";s:6:\"logout\";s:13:\"Đăng xuất\";s:6:\"search\";s:11:\"Tìm kiếm\";s:13:\"search_holder\";s:31:\"Nhập từ khóa tìm kiếm !\";}', 'vi'),
(5, 3, 'Multi Languages', 'a:29:{s:14:\"translate_list\";s:14:\"Translate List\";s:9:\"add_title\";s:16:\"Add New Language\";s:6:\"update\";s:6:\"Update\";s:12:\"back_to_list\";s:12:\"Back to List\";s:10:\"list_title\";s:13:\"Language List\";s:14:\"translate_page\";s:14:\"Translate Page\";s:6:\"status\";s:6:\"Status\";s:6:\"action\";s:6:\"Action\";s:6:\"enable\";s:6:\"Enable\";s:7:\"disable\";s:7:\"Disable\";s:15:\"update_language\";s:15:\"Update Language\";s:13:\"language_name\";s:13:\"Language Name\";s:4:\"code\";s:13:\"Language Code\";s:4:\"sort\";s:4:\"Sort\";s:11:\"sort_holder\";s:12:\"Input sort !\";s:11:\"name_holder\";s:12:\"Input Name !\";s:11:\"code_holder\";s:21:\"Input language Code !\";s:13:\"code_required\";s:18:\"Code is required !\";s:10:\"create_new\";s:10:\"Create New\";s:16:\"name_en_required\";s:18:\"Name is Required !\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:8:\"is_exist\";s:18:\"is already exits !\";s:14:\"status_changed\";s:18:\"has been changed !\";s:10:\"ask_delete\";s:37:\"Do you want to delete this language ?\";}', 'en'),
(6, 3, 'Đa Ngôn Ngữ', 'a:29:{s:14:\"translate_list\";s:23:\"Danh sách Trang Dịch\";s:9:\"add_title\";s:23:\"Thêm Ngôn Ngữ Mới\";s:6:\"update\";s:12:\"Cập nhật\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:10:\"list_title\";s:22:\"Danh sách Ngôn Ngữ\";s:14:\"translate_page\";s:12:\"Trang dịch\";s:6:\"status\";s:13:\"Trạng thái\";s:6:\"action\";s:9:\"Tác vụ\";s:6:\"enable\";s:14:\"Hoạt động\";s:7:\"disable\";s:15:\"Vô hiệu hóa\";s:15:\"update_language\";s:24:\"Cập nhật Ngôn Ngữ\";s:13:\"language_name\";s:16:\"Tên Ngôn Ngữ\";s:4:\"code\";s:15:\"Mã Ngôn Ngữ\";s:4:\"sort\";s:11:\"Sắp xếp\";s:11:\"sort_holder\";s:31:\"Nhập thứ tự sắp xếp !\";s:11:\"name_holder\";s:25:\"Nhập tên Ngôn Ngữ !\";s:11:\"code_holder\";s:22:\"Nhập mã Ngôn Ngữ\";s:13:\"code_required\";s:34:\"Mã Ngôn Ngữ là bắt buộc !\";s:10:\"create_new\";s:11:\"Thêm mới\";s:16:\"name_en_required\";s:35:\"Tên Ngôn Ngữ là bắt buộc !\";s:7:\"success\";s:14:\"Thành Công !\";s:5:\"error\";s:7:\"Lỗi !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:14:\"status_changed\";s:41:\"đã được thay đổi trạng thái !\";s:10:\"ask_delete\";s:40:\"Bạn có muốn xóa ngôn ngữ này ?\";}', 'vi'),
(7, 4, 'Article Categories', 'a:37:{s:10:\"list_title\";s:23:\"Article Categories List\";s:18:\"article_categories\";s:18:\"Article Categories\";s:4:\"root\";s:24:\"Articale Categories List\";s:3:\"add\";s:3:\"Add\";s:4:\"edit\";s:4:\"Edit\";s:6:\"delete\";s:6:\"Delete\";s:10:\"create_new\";s:10:\"Create New\";s:6:\"parent\";s:6:\"Parent\";s:14:\"structure_name\";s:4:\"Name\";s:9:\"menu_icon\";s:4:\"Icon\";s:4:\"sort\";s:4:\"Sort\";s:21:\"structure_name_holder\";s:24:\"Input name of category !\";s:16:\"menu_icon_holder\";s:20:\"Input icon of menu !\";s:11:\"sort_holder\";s:19:\"Input Sort Number !\";s:9:\"page_type\";s:9:\"Page Type\";s:4:\"menu\";s:4:\"Menu\";s:8:\"category\";s:8:\"Category\";s:6:\"action\";s:6:\"Action\";s:6:\"status\";s:6:\"Status\";s:7:\"disable\";s:7:\"Disable\";s:6:\"enable\";s:6:\"Enable\";s:6:\"update\";s:6:\"Update\";s:12:\"back_to_list\";s:12:\"Back to List\";s:26:\"structure_name_vi_required\";s:18:\"Name id required !\";s:18:\"parent_id_required\";s:20:\"Parent is required !\";s:17:\"parent_id_numeric\";s:28:\"Parent ID must be a number !\";s:13:\"sort_required\";s:18:\"Sort is required !\";s:12:\"sort_numeric\";s:23:\"Sort must be a number !\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:12:\"delete_error\";s:30:\"have one or more child items !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:8:\"is_exist\";s:19:\"is already exists !\";s:16:\"error_permission\";s:40:\"You have not permissions for this page !\";}', 'en'),
(8, 4, 'Danh Mục Bài Viết', 'a:37:{s:10:\"list_title\";s:22:\"Danh Mục Bài Viết\";s:18:\"article_categories\";s:17:\"Nhóm Bài Viết\";s:4:\"root\";s:17:\"Nhóm Bài Viết\";s:3:\"add\";s:5:\"Thêm\";s:4:\"edit\";s:5:\"Sửa\";s:6:\"delete\";s:4:\"Xóa\";s:10:\"create_new\";s:11:\"Thêm Mới\";s:6:\"parent\";s:3:\"Cha\";s:14:\"structure_name\";s:4:\"Tên\";s:9:\"menu_icon\";s:15:\"Biểu Tượng\";s:4:\"sort\";s:11:\"Sắp Xếp\";s:21:\"structure_name_holder\";s:24:\"Nhập tên danh mục !\";s:16:\"menu_icon_holder\";s:22:\"Nhập Biểu Tượng\";s:11:\"sort_holder\";s:29:\"Nhập Thứ Tự Sắp Xếp\";s:9:\"page_type\";s:6:\"Kiểu\";s:4:\"menu\";s:4:\"Menu\";s:8:\"category\";s:10:\"Danh Mục\";s:6:\"action\";s:9:\"Tác Vụ\";s:6:\"status\";s:13:\"Trạng Thái\";s:7:\"disable\";s:15:\"Vô Hiệu Hóa\";s:6:\"enable\";s:14:\"Hoạt Động\";s:6:\"update\";s:12:\"Cập Nhật\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:26:\"structure_name_vi_required\";s:23:\"Tên là bắt buộc !\";s:18:\"parent_id_required\";s:27:\"Menu Cha là bắt buộc !\";s:17:\"parent_id_numeric\";s:29:\"Id Menu Cha phải là số !\";s:13:\"sort_required\";s:41:\"Thứ Tự Sắp Xếp là bắt buộc !\";s:12:\"sort_numeric\";s:40:\"Thứ Tự Sắp Xếp phải là số !\";s:7:\"success\";s:14:\"Thành Công !\";s:5:\"error\";s:7:\"Lỗi !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:12:\"delete_error\";s:35:\"có một hoặc nhiều item con !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:16:\"error_permission\";s:58:\"Bạn chưa được phân quyền truy cập trang này !\";}', 'vi'),
(9, 5, 'About', 'a:66:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"1VGjH0uGngtaLITGncFUiWOTYdVwc2VyVCI6cROW\";s:4:\"lang\";s:2:\"en\";s:10:\"list_title\";s:13:\"Products List\";s:4:\"show\";s:4:\"Show\";s:13:\"rows_per_page\";s:7:\"entries\";s:12:\"product_name\";s:4:\"Name\";s:4:\"unit\";s:4:\"Unit\";s:5:\"image\";s:5:\"Image\";s:5:\"group\";s:5:\"Group\";s:8:\"posision\";s:8:\"Posision\";s:4:\"sort\";s:4:\"Sort\";s:6:\"status\";s:6:\"Status\";s:6:\"action\";s:6:\"Action\";s:13:\"show_in_stock\";s:13:\"Show in Stock\";s:14:\"calculate_type\";s:14:\"Calculate Type\";s:10:\"processing\";s:10:\"Processing\";s:15:\"processing_unit\";s:15:\"Processing Unit\";s:15:\"actual_quantity\";s:15:\"Actual Quantity\";s:4:\"auto\";s:4:\"Auto\";s:9:\"hand_work\";s:9:\"Hand Work\";s:2:\"m2\";s:2:\"M2\";s:2:\"no\";s:2:\"No\";s:3:\"yes\";s:3:\"Yes\";s:7:\"disable\";s:7:\"Disable\";s:6:\"enable\";s:6:\"Enable\";s:4:\"next\";s:4:\"Next\";s:8:\"previous\";s:8:\"Previous\";s:10:\"lengthMenu\";s:31:\"Display _MENU_ records per page\";s:11:\"zeroRecords\";s:21:\"Nothing found - sorry\";s:4:\"info\";s:15:\"Delete selected\";s:9:\"infoEmpty\";s:20:\"No records available\";s:12:\"infoFiltered\";s:35:\"(filtered from _MAX_ total records)\";s:3:\"all\";s:3:\"All\";s:6:\"search\";s:6:\"Search\";s:14:\"delete_confirm\";s:37:\"Are you sure to delete this product ?\";s:21:\"delete_filter_confirm\";s:39:\"Are you sure to delete these products ?\";s:9:\"add_title\";s:11:\"Add Product\";s:19:\"product_name_holder\";s:20:\"Input product name !\";s:11:\"unit_holder\";s:23:\"Input unit of product !\";s:15:\"posision_holder\";s:27:\"Input posision of product !\";s:11:\"sort_holder\";s:19:\"Input sort number !\";s:22:\"processing_unit_holder\";s:32:\"Input unit of finished product !\";s:22:\"actual_quantity_holder\";s:34:\"Input actual quantity of product !\";s:10:\"create_new\";s:10:\"Create New\";s:12:\"back_to_list\";s:12:\"Back to List\";s:4:\"edit\";s:4:\"Edit\";s:6:\"update\";s:6:\"Update\";s:24:\"product_name_vi_required\";s:28:\"Product\'s name is required !\";s:18:\"parent_id_required\";s:19:\"Group is required !\";s:17:\"parent_id_numeric\";s:18:\"Group is invalid !\";s:16:\"unit_vi_required\";s:18:\"Unit is required !\";s:24:\"processing_unit_required\";s:29:\"Processing Unit is required !\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:12:\"delete_error\";s:62:\"This product has been use for Import,Export and Stock Report !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:8:\"is_exist\";s:18:\"is already exits !\";s:16:\"error_permission\";s:53:\"You have not permissions for products in this group !\";s:15:\"error_extension\";s:21:\"Invalid image style !\";s:11:\"error_image\";s:22:\"File must be a image !\";s:14:\"status_changed\";s:25:\"status has been changed !\";s:16:\"products_checked\";s:17:\"Selected products\";}', 'en'),
(10, 5, 'Giới Thiệu', 'a:66:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"1VGjH0uGngtaLITGncFUiWOTYdVwc2VyVCI6cROW\";s:4:\"lang\";s:2:\"vi\";s:10:\"list_title\";s:23:\"Danh sách Sản Phẩm\";s:4:\"show\";s:3:\"Xem\";s:13:\"rows_per_page\";s:17:\"dòng mỗi trang\";s:12:\"product_name\";s:17:\"Tên sản phẩm\";s:4:\"unit\";s:16:\"Đơn vị tính\";s:5:\"image\";s:5:\"Hình\";s:5:\"group\";s:5:\"Nhóm\";s:8:\"posision\";s:9:\"Vị trí\";s:4:\"sort\";s:11:\"Sắp Xếp\";s:6:\"status\";s:13:\"Trạng Thái\";s:6:\"action\";s:9:\"Tác Vụ\";s:13:\"show_in_stock\";s:19:\"Theo Dõi Tồn Kho\";s:14:\"calculate_type\";s:18:\"Loại Trừ Hàng\";s:10:\"processing\";s:9:\"Gia Công\";s:15:\"processing_unit\";s:20:\"Đơn vị gia công\";s:15:\"actual_quantity\";s:16:\"Tồn chi tiết\";s:4:\"auto\";s:12:\"Tự động\";s:9:\"hand_work\";s:11:\"Thủ Công\";s:2:\"m2\";s:8:\"Tính M2\";s:2:\"no\";s:6:\"Không\";s:3:\"yes\";s:3:\"Có\";s:7:\"disable\";s:15:\"Vô Hiệu Hóa\";s:6:\"enable\";s:14:\"Hoạt Động\";s:4:\"next\";s:3:\"Sau\";s:8:\"previous\";s:8:\"Trước\";s:10:\"lengthMenu\";s:28:\"Xem _MENU_ dòng mỗi trang\";s:11:\"zeroRecords\";s:34:\"Không có kết quả tìm kiếm\";s:4:\"info\";s:22:\"Xóa mục đã chọn\";s:9:\"infoEmpty\";s:22:\"Không có dữ liệu\";s:12:\"infoFiltered\";s:36:\"(Lọc từ _MAX_ tổng số dòng)\";s:3:\"all\";s:10:\"Tất cả\";s:6:\"search\";s:11:\"Tìm kiếm\";s:14:\"delete_confirm\";s:37:\"Bạn muốn xóa sản phẩm này ?\";s:21:\"delete_filter_confirm\";s:52:\"Bạn muốn xóa những sản phẩm đã chọn ?\";s:9:\"add_title\";s:18:\"Thêm Sản Phẩm\";s:19:\"product_name_holder\";s:26:\"Nhập tên sản phẩm !\";s:11:\"unit_holder\";s:25:\"Nhập đơn vị tính !\";s:15:\"posision_holder\";s:31:\"Nhập vị trí sản phẩm !\";s:11:\"sort_holder\";s:31:\"Nhập thứ tự sắp xếp !\";s:22:\"processing_unit_holder\";s:44:\"Nhập đơn vị tính theo thành phẩm !\";s:22:\"actual_quantity_holder\";s:51:\"Nhập số lượng tồn chi tiết thực tế !\";s:10:\"create_new\";s:11:\"Thêm mới\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:4:\"edit\";s:13:\"Chỉnh sửa\";s:6:\"update\";s:12:\"Cập nhật\";s:24:\"product_name_vi_required\";s:36:\"Tên sản phẩm là bắt buộc !\";s:18:\"parent_id_required\";s:37:\"Nhóm sản phẩm là bắt buộc !\";s:17:\"parent_id_numeric\";s:38:\"Nhóm sản phẩm không hợp lệ !\";s:16:\"unit_vi_required\";s:35:\"Đơn vị tính là bắt buộc !\";s:24:\"processing_unit_required\";s:39:\"Đơn vị gia công là bắt buộc !\";s:7:\"success\";s:14:\"Thành Công !\";s:5:\"error\";s:7:\"Lỗi !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:12:\"delete_error\";s:72:\"Sản phẩm này đang được sử dụng trong xuất nhập tồn !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:16:\"error_permission\";s:71:\"Bạn chưa được phân quyền quản lý nhóm sản phẩm này !\";s:15:\"error_extension\";s:47:\"File hình ảnh không đúng định dạng !\";s:11:\"error_image\";s:47:\"File hình ảnh không phải là file hình !\";s:14:\"status_changed\";s:41:\"đã được thay đổi trạng thái !\";s:16:\"products_checked\";s:32:\"Những sản phẩm đã chọn\";}', 'vi'),
(11, 6, 'Users', 'a:62:{s:10:\"list_title\";s:10:\"Users List\";s:6:\"avatar\";s:6:\"Avatar\";s:9:\"full_name\";s:9:\"Full Name\";s:5:\"email\";s:5:\"Email\";s:10:\"last_login\";s:10:\"Last Login\";s:6:\"status\";s:6:\"Status\";s:6:\"action\";s:6:\"Action\";s:10:\"ask_delete\";s:34:\"Are you sure to delete this user ?\";s:9:\"add_title\";s:15:\"Create New User\";s:7:\"address\";s:7:\"Address\";s:14:\"address_holder\";s:20:\"Input your address !\";s:6:\"idCard\";s:16:\"ID Card/Passport\";s:13:\"idCard_holder\";s:38:\"Input identify card or passport number\";s:12:\"upload_image\";s:12:\"Upload Image\";s:12:\"phone_number\";s:12:\"Phone Number\";s:19:\"phone_number_holder\";s:25:\"Input your phone number !\";s:5:\"reset\";s:5:\"Reset\";s:8:\"password\";s:8:\"Password\";s:11:\"permissions\";s:11:\"Permissions\";s:8:\"language\";s:8:\"Language\";s:5:\"group\";s:5:\"Group\";s:12:\"choose_group\";s:17:\"Choose User Group\";s:9:\"warehouse\";s:9:\"Warehouse\";s:7:\"extends\";s:13:\"Extends Users\";s:14:\"extends_holder\";s:31:\"Input users whoes extend from !\";s:4:\"none\";s:4:\"None\";s:16:\"menus_permission\";s:16:\"Menus Permission\";s:18:\"classes_permission\";s:18:\"Classes Permission\";s:6:\"enable\";s:6:\"Enable\";s:7:\"disable\";s:7:\"Disable\";s:10:\"create_new\";s:10:\"Create New\";s:16:\"full_name_holder\";s:15:\"Input Full Name\";s:15:\"password_holder\";s:14:\"Input Password\";s:12:\"email_holder\";s:28:\"Input Email type @stdsvn.com\";s:18:\"full_name_required\";s:23:\"Full Name is required !\";s:14:\"email_required\";s:19:\"Email is required !\";s:11:\"email_email\";s:44:\"Email must be in the format xxx@stdsvn.com !\";s:17:\"password_required\";s:22:\"Password is required !\";s:12:\"password_min\";s:40:\"Password must be at least 8 characters !\";s:8:\"max_size\";s:43:\"Allowed JPG, GIF or PNG. Max size of 800K !\";s:15:\"choose_language\";s:23:\"Choose default language\";s:10:\"edit_title\";s:9:\"Edit User\";s:6:\"update\";s:6:\"Update\";s:12:\"back_to_list\";s:12:\"Back to List\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:8:\"is_exist\";s:18:\"is already exits !\";s:15:\"error_extension\";s:21:\"Invalid image style !\";s:11:\"error_image\";s:24:\"Avatar must be a image !\";s:14:\"status_changed\";s:25:\"status has been changed !\";s:16:\"list_group_title\";s:16:\"Users Group List\";s:15:\"add_group_title\";s:13:\"Add New Group\";s:16:\"edit_group_title\";s:16:\"Edit Users Group\";s:4:\"name\";s:4:\"Name\";s:11:\"name_holder\";s:16:\"Input Group name\";s:13:\"name_required\";s:24:\"Group name is required !\";s:14:\"group_required\";s:19:\"Choose user group !\";s:9:\"is_denied\";s:11:\"is denied !\";}', 'en'),
(12, 6, 'Người Dùng', 'a:62:{s:10:\"list_title\";s:25:\"Danh sách Người Dùng\";s:6:\"avatar\";s:19:\"Ảnh Đại Diện\";s:9:\"full_name\";s:9:\"Họ Tên\";s:5:\"email\";s:5:\"Email\";s:10:\"last_login\";s:26:\"Đăng Nhập Gần Nhất\";s:6:\"status\";s:13:\"Trạng Thái\";s:6:\"action\";s:9:\"Tác Vụ\";s:10:\"ask_delete\";s:57:\"Bạn có chắc chắn muốn xóa người dùng này ?\";s:9:\"add_title\";s:26:\"Thêm Người Dùng Mới\";s:7:\"address\";s:12:\"Địa chỉ\";s:14:\"address_holder\";s:33:\"Nhập địa chỉ của bạn !\";s:6:\"idCard\";s:12:\"CCCD/Passort\";s:13:\"idCard_holder\";s:34:\"Nhập số CCCD hoặc Passport !\";s:12:\"upload_image\";s:12:\"Upload Ảnh\";s:12:\"phone_number\";s:20:\"Số điện thoại\";s:19:\"phone_number_holder\";s:41:\"Nhập số điện thoại của bạn !\";s:5:\"reset\";s:11:\"Bỏ chọn\";s:8:\"password\";s:12:\"Mật Khẩu\";s:11:\"permissions\";s:13:\"Phân Quyền\";s:8:\"language\";s:11:\"Ngôn Ngữ\";s:5:\"group\";s:5:\"Nhóm\";s:12:\"choose_group\";s:27:\"Chọn nhóm người dùng\";s:9:\"warehouse\";s:14:\"Kho quản lý\";s:7:\"extends\";s:26:\"Kế thừa Người Dùng\";s:14:\"extends_holder\";s:44:\"Nhập người dùng được kế thừa !\";s:4:\"none\";s:6:\"Không\";s:16:\"menus_permission\";s:18:\"Phân Quyền Menu\";s:18:\"classes_permission\";s:25:\"Phân quyền Lớp Học\";s:6:\"enable\";s:14:\"Hoạt Động\";s:7:\"disable\";s:15:\"Vô Hiệu Hóa\";s:10:\"create_new\";s:11:\"Thêm Mới\";s:16:\"full_name_holder\";s:16:\"Nhập họ tên\";s:15:\"password_holder\";s:19:\"Nhập mật khẩu\";s:12:\"email_holder\";s:12:\"Nhập email\";s:18:\"full_name_required\";s:28:\"Họ tên là bắt buộc !\";s:14:\"email_required\";s:24:\"Email là bắt buộc !\";s:11:\"email_email\";s:46:\"Email phải có định dạng xxx@stdsvn.com\";s:17:\"password_required\";s:31:\"Mật khẩu là bắt buộc !\";s:12:\"password_min\";s:47:\"Mật khẩu phải có ít nhất 8 ký tự !\";s:8:\"max_size\";s:66:\"File ảnh JPG, GIF hoặc PNG. Dung lượng tối đa là 800K !\";s:15:\"choose_language\";s:32:\"Chọn ngôn ngữ mặc định\";s:10:\"edit_title\";s:37:\"Thay đổi thông tin Người Dùng\";s:6:\"update\";s:12:\"Cập Nhật\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:7:\"success\";s:14:\"Thành công !\";s:5:\"error\";s:7:\"Lỗi !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:15:\"error_extension\";s:42:\"File avatar không đúng định dạng !\";s:11:\"error_image\";s:36:\"Avatar phải là file hình ảnh !\";s:14:\"status_changed\";s:41:\"đã được thay đổi trạng thái !\";s:16:\"list_group_title\";s:31:\"Danh sách Nhóm Người Dùng\";s:15:\"add_group_title\";s:26:\"Thêm Nhóm Người Dùng\";s:16:\"edit_group_title\";s:33:\"Tùy chỉnh Nhóm Người Dùng\";s:4:\"name\";s:4:\"Tên\";s:11:\"name_holder\";s:17:\"Nhập tên nhóm\";s:13:\"name_required\";s:29:\"Tên nhóm là bắt buộc !\";s:14:\"group_required\";s:29:\"Chọn nhóm người dùng !\";s:9:\"is_denied\";s:18:\"bị từ chối !\";}', 'vi'),
(13, 7, 'Account', 'a:30:{s:10:\"create_new\";s:10:\"Create New\";s:10:\"show_title\";s:12:\"User Profile\";s:9:\"full_name\";s:9:\"Full Name\";s:5:\"email\";s:5:\"Email\";s:8:\"password\";s:8:\"Password\";s:7:\"address\";s:7:\"Address\";s:14:\"address_holder\";s:20:\"Input your address !\";s:12:\"upload_image\";s:12:\"Upload Image\";s:5:\"reset\";s:5:\"Reset\";s:4:\"edit\";s:4:\"Edit\";s:10:\"edit_title\";s:12:\"Edit Account\";s:16:\"full_name_holder\";s:20:\"Input Your Full Name\";s:12:\"email_holder\";s:16:\"Input Your Email\";s:15:\"password_holder\";s:45:\"Input Your Password with at least 8 character\";s:18:\"full_name_required\";s:23:\"Full Name is required !\";s:14:\"email_required\";s:19:\"Email is required !\";s:11:\"email_email\";s:44:\"Email must be in the format xxx@stdsvn.com !\";s:17:\"password_required\";s:22:\"Password is required !\";s:12:\"password_min\";s:40:\"Password must be at least 8 characters !\";s:12:\"phone_number\";s:12:\"Phone Number\";s:19:\"phone_number_holder\";s:25:\"Input your phone number !\";s:6:\"update\";s:6:\"Update\";s:12:\"back_to_home\";s:12:\"Back to Home\";s:9:\"not_exist\";s:29:\"Your account does not exist !\";s:10:\"email_used\";s:15:\"has been used !\";s:7:\"success\";s:9:\"Success !\";s:14:\"update_success\";s:18:\"has been updated !\";s:5:\"error\";s:7:\"Error !\";s:8:\"max_size\";s:43:\"Allowed JPG, GIF or PNG. Max size of 800K !\";s:15:\"choose_language\";s:23:\"Choose default language\";}', 'en'),
(14, 7, 'Tài Khoản', 'a:30:{s:10:\"create_new\";s:11:\"Thêm Mới\";s:10:\"show_title\";s:23:\"Thông Tin Tài Khoản\";s:9:\"full_name\";s:9:\"Họ Tên\";s:5:\"email\";s:5:\"Email\";s:8:\"password\";s:12:\"Mật Khẩu\";s:7:\"address\";s:12:\"Địa chỉ\";s:14:\"address_holder\";s:33:\"Nhập địa chỉ của bạn !\";s:12:\"upload_image\";s:12:\"Upload Ảnh\";s:5:\"reset\";s:11:\"Bỏ chọn\";s:4:\"edit\";s:5:\"Sửa\";s:10:\"edit_title\";s:25:\"Cập nhật Tài Khoản\";s:16:\"full_name_holder\";s:30:\"Nhập họ tên của bạn !\";s:12:\"email_holder\";s:39:\"Nhập địa chỉ email của bạn !\";s:15:\"password_holder\";s:49:\"Nhập mật khẩu với ít nhất 8 ký tự !\";s:18:\"full_name_required\";s:28:\"Họ tên là bắt buộc !\";s:14:\"email_required\";s:24:\"Email là bắt buộc !\";s:11:\"email_email\";s:46:\"Email phải có định dạng xxx@stdsvn.com\";s:17:\"password_required\";s:31:\"Mật khẩu là bắt buộc !\";s:12:\"password_min\";s:47:\"Mật khẩu phải có ít nhất 8 ký tự !\";s:12:\"phone_number\";s:20:\"Số điện thoại\";s:19:\"phone_number_holder\";s:41:\"Nhập số điện thoại của bạn !\";s:6:\"update\";s:12:\"Cập nhật\";s:12:\"back_to_home\";s:21:\"Quay về Trang Chủ\";s:9:\"not_exist\";s:33:\"Tài khoản không tồn tại !\";s:10:\"email_used\";s:27:\"đã được sử dụng !\";s:7:\"success\";s:14:\"Thành công !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:5:\"error\";s:7:\"Lỗi !\";s:8:\"max_size\";s:66:\"File ảnh JPG, GIF hoặc PNG. Dung lượng tối đa là 800K !\";s:15:\"choose_language\";s:32:\"Chọn ngôn ngữ mặc định\";}', 'vi'),
(15, 8, 'Change Language', '', 'en'),
(16, 8, 'Đổi Ngôn Ngữ', '', 'vi'),
(17, 9, 'Login', '', 'en'),
(18, 9, 'Đăng nhập', '', 'vi'),
(19, 10, 'Register', '', 'en'),
(20, 10, 'Đăng ký', '', 'vi'),
(21, 11, 'Forgot Password', '', 'en'),
(22, 11, 'Quên mật khẩu', '', 'vi'),
(23, 12, 'Add', '', 'en'),
(24, 12, 'Thêm', '', 'vi'),
(25, 13, 'Edit', '', 'en'),
(26, 13, 'Sửa', '', 'vi'),
(27, 14, 'Delete', '', 'en'),
(28, 14, 'Xóa', '', 'vi'),
(29, 15, 'Languages', '', 'en'),
(30, 15, 'Ngôn Ngữ', '', 'vi'),
(31, 16, 'Translations', '', 'en'),
(32, 16, 'Bản Dịch', '', 'vi'),
(33, 17, 'Edit', '', 'en'),
(34, 17, 'Sửa', '', 'vi'),
(35, 21, 'Users Group', '', 'en'),
(36, 21, 'Nhóm Người Dùng', '', 'vi'),
(37, 22, 'Users List', '', 'en'),
(38, 22, 'Danh sách Người Dùng', '', 'vi'),
(39, 18, 'Add', '', 'en'),
(40, 18, 'Thêm', '', 'vi'),
(41, 19, 'Edit', '', 'en'),
(42, 19, 'Sửa', '', 'vi'),
(43, 20, 'Delete', '', 'en'),
(44, 20, 'Xóa', '', 'vi'),
(45, 26, 'Add', '', 'en'),
(46, 26, 'Thêm', '', 'vi'),
(47, 27, 'Edit', '', 'en'),
(48, 27, 'Sửa', '', 'vi'),
(49, 28, 'Delete', '', 'en'),
(50, 28, 'Xóa', '', 'vi'),
(51, 23, 'Add', '', 'en'),
(52, 23, 'Thêm', '', 'vi'),
(53, 24, 'Edit', '', 'en'),
(54, 24, 'Sửa', '', 'vi'),
(55, 25, 'Delete', '', 'en'),
(56, 25, 'Xóa', '', 'vi'),
(57, 29, 'EN', '', 'en'),
(58, 29, 'EN', '', 'vi'),
(59, 30, 'VI', '', 'en'),
(60, 30, 'VI', '', 'vi'),
(61, 31, 'Change Status', '', 'en'),
(62, 31, 'Đổi Trạng Thái', '', 'vi'),
(63, 32, 'Change Status', '', 'en'),
(64, 32, 'Đổi Trạng Thái', '', 'vi'),
(65, 33, 'Change Status', '', 'en'),
(66, 33, 'Đổi Trạng Thái', '', 'vi'),
(67, 34, 'Edit', '', 'en'),
(68, 34, 'Sửa', '', 'vi'),
(89, 45, 'Add', '', 'en'),
(90, 45, 'Thêm', '', 'vi'),
(91, 46, 'Edit', '', 'en'),
(93, 47, 'Delete', '', 'en'),
(94, 47, 'Xóa', '', 'vi'),
(97, 49, 'Đổi Trạng Thái', '', 'vi'),
(98, 49, 'Change Status', '', 'en'),
(99, 50, 'Học viên', 'a:70:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"of8aTon55pctWyBvxZdwTMkV2Gr1XDRnStmTRVPl\";s:4:\"lang\";s:2:\"vi\";s:10:\"list_title\";s:21:\"Danh sách Nhập Kho\";s:4:\"show\";s:3:\"Xem\";s:3:\"all\";s:10:\"Tất cả\";s:13:\"rows_per_page\";s:17:\"dòng mỗi trang\";s:6:\"search\";s:11:\"Tìm kiếm\";s:6:\"action\";s:9:\"Tác vụ\";s:8:\"previous\";s:8:\"Trước\";s:4:\"next\";s:3:\"Sau\";s:14:\"delete_confirm\";s:55:\"Bạn có chắc chắn muốn xóa chứng từ này ?\";s:21:\"delete_filter_confirm\";s:70:\"Bạn có chắc chắn muốn xóa những chứng từ đã chọn ?\";s:13:\"delete_filter\";s:22:\"Xóa mục đã chọn\";s:9:\"add_title\";s:11:\"Thêm mới\";s:10:\"edit_title\";s:12:\"Cập nhật\";s:4:\"date\";s:5:\"Ngày\";s:8:\"salesman\";s:14:\"Mã kinh doanh\";s:8:\"supplier\";s:15:\"Nhà cung cấp\";s:6:\"number\";s:17:\"Số chứng từ\";s:11:\"import_type\";s:13:\"Kiểu nhập\";s:3:\"buy\";s:10:\"Nhập mua\";s:6:\"return\";s:11:\"Trả lại\";s:16:\"change_warehouse\";s:12:\"Chuyển kho\";s:4:\"note\";s:8:\"Ghi chú\";s:12:\"product_name\";s:17:\"Tên sản phẩm\";s:4:\"unit\";s:4:\"ĐVT\";s:14:\"specifications\";s:9:\"Quy cách\";s:5:\"stock\";s:5:\"Tồn\";s:8:\"quantity\";s:13:\"Số lượng\";s:15:\"actual_quantity\";s:17:\"Chi tiết nhập\";s:12:\"actual_stock\";s:17:\"Tồn thực tế\";s:5:\"price\";s:10:\"Đơn giá\";s:6:\"amount\";s:13:\"Thành tiền\";s:5:\"total\";s:13:\"Tổng tiền\";s:6:\"detail\";s:10:\"Chi tiết\";s:7:\"add_row\";s:11:\"Thêm dòng\";s:15:\"remove_last_row\";s:17:\"Xóa dòng cuối\";s:11:\"date_holder\";s:21:\"Chọn ngày nhập !\";s:15:\"salesman_holder\";s:35:\"Nhập mã nhân viên bán hàng !\";s:15:\"supplier_holder\";s:29:\"Nhập tên nhà cung cấp !\";s:13:\"number_holder\";s:26:\"Nhập số chứng từ !\";s:10:\"create_new\";s:11:\"Tạo mới\";s:6:\"update\";s:12:\"Cập nhật\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:7:\"success\";s:14:\"Thành công !\";s:5:\"error\";s:7:\"Lỗi !\";s:12:\"name_error_1\";s:24:\"Sản phẩm tại dòng\";s:12:\"name_error_2\";s:53:\"không tồn tại ! Hãy chọn sản phẩm khác !\";s:16:\"quantity_error_1\";s:25:\"Số lượng tại dòng\";s:16:\"quantity_error_2\";s:41:\"phải là số ! Hãy kiểm tra lại !\";s:9:\"is_zero_1\";s:25:\"Số lượng tại dòng\";s:9:\"is_zero_2\";s:78:\"bằng 0 và sẽ không được lưu lại ! Bạn có muốn tiếp tục ?\";s:13:\"price_error_1\";s:22:\"Đơn giá tại dòng\";s:13:\"price_error_2\";s:41:\"phải là số ! Hãy kiểm tra lại !\";s:13:\"date_required\";s:31:\"Ngày nhập là bắt buộc !\";s:17:\"salesman_required\";s:41:\"Nhân viên bán hàng là bắt buộc !\";s:17:\"supplier_required\";s:34:\"Nhà cung cấp là bắt buộc !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:17:\"product_not_exist\";s:51:\"Sản phẩm tại dòng _NUM_ không tồn tại !\";s:16:\"import_not_exist\";s:20:\"không tồn tại !\";s:15:\"number_is_exist\";s:18:\"đã tồn tại !\";s:15:\"staff_not_exist\";s:47:\"Mã nhân viên kinh doanh không tồn tại !\";s:18:\"supplier_not_exist\";s:36:\"Nhà cung cấp không tồn tại !\";s:16:\"error_permission\";s:71:\"Bạn chưa được phân quyền quản lý nhóm sản phẩm này !\";s:14:\"status_changed\";s:41:\"đã được thay đổi trạng thái !\";s:14:\"import_checked\";s:35:\"Chứng từ nhập kho đã chọn\";}', 'vi'),
(100, 50, 'Students', 'a:70:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"of8aTon55pctWyBvxZdwTMkV2Gr1XDRnStmTRVPl\";s:4:\"lang\";s:2:\"en\";s:10:\"list_title\";s:11:\"Import List\";s:4:\"show\";s:4:\"Show\";s:3:\"all\";s:3:\"All\";s:13:\"rows_per_page\";s:7:\"entries\";s:6:\"search\";s:6:\"Search\";s:6:\"action\";s:6:\"Action\";s:8:\"previous\";s:8:\"Previous\";s:4:\"next\";s:4:\"Next\";s:14:\"delete_confirm\";s:38:\"Are you sure to delete this document ?\";s:21:\"delete_filter_confirm\";s:40:\"Are you sure to delete these documents ?\";s:13:\"delete_filter\";s:15:\"Delete selected\";s:9:\"add_title\";s:10:\"Create New\";s:10:\"edit_title\";s:4:\"Edit\";s:4:\"date\";s:4:\"Date\";s:8:\"salesman\";s:8:\"Salesman\";s:8:\"supplier\";s:8:\"Supplier\";s:6:\"number\";s:6:\"Number\";s:11:\"import_type\";s:11:\"Import Type\";s:3:\"buy\";s:3:\"Buy\";s:6:\"return\";s:6:\"Return\";s:16:\"change_warehouse\";s:16:\"Change Warehouse\";s:4:\"note\";s:4:\"Note\";s:12:\"product_name\";s:12:\"Product name\";s:4:\"unit\";s:4:\"Unit\";s:14:\"specifications\";s:14:\"Specifications\";s:5:\"stock\";s:5:\"Stock\";s:8:\"quantity\";s:8:\"Quantity\";s:15:\"actual_quantity\";s:10:\"Actual Qty\";s:12:\"actual_stock\";s:12:\"Actual Stock\";s:5:\"price\";s:5:\"Price\";s:6:\"amount\";s:6:\"Amount\";s:5:\"total\";s:5:\"Total\";s:6:\"detail\";s:7:\"Details\";s:7:\"add_row\";s:7:\"Add row\";s:15:\"remove_last_row\";s:15:\"Remove last Row\";s:11:\"date_holder\";s:20:\"Choose import date !\";s:15:\"salesman_holder\";s:21:\"Input salesman code !\";s:15:\"supplier_holder\";s:21:\"Input supplier name !\";s:13:\"number_holder\";s:14:\"Input number !\";s:10:\"create_new\";s:10:\"Create New\";s:6:\"update\";s:6:\"Update\";s:12:\"back_to_list\";s:12:\"Back to List\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:12:\"name_error_1\";s:22:\"Product name at row(s)\";s:12:\"name_error_2\";s:14:\"is not exist !\";s:16:\"quantity_error_1\";s:18:\"Quantity at row(s)\";s:16:\"quantity_error_2\";s:19:\"must be a numeric !\";s:9:\"is_zero_1\";s:18:\"Quantity at row(s)\";s:9:\"is_zero_2\";s:56:\"is zero and will be deleted ! Are you sure to continue ?\";s:13:\"price_error_1\";s:15:\"Price at row(s)\";s:13:\"price_error_2\";s:19:\"must be a numeric !\";s:13:\"date_required\";s:18:\"Date is required !\";s:17:\"salesman_required\";s:22:\"Salesman is required !\";s:17:\"supplier_required\";s:22:\"Supplier is required !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:17:\"product_not_exist\";s:38:\"Product at line _NUM_ does not exist !\";s:16:\"import_not_exist\";s:21:\"Import is not exist !\";s:15:\"number_is_exist\";s:22:\"Number already exist !\";s:15:\"staff_not_exist\";s:22:\"Saleman is not exist !\";s:18:\"supplier_not_exist\";s:23:\"Supplier is not exist !\";s:16:\"error_permission\";s:53:\"You have not permissions for products in this group !\";s:14:\"status_changed\";s:25:\"status has been changed !\";s:14:\"import_checked\";s:16:\"Selected Imports\";}', 'en'),
(101, 51, 'Thêm', '', 'vi'),
(102, 51, 'Add', '', 'en'),
(103, 52, 'Sửa', '', 'vi'),
(104, 52, 'Edit', '', 'en'),
(105, 53, 'Xóa', '', 'vi'),
(106, 53, 'Delete', '', 'en'),
(107, 54, 'Xuất Kho', 'a:70:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"of8aTon55pctWyBvxZdwTMkV2Gr1XDRnStmTRVPl\";s:4:\"lang\";s:2:\"vi\";s:10:\"list_title\";s:21:\"Danh sách Xuất Kho\";s:4:\"show\";s:3:\"Xem\";s:3:\"all\";s:10:\"Tất cả\";s:13:\"rows_per_page\";s:17:\"dòng mỗi trang\";s:6:\"search\";s:11:\"Tìm kiếm\";s:6:\"action\";s:9:\"Tác vụ\";s:8:\"previous\";s:8:\"Trước\";s:4:\"next\";s:3:\"Sau\";s:14:\"delete_confirm\";s:55:\"Bạn có chắc chắn muốn xóa chứng từ này ?\";s:21:\"delete_filter_confirm\";s:70:\"Bạn có chắc chắn muốn xóa những chứng từ đã chọn ?\";s:13:\"delete_filter\";s:22:\"Xóa mục đã chọn\";s:9:\"add_title\";s:11:\"Thêm mới\";s:10:\"edit_title\";s:12:\"Cập nhật\";s:4:\"date\";s:5:\"Ngày\";s:8:\"salesman\";s:14:\"Mã kinh doanh\";s:8:\"supplier\";s:12:\"Khách hàng\";s:6:\"number\";s:17:\"Số chứng từ\";s:11:\"export_type\";s:13:\"Kiểu xuất\";s:4:\"sell\";s:4:\"Bán\";s:7:\"destroy\";s:10:\"Hủy bỏ\";s:16:\"change_warehouse\";s:12:\"Chuyển kho\";s:4:\"note\";s:8:\"Ghi chú\";s:12:\"product_name\";s:17:\"Tên sản phẩm\";s:4:\"unit\";s:4:\"ĐVT\";s:14:\"specifications\";s:9:\"Quy cách\";s:5:\"stock\";s:5:\"Tồn\";s:8:\"quantity\";s:13:\"Số lượng\";s:15:\"actual_quantity\";s:17:\"Chi tiết nhập\";s:12:\"actual_stock\";s:17:\"Tồn thực tế\";s:5:\"price\";s:10:\"Đơn giá\";s:6:\"amount\";s:13:\"Thành tiền\";s:5:\"total\";s:13:\"Tổng tiền\";s:6:\"detail\";s:10:\"Chi tiết\";s:7:\"add_row\";s:11:\"Thêm dòng\";s:15:\"remove_last_row\";s:17:\"Xóa dòng cuối\";s:11:\"date_holder\";s:21:\"Chọn ngày xuất !\";s:15:\"salesman_holder\";s:35:\"Nhập mã nhân viên bán hàng !\";s:15:\"supplier_holder\";s:26:\"Nhập tên khách hàng !\";s:13:\"number_holder\";s:26:\"Nhập số chứng từ !\";s:10:\"create_new\";s:11:\"Tạo mới\";s:6:\"update\";s:12:\"Cập nhật\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:7:\"success\";s:14:\"Thành công !\";s:5:\"error\";s:7:\"Lỗi !\";s:12:\"name_error_1\";s:24:\"Sản phẩm tại dòng\";s:12:\"name_error_2\";s:53:\"không tồn tại ! Hãy chọn sản phẩm khác !\";s:16:\"quantity_error_1\";s:25:\"Số lượng tại dòng\";s:16:\"quantity_error_2\";s:41:\"phải là số ! Hãy kiểm tra lại !\";s:9:\"is_zero_1\";s:25:\"Số lượng tại dòng\";s:9:\"is_zero_2\";s:78:\"bằng 0 và sẽ không được lưu lại ! Bạn có muốn tiếp tục ?\";s:13:\"price_error_1\";s:22:\"Đơn giá tại dòng\";s:13:\"price_error_2\";s:41:\"phải là số ! Hãy kiểm tra lại !\";s:13:\"date_required\";s:31:\"Ngày nhập là bắt buộc !\";s:17:\"salesman_required\";s:41:\"Nhân viên bán hàng là bắt buộc !\";s:17:\"supplier_required\";s:34:\"Nhà cung cấp là bắt buộc !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:17:\"product_not_exist\";s:51:\"Sản phẩm tại dòng _NUM_ không tồn tại !\";s:16:\"export_not_exist\";s:20:\"không tồn tại !\";s:15:\"number_is_exist\";s:18:\"đã tồn tại !\";s:15:\"staff_not_exist\";s:47:\"Mã nhân viên kinh doanh không tồn tại !\";s:18:\"supplier_not_exist\";s:33:\"Khách hàng không tồn tại !\";s:16:\"error_permission\";s:71:\"Bạn chưa được phân quyền quản lý nhóm sản phẩm này !\";s:14:\"status_changed\";s:41:\"đã được thay đổi trạng thái !\";s:14:\"export_checked\";s:35:\"Chứng từ xuất kho đã chọn\";}', 'vi'),
(108, 54, 'Export', 'a:70:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"of8aTon55pctWyBvxZdwTMkV2Gr1XDRnStmTRVPl\";s:4:\"lang\";s:2:\"en\";s:10:\"list_title\";s:11:\"Export List\";s:4:\"show\";s:4:\"Show\";s:3:\"all\";s:3:\"All\";s:13:\"rows_per_page\";s:7:\"entries\";s:6:\"search\";s:6:\"Search\";s:6:\"action\";s:6:\"Action\";s:8:\"previous\";s:8:\"Previous\";s:4:\"next\";s:4:\"Next\";s:14:\"delete_confirm\";s:38:\"Are you sure to delete this document ?\";s:21:\"delete_filter_confirm\";s:40:\"Are you sure to delete these documents ?\";s:13:\"delete_filter\";s:15:\"Delete selected\";s:9:\"add_title\";s:10:\"Create New\";s:10:\"edit_title\";s:4:\"Edit\";s:4:\"date\";s:4:\"Date\";s:8:\"salesman\";s:8:\"Salesman\";s:8:\"supplier\";s:8:\"Customer\";s:6:\"number\";s:6:\"Number\";s:11:\"export_type\";s:11:\"Export Type\";s:4:\"sell\";s:4:\"Sell\";s:7:\"destroy\";s:7:\"Destroy\";s:16:\"change_warehouse\";s:16:\"Change Warehouse\";s:4:\"note\";s:4:\"Note\";s:12:\"product_name\";s:12:\"Product name\";s:4:\"unit\";s:4:\"Unit\";s:14:\"specifications\";s:14:\"Specifications\";s:5:\"stock\";s:5:\"Stock\";s:8:\"quantity\";s:8:\"Quantity\";s:15:\"actual_quantity\";s:10:\"Actual Qty\";s:12:\"actual_stock\";s:12:\"Actual Stock\";s:5:\"price\";s:5:\"Price\";s:6:\"amount\";s:6:\"Amount\";s:5:\"total\";s:5:\"Total\";s:6:\"detail\";s:7:\"Details\";s:7:\"add_row\";s:7:\"Add row\";s:15:\"remove_last_row\";s:15:\"Remove last Row\";s:11:\"date_holder\";s:20:\"Choose export date !\";s:15:\"salesman_holder\";s:21:\"Input salesman code !\";s:15:\"supplier_holder\";s:21:\"Input customer name !\";s:13:\"number_holder\";s:14:\"Input number !\";s:10:\"create_new\";s:10:\"Create New\";s:6:\"update\";s:6:\"Update\";s:12:\"back_to_list\";s:12:\"Back to List\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:12:\"name_error_1\";s:22:\"Product name at row(s)\";s:12:\"name_error_2\";s:14:\"is not exist !\";s:16:\"quantity_error_1\";s:18:\"Quantity at row(s)\";s:16:\"quantity_error_2\";s:19:\"must be a numeric !\";s:9:\"is_zero_1\";s:18:\"Quantity at row(s)\";s:9:\"is_zero_2\";s:56:\"is zero and will be deleted ! Are you sure to continue ?\";s:13:\"price_error_1\";s:15:\"Price at row(s)\";s:13:\"price_error_2\";s:19:\"must be a numeric !\";s:13:\"date_required\";s:18:\"Date is required !\";s:17:\"salesman_required\";s:22:\"Salesman is required !\";s:17:\"supplier_required\";s:22:\"Customer is required !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:17:\"product_not_exist\";s:38:\"Product at line _NUM_ does not exist !\";s:16:\"export_not_exist\";s:21:\"Export is not exist !\";s:15:\"number_is_exist\";s:22:\"Number already exist !\";s:15:\"staff_not_exist\";s:22:\"Saleman is not exist !\";s:18:\"supplier_not_exist\";s:23:\"Customer is not exist !\";s:16:\"error_permission\";s:53:\"You have not permissions for products in this group !\";s:14:\"status_changed\";s:25:\"status has been changed !\";s:14:\"export_checked\";s:16:\"Selected Exports\";}', 'en'),
(109, 55, 'Thêm', '', 'vi'),
(110, 55, 'Add', '', 'en'),
(111, 56, 'Sửa', '', 'vi'),
(112, 56, 'Edit', '', 'en'),
(113, 57, 'Xóa', '', 'vi'),
(114, 57, 'Delete', '', 'en'),
(117, 59, 'Ajax', '', 'vi'),
(118, 59, 'Ajax', '', 'en'),
(119, 60, 'Nhập Xuất', '', 'vi'),
(120, 60, 'Import-Export', '', 'en'),
(125, 63, 'Thêm', '', 'vi'),
(126, 63, 'Add', '', 'en'),
(137, 69, 'Lớp Học', 'a:85:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"GpQ0vmMUON33DoOfFLeUrs5yf1lPiNt8PzGH75N0\";s:4:\"lang\";s:2:\"vi\";s:10:\"list_title\";s:20:\"Danh sách tồn kho\";s:6:\"detail\";s:10:\"Chi tiết\";s:4:\"show\";s:3:\"Xem\";s:13:\"rows_per_page\";s:17:\"dòng mỗi trang\";s:12:\"product_name\";s:17:\"Tên sản phẩm\";s:4:\"unit\";s:16:\"Đơn vị tính\";s:5:\"begin\";s:17:\"Tồn đầu kỳ\";s:6:\"import\";s:6:\"Nhập\";s:6:\"export\";s:6:\"Xuất\";s:5:\"image\";s:5:\"Hình\";s:5:\"group\";s:5:\"Nhóm\";s:8:\"quantity\";s:13:\"Số lượng\";s:14:\"actualQuantity\";s:12:\"Tồn thực\";s:8:\"posision\";s:9:\"Vị trí\";s:4:\"sort\";s:11:\"Sắp Xếp\";s:6:\"status\";s:13:\"Trạng Thái\";s:6:\"action\";s:9:\"Tác Vụ\";s:13:\"show_in_stock\";s:19:\"Theo Dõi Tồn Kho\";s:14:\"calculate_type\";s:18:\"Loại Trừ Hàng\";s:10:\"processing\";s:9:\"Gia Công\";s:15:\"processing_unit\";s:20:\"Đơn vị gia công\";s:4:\"auto\";s:12:\"Tự động\";s:9:\"hand_work\";s:11:\"Thủ Công\";s:2:\"m2\";s:8:\"Tính M2\";s:2:\"no\";s:6:\"Không\";s:3:\"yes\";s:3:\"Có\";s:7:\"disable\";s:15:\"Vô Hiệu Hóa\";s:6:\"enable\";s:14:\"Hoạt Động\";s:4:\"next\";s:3:\"Sau\";s:8:\"previous\";s:8:\"Trước\";s:10:\"lengthMenu\";s:28:\"Xem _MENU_ dòng mỗi trang\";s:11:\"zeroRecords\";s:34:\"Không có kết quả tìm kiếm\";s:4:\"info\";s:22:\"Xóa mục đã chọn\";s:9:\"infoEmpty\";s:22:\"Không có dữ liệu\";s:12:\"infoFiltered\";s:36:\"(Lọc từ _MAX_ tổng số dòng)\";s:3:\"all\";s:10:\"Tất cả\";s:6:\"search\";s:11:\"Tìm kiếm\";s:14:\"delete_confirm\";s:37:\"Bạn muốn xóa sản phẩm này ?\";s:21:\"delete_filter_confirm\";s:52:\"Bạn muốn xóa những sản phẩm đã chọn ?\";s:12:\"detail_title\";s:23:\"Nhập - Xuất - Tồn\";s:19:\"product_name_holder\";s:26:\"Nhập tên sản phẩm !\";s:11:\"unit_holder\";s:25:\"Nhập đơn vị tính !\";s:15:\"position_holder\";s:37:\"Nhập vị trí của Sản Phẩm !\";s:11:\"sort_holder\";s:31:\"Nhập thứ tự sắp xếp !\";s:22:\"processing_unit_holder\";s:44:\"Nhập đơn vị tính theo thành phẩm !\";s:10:\"create_new\";s:11:\"Thêm mới\";s:12:\"back_to_list\";s:10:\"Quay lại\";s:4:\"edit\";s:13:\"Chỉnh sửa\";s:6:\"update\";s:12:\"Cập nhật\";s:7:\"success\";s:14:\"Thành Công !\";s:5:\"error\";s:7:\"Lỗi !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:12:\"delete_error\";s:72:\"Sản phẩm này đang được sử dụng trong xuất nhập tồn !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:16:\"error_permission\";s:71:\"Bạn chưa được phân quyền quản lý nhóm sản phẩm này !\";s:15:\"error_extension\";s:47:\"File hình ảnh không đúng định dạng !\";s:11:\"error_image\";s:47:\"File hình ảnh không phải là file hình !\";s:14:\"status_changed\";s:41:\"đã được thay đổi trạng thái !\";s:8:\"ie_title\";s:10:\"Chi tiết\";s:4:\"date\";s:5:\"Ngày\";s:6:\"number\";s:7:\"Số CT\";s:8:\"supplier\";s:15:\"Nhà cung cấp\";s:8:\"customer\";s:12:\"Khách hàng\";s:8:\"salesman\";s:15:\"Mã nhân viên\";s:7:\"ie_type\";s:6:\"Loại\";s:5:\"stock\";s:12:\"Tồn cuối\";s:5:\"total\";s:13:\"Tổng tiền\";s:4:\"note\";s:8:\"Ghi chú\";s:14:\"specifications\";s:9:\"Quy cách\";s:5:\"price\";s:10:\"Đơn giá\";s:6:\"amount\";s:13:\"Thành tiền\";s:11:\"import_type\";s:13:\"Kiểu nhập\";s:11:\"export_type\";s:13:\"Kiểu xuất\";s:3:\"buy\";s:3:\"Mua\";s:6:\"return\";s:5:\"Trả\";s:4:\"sell\";s:4:\"Bán\";s:7:\"destroy\";s:10:\"Hủy bỏ\";s:16:\"change_warehouse\";s:12:\"Chuyển kho\";s:4:\"hide\";s:4:\"Ẩn\";}', 'vi'),
(138, 69, 'Class', 'a:85:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"GpQ0vmMUON33DoOfFLeUrs5yf1lPiNt8PzGH75N0\";s:4:\"lang\";s:2:\"en\";s:10:\"list_title\";s:10:\"Stock List\";s:6:\"detail\";s:6:\"Detail\";s:4:\"show\";s:4:\"Show\";s:13:\"rows_per_page\";s:7:\"entries\";s:12:\"product_name\";s:4:\"Name\";s:4:\"unit\";s:4:\"Unit\";s:5:\"begin\";s:5:\"Begin\";s:6:\"import\";s:6:\"Import\";s:6:\"export\";s:6:\"Export\";s:5:\"image\";s:5:\"Image\";s:5:\"group\";s:5:\"Group\";s:8:\"quantity\";s:8:\"Quantity\";s:14:\"actualQuantity\";s:15:\"Actual Quantity\";s:8:\"posision\";s:8:\"Posision\";s:4:\"sort\";s:4:\"Sort\";s:6:\"status\";s:6:\"Status\";s:6:\"action\";s:6:\"Action\";s:13:\"show_in_stock\";s:13:\"Show in Stock\";s:14:\"calculate_type\";s:14:\"Calculate Type\";s:10:\"processing\";s:10:\"Processing\";s:15:\"processing_unit\";s:15:\"Processing Unit\";s:4:\"auto\";s:4:\"Auto\";s:9:\"hand_work\";s:9:\"Hand Work\";s:2:\"m2\";s:2:\"M2\";s:2:\"no\";s:2:\"No\";s:3:\"yes\";s:3:\"Yes\";s:7:\"disable\";s:7:\"Disable\";s:6:\"enable\";s:6:\"Enable\";s:4:\"next\";s:4:\"Next\";s:8:\"previous\";s:8:\"Previous\";s:10:\"lengthMenu\";s:31:\"Display _MENU_ records per page\";s:11:\"zeroRecords\";s:21:\"Nothing found - sorry\";s:4:\"info\";s:15:\"Delete selected\";s:9:\"infoEmpty\";s:20:\"No records available\";s:12:\"infoFiltered\";s:35:\"(filtered from _MAX_ total records)\";s:3:\"all\";s:3:\"All\";s:6:\"search\";s:6:\"Search\";s:14:\"delete_confirm\";s:37:\"Are you sure to delete this product ?\";s:21:\"delete_filter_confirm\";s:39:\"Are you sure to delete these products ?\";s:12:\"detail_title\";s:19:\"Import-Export-Stock\";s:19:\"product_name_holder\";s:20:\"Input product name !\";s:11:\"unit_holder\";s:23:\"Input unit of product !\";s:15:\"position_holder\";s:31:\"Input the location of product !\";s:11:\"sort_holder\";s:19:\"Input sort number !\";s:22:\"processing_unit_holder\";s:32:\"Input unit of finished product !\";s:10:\"create_new\";s:10:\"Create New\";s:12:\"back_to_list\";s:4:\"Back\";s:4:\"edit\";s:4:\"Edit\";s:6:\"update\";s:6:\"Update\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:12:\"delete_error\";s:62:\"This product has been use for Import,Export and Stock Report !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:8:\"is_exist\";s:18:\"is already exits !\";s:16:\"error_permission\";s:53:\"You have not permissions for products in this group !\";s:15:\"error_extension\";s:21:\"Invalid image style !\";s:11:\"error_image\";s:22:\"File must be a image !\";s:14:\"status_changed\";s:25:\"status has been changed !\";s:8:\"ie_title\";s:6:\"Detail\";s:4:\"date\";s:4:\"Date\";s:6:\"number\";s:6:\"Number\";s:8:\"supplier\";s:8:\"Suppiler\";s:8:\"customer\";s:8:\"Customer\";s:8:\"salesman\";s:8:\"Salesman\";s:7:\"ie_type\";s:4:\"Type\";s:5:\"stock\";s:5:\"Stock\";s:5:\"total\";s:5:\"Total\";s:4:\"note\";s:4:\"Note\";s:14:\"specifications\";s:14:\"Specifications\";s:5:\"price\";s:5:\"Price\";s:6:\"amount\";s:6:\"Amount\";s:11:\"import_type\";s:11:\"Import Type\";s:11:\"export_type\";s:11:\"Export Type\";s:3:\"buy\";s:3:\"Buy\";s:6:\"return\";s:6:\"Return\";s:4:\"sell\";s:4:\"Sell\";s:7:\"destroy\";s:7:\"Destroy\";s:16:\"change_warehouse\";s:16:\"Change Warehouse\";s:4:\"hide\";s:4:\"Hide\";}', 'en'),
(145, 73, 'Khách Hàng', 'a:45:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"of8aTon55pctWyBvxZdwTMkV2Gr1XDRnStmTRVPl\";s:4:\"lang\";s:2:\"vi\";s:10:\"list_title\";s:23:\"Danh sách Khách Hàng\";s:4:\"show\";s:3:\"Xem\";s:13:\"rows_per_page\";s:17:\"dòng mỗi trang\";s:4:\"next\";s:3:\"Sau\";s:8:\"previous\";s:8:\"Trước\";s:10:\"lengthMenu\";s:28:\"Xem _MENU_ dòng mỗi trang\";s:11:\"zeroRecords\";s:34:\"Không có kết quả tìm kiếm\";s:4:\"info\";s:22:\"Xóa mục đã chọn\";s:9:\"infoEmpty\";s:22:\"Không có dữ liệu\";s:12:\"infoFiltered\";s:36:\"(Lọc từ _MAX_ tổng số dòng)\";s:3:\"all\";s:10:\"Tất cả\";s:6:\"search\";s:11:\"Tìm kiếm\";s:14:\"delete_confirm\";s:37:\"Bạn muốn xóa khách hàng này ?\";s:21:\"delete_filter_confirm\";s:52:\"Bạn muốn xóa những khách hàng đã chọn ?\";s:17:\"checked_customers\";s:32:\"Những khách hàng đã chọn\";s:6:\"action\";s:9:\"Tác Vụ\";s:4:\"name\";s:4:\"Tên\";s:11:\"name_holder\";s:26:\"Nhập tên khách hàng !\";s:13:\"name_required\";s:36:\"Tên khách hàng là bắt buộc !\";s:7:\"address\";s:12:\"Địa chỉ\";s:14:\"address_holder\";s:34:\"Nhập địa chỉ khách hàng !\";s:16:\"address_required\";s:31:\"Địa chỉ là bắt buộc !\";s:8:\"tax_code\";s:15:\"Mã số thuế\";s:15:\"tax_code_holder\";s:24:\"Nhập mã số thuế !\";s:7:\"contact\";s:10:\"Liên hệ\";s:14:\"contact_holder\";s:30:\"Nhập thông tin liên hệ !\";s:5:\"phone\";s:15:\"Điện thoại\";s:12:\"phone_holder\";s:29:\"Nhập số điện thoại !\";s:5:\"email\";s:5:\"Email\";s:12:\"email_holder\";s:27:\"Nhập địa chỉ Email !\";s:9:\"add_title\";s:11:\"Thêm mới\";s:10:\"create_new\";s:11:\"Thêm mới\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:7:\"success\";s:14:\"Thành công !\";s:5:\"error\";s:7:\"Lỗi !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:10:\"edit_title\";s:12:\"Cập nhật\";s:6:\"update\";s:12:\"Cập Nhật\";}', 'vi');
INSERT INTO `structure_translations` (`id`, `structure_id`, `structure_name`, `trans_page`, `locale`) VALUES
(146, 73, 'Customers', 'a:45:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"of8aTon55pctWyBvxZdwTMkV2Gr1XDRnStmTRVPl\";s:4:\"lang\";s:2:\"en\";s:10:\"list_title\";s:14:\"Customers List\";s:4:\"show\";s:4:\"Show\";s:13:\"rows_per_page\";s:13:\"rows per page\";s:4:\"next\";s:4:\"Next\";s:8:\"previous\";s:8:\"Previous\";s:10:\"lengthMenu\";s:31:\"Display _MENU_ records per page\";s:11:\"zeroRecords\";s:23:\"Nothing found - sorry !\";s:4:\"info\";s:15:\"Delete selected\";s:9:\"infoEmpty\";s:22:\"No records available !\";s:12:\"infoFiltered\";s:35:\"(filtered from _MAX_ total records)\";s:3:\"all\";s:3:\"All\";s:6:\"search\";s:6:\"Search\";s:14:\"delete_confirm\";s:38:\"Are you sure to delete this customer ?\";s:21:\"delete_filter_confirm\";s:39:\"Are you sure to delete these customers?\";s:17:\"checked_customers\";s:18:\"Selected Customers\";s:6:\"action\";s:6:\"Action\";s:4:\"name\";s:4:\"Name\";s:11:\"name_holder\";s:21:\"Input customer name !\";s:13:\"name_required\";s:18:\"Name is required !\";s:7:\"address\";s:7:\"Address\";s:14:\"address_holder\";s:15:\"Input address !\";s:16:\"address_required\";s:21:\"Address is required !\";s:8:\"tax_code\";s:8:\"Tax Code\";s:15:\"tax_code_holder\";s:16:\"Input tax code !\";s:7:\"contact\";s:7:\"Contact\";s:14:\"contact_holder\";s:27:\"Input  contact infomation !\";s:5:\"phone\";s:5:\"Phone\";s:12:\"phone_holder\";s:20:\"Input phone number !\";s:5:\"email\";s:5:\"Email\";s:12:\"email_holder\";s:21:\"Input contact email !\";s:9:\"add_title\";s:12:\"Add Customer\";s:10:\"create_new\";s:10:\"Create New\";s:12:\"back_to_list\";s:12:\"Back to List\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:8:\"is_exist\";s:18:\"is already exits !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:10:\"edit_title\";s:4:\"Edit\";s:6:\"update\";s:6:\"Update\";}', 'en'),
(147, 74, 'Nhân Viên', 'a:57:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"c34W9Vrtu48wKbTbtOhOhP90W8bMHhggXFJ2dTgj\";s:4:\"lang\";s:2:\"vi\";s:10:\"list_title\";s:22:\"Danh sách Nhân Viên\";s:4:\"show\";s:3:\"Xem\";s:13:\"rows_per_page\";s:17:\"dòng mỗi trang\";s:4:\"next\";s:3:\"Sau\";s:8:\"previous\";s:8:\"Trước\";s:10:\"lengthMenu\";s:28:\"Xem _MENU_ dòng mỗi trang\";s:11:\"zeroRecords\";s:34:\"Không có kết quả tìm kiếm\";s:4:\"info\";s:22:\"Xóa mục đã chọn\";s:9:\"infoEmpty\";s:22:\"Không có dữ liệu\";s:12:\"infoFiltered\";s:36:\"(Lọc từ _MAX_ tổng số dòng)\";s:3:\"all\";s:10:\"Tất cả\";s:6:\"search\";s:11:\"Tìm kiếm\";s:14:\"delete_confirm\";s:36:\"Bạn muốn xóa nhân viên này ?\";s:21:\"delete_filter_confirm\";s:51:\"Bạn muốn xóa những nhân viên đã chọn ?\";s:17:\"checked_customers\";s:31:\"Những nhân viên đã chọn\";s:6:\"action\";s:9:\"Tác Vụ\";s:4:\"name\";s:4:\"Tên\";s:11:\"name_holder\";s:25:\"Nhập tên nhân viên !\";s:13:\"name_required\";s:35:\"Tên nhân viên là bắt buộc !\";s:10:\"staff_code\";s:15:\"Mã nhân viên\";s:17:\"staff_code_holder\";s:24:\"Nhập mã nhân viên !\";s:19:\"staff_code_required\";s:34:\"Mã nhân biên là bắt buộc !\";s:13:\"date_of_birth\";s:10:\"Ngày sinh\";s:20:\"date_of_birth_holder\";s:19:\"Nhập ngày sinh !\";s:18:\"date_format_holder\";s:57:\"Ngày sinh phải có  định dạng ngày/tháng/năm !\";s:7:\"address\";s:12:\"Địa chỉ\";s:14:\"address_holder\";s:33:\"Nhập địa chỉ nhân viên !\";s:16:\"address_required\";s:31:\"Địa chỉ là bắt buộc !\";s:5:\"email\";s:5:\"Email\";s:12:\"email_holder\";s:27:\"Nhập địa chỉ Email !\";s:14:\"email_required\";s:24:\"Email là bắt buộc !\";s:11:\"email_email\";s:67:\"Email phải có định dạng xxx@stdsvn.com hoặc xxx@stds.vn !\";s:5:\"phone\";s:15:\"Điện thoại\";s:12:\"phone_holder\";s:29:\"Nhập số điện thoại !\";s:9:\"cmnd_cccd\";s:11:\"CMND / CCCD\";s:16:\"cmnd_cccd_holder\";s:23:\"Nhập số CMND/CCCD !\";s:4:\"area\";s:9:\"Khu vực\";s:11:\"area_holder\";s:18:\"Nhập khu vực !\";s:6:\"status\";s:13:\"Trạng thái\";s:9:\"add_title\";s:11:\"Thêm mới\";s:10:\"create_new\";s:11:\"Thêm mới\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:7:\"success\";s:14:\"Thành công !\";s:5:\"error\";s:7:\"Lỗi !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:9:\"is_denied\";s:18:\"bị từ chối !\";s:10:\"edit_title\";s:12:\"Cập nhật\";s:6:\"update\";s:12:\"Cập Nhật\";s:14:\"checked_staffs\";s:31:\"Những nhân viên đã chọn\";s:14:\"status_changed\";s:41:\"đã được thay đổi trạng thái !\";}', 'vi'),
(148, 74, 'Staffs', 'a:57:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"c34W9Vrtu48wKbTbtOhOhP90W8bMHhggXFJ2dTgj\";s:4:\"lang\";s:2:\"en\";s:10:\"list_title\";s:11:\"Staffs List\";s:4:\"show\";s:4:\"Show\";s:13:\"rows_per_page\";s:13:\"rows per page\";s:4:\"next\";s:4:\"Next\";s:8:\"previous\";s:8:\"Previous\";s:10:\"lengthMenu\";s:31:\"Display _MENU_ records per page\";s:11:\"zeroRecords\";s:23:\"Nothing found - sorry !\";s:4:\"info\";s:15:\"Delete selected\";s:9:\"infoEmpty\";s:22:\"No records available !\";s:12:\"infoFiltered\";s:35:\"(filtered from _MAX_ total records)\";s:3:\"all\";s:3:\"All\";s:6:\"search\";s:6:\"Search\";s:14:\"delete_confirm\";s:34:\"Are you sure to delete this staff?\";s:21:\"delete_filter_confirm\";s:36:\"Are you sure to delete these staffs?\";s:17:\"checked_customers\";s:15:\"Selected staffs\";s:6:\"action\";s:6:\"Action\";s:4:\"name\";s:4:\"Name\";s:11:\"name_holder\";s:18:\"Input staff name !\";s:13:\"name_required\";s:18:\"Name is required !\";s:10:\"staff_code\";s:10:\"Staff Code\";s:17:\"staff_code_holder\";s:18:\"Input staff code !\";s:19:\"staff_code_required\";s:25:\"Staff ccode is required !\";s:13:\"date_of_birth\";s:13:\"Date of Birth\";s:20:\"date_of_birth_holder\";s:21:\"Input date of birth !\";s:18:\"date_format_holder\";s:52:\"Date of birth must be in the format day/month/year !\";s:7:\"address\";s:7:\"Address\";s:14:\"address_holder\";s:15:\"Input address !\";s:16:\"address_required\";s:21:\"Address is required !\";s:5:\"email\";s:5:\"Email\";s:12:\"email_holder\";s:21:\"Input contact email !\";s:14:\"email_required\";s:59:\"Email must be in the format xxx@stdsvn.com or xxx@stds.vn !\";s:11:\"email_email\";s:19:\"Email is required !\";s:5:\"phone\";s:5:\"Phone\";s:12:\"phone_holder\";s:20:\"Input phone number !\";s:9:\"cmnd_cccd\";s:21:\"Citizen identity card\";s:16:\"cmnd_cccd_holder\";s:17:\"Input ID number !\";s:4:\"area\";s:4:\"Area\";s:11:\"area_holder\";s:12:\"Input area !\";s:6:\"status\";s:6:\"Status\";s:9:\"add_title\";s:9:\"Add Staff\";s:10:\"create_new\";s:10:\"Create New\";s:12:\"back_to_list\";s:12:\"Back to List\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:8:\"is_exist\";s:18:\"is already exits !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:9:\"is_denied\";s:12:\"is dennied !\";s:10:\"edit_title\";s:10:\"Edit Staff\";s:6:\"update\";s:6:\"Update\";s:14:\"checked_staffs\";s:15:\"Selected staffs\";s:14:\"status_changed\";s:25:\"status has been changed !\";}', 'en'),
(149, 75, 'Thêm', '', 'vi'),
(150, 75, 'Add', '', 'en'),
(151, 76, 'Sửa', '', 'vi'),
(152, 76, 'Edit', '', 'en'),
(153, 77, 'Xóa', '', 'vi'),
(154, 77, 'Delete', '', 'en'),
(155, 78, 'Thêm', '', 'vi'),
(156, 78, 'Add', '', 'en'),
(157, 79, 'Sửa', '', 'vi'),
(158, 79, 'Edit', '', 'en'),
(159, 80, 'Xóa', '', 'vi'),
(160, 80, 'Delete', '', 'en'),
(167, 84, 'Đổi trạng thái', '', 'vi'),
(168, 84, 'Change Status', '', 'en'),
(169, 85, 'Bài Viết', 'a:28:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"09H5a0d48tsmpWV60VVuWx5u4OW6lDQjdsNUg3SY\";s:4:\"lang\";s:2:\"vi\";s:9:\"add_title\";s:15:\"Thêm Kho Hàng\";s:6:\"update\";s:12:\"Cập nhật\";s:12:\"back_to_list\";s:20:\"Quay về Danh Sách\";s:10:\"list_title\";s:20:\"Danh sách Kho Hàng\";s:6:\"status\";s:13:\"Trạng Thái\";s:6:\"action\";s:9:\"Tác Vụ\";s:6:\"enable\";s:14:\"Hoạt động\";s:7:\"disable\";s:15:\"Vô hiệu hóa\";s:4:\"sort\";s:10:\"Thứ tự\";s:11:\"sort_holder\";s:32:\"Nhập thứ tự hiển thị !\";s:11:\"name_holder\";s:21:\"Nhập tên Kho Hàng\";s:14:\"warehouse_name\";s:14:\"Tên Kho Hàng\";s:13:\"name_required\";s:33:\"Tên kho hàng là bắt buộc !\";s:14:\"warehouse_code\";s:7:\"Mã Kho\";s:11:\"code_holder\";s:22:\"Nhập mã Kho Hàng !\";s:13:\"code_required\";s:32:\"Mã kho hàng là bắt buộc !\";s:10:\"create_new\";s:11:\"Thêm mới\";s:7:\"success\";s:14:\"Thành Công !\";s:5:\"error\";s:7:\"Lỗi !\";s:11:\"add_success\";s:27:\"đã được thêm mới !\";s:14:\"update_success\";s:28:\"đã được cập nhật !\";s:14:\"delete_success\";s:20:\"đã được xóa !\";s:10:\"data_error\";s:31:\"Dữ liệu không hợp lệ !\";s:8:\"is_exist\";s:18:\"đã tồn tại !\";s:14:\"status_changed\";s:41:\"đã được thay đổi trạng thái !\";}', 'vi'),
(170, 85, 'Articles', 'a:28:{s:7:\"_method\";s:3:\"PUT\";s:6:\"_token\";s:40:\"09H5a0d48tsmpWV60VVuWx5u4OW6lDQjdsNUg3SY\";s:4:\"lang\";s:2:\"en\";s:9:\"add_title\";s:17:\"Add New Warehouse\";s:6:\"update\";s:6:\"Update\";s:12:\"back_to_list\";s:12:\"Back to List\";s:10:\"list_title\";s:14:\"Warehouse List\";s:6:\"status\";s:6:\"Status\";s:6:\"action\";s:6:\"Action\";s:6:\"enable\";s:6:\"Enable\";s:7:\"disable\";s:7:\"Disable\";s:4:\"sort\";s:4:\"Sort\";s:11:\"sort_holder\";s:12:\"Input sort !\";s:11:\"name_holder\";s:12:\"Input Name !\";s:14:\"warehouse_name\";s:14:\"Warehouse Name\";s:13:\"name_required\";s:28:\"Warehouse name is required !\";s:14:\"warehouse_code\";s:14:\"Warehouse Code\";s:11:\"code_holder\";s:22:\"Input warehouse code !\";s:13:\"code_required\";s:28:\"Warehouse code is required !\";s:10:\"create_new\";s:10:\"Create New\";s:7:\"success\";s:9:\"Success !\";s:5:\"error\";s:7:\"Error !\";s:11:\"add_success\";s:16:\"has been added !\";s:14:\"update_success\";s:18:\"has been updated !\";s:14:\"delete_success\";s:18:\"has been deleted !\";s:10:\"data_error\";s:14:\"Invalid Data !\";s:8:\"is_exist\";s:18:\"is already exits !\";s:14:\"status_changed\";s:18:\"has been changed !\";}', 'en'),
(171, 86, 'Thêm', '', 'vi'),
(172, 86, 'Add', '', 'en'),
(173, 87, 'Sửa', '', 'vi'),
(174, 87, 'Edit', '', 'en'),
(175, 88, 'Xóa', '', 'vi'),
(176, 88, 'Delete', '', 'en'),
(177, 89, 'Đổi Trạng Thái', '', 'vi'),
(178, 89, 'Change Status', '', 'en'),
(179, 90, 'Thêm', '', 'vi'),
(180, 90, 'Add', '', 'en'),
(181, 91, 'Sửa', '', 'vi'),
(182, 91, 'Edit', '', 'en'),
(197, 46, 'Sửa', '', 'vi'),
(198, 98, 'Sửa', '', 'vi'),
(199, 98, 'Edit', '', 'en'),
(200, 99, 'Xóa', '', 'vi'),
(201, 99, 'Delete', '', 'en'),
(212, 105, 'Thông Báo', '', 'vi'),
(213, 105, 'Notification', '', 'en'),
(214, 106, 'Xóa', '', 'vi'),
(215, 106, 'Delete', '', 'en'),
(216, 107, 'Đổi trạng thái', '', 'vi'),
(217, 107, 'Change Status', '', 'en');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `group` int(11) NOT NULL,
  `langId` int(11) NOT NULL,
  `phone` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extends_users` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `avatar` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `last_login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `menus_permission` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `products_permission` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `email_verified_at`, `password`, `group`, `langId`, `phone`, `extends_users`, `status`, `avatar`, `last_login`, `menus_permission`, `products_permission`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@sungduc.com', NULL, '$2y$10$7b95i0FKJp2HV1GN9phkw.h5JY9yT/VrgiTFio2i0m0G6o5fnKPKe', 1, 1, NULL, NULL, '1', 'admin@sungduc.com.jpg', '2022-11-30 10:59:48', NULL, NULL, NULL, NULL, '2022-11-30 04:00:34'),
(15, 'Lê Minh Khôi', 'khoi.leminh@sungduc.com', NULL, '$2y$10$7b95i0FKJp2HV1GN9phkw.h5JY9yT/VrgiTFio2i0m0G6o5fnKPKe', 2, 1, NULL, '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-01 03:32:35');

-- --------------------------------------------------------

--
-- Table structure for table `users_group`
--

DROP TABLE IF EXISTS `users_group`;
CREATE TABLE IF NOT EXISTS `users_group` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `menus_permission` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `products_permission` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_group`
--

INSERT INTO `users_group` (`id`, `name`, `sort`, `status`, `menus_permission`, `products_permission`, `created_at`, `updated_at`) VALUES
(1, 'New Members', 1, 1, '1,7,8,34,29,30', NULL, '2020-01-02 07:51:16', '2022-06-01 14:39:55'),
(2, 'Quản trị', 2, 1, '1,18,19,20,33,15,17,16,3,45,46,47,49,5,4,86,87,88,89,85,23,24,25,31,21,26,27,28,32,22,6,34,7,29,30,8,60,59,63,98,99', NULL, '2022-05-17 08:59:11', '2022-11-30 03:53:38'),
(3, 'Hỗ trợ - Kinh Doanh', 3, 1, '1,90,69,73,74,34,7,29,30,8,91', NULL, '2022-05-26 03:50:28', '2022-06-07 06:49:32'),
(4, 'Quản lý kho hàng', 4, 1, '1,5,90,91,69,51,52,53,50,55,56,57,54,34,7,29,30,8,60,59,4,35,37,36', NULL, '2022-06-02 01:34:05', '2022-06-21 08:10:32'),
(5, 'Kế toán', 5, 1, '1,85,5,4,69,90,91,50,54,73,74,7,34,8,29,30', NULL, '2022-06-07 06:57:51', '2022-06-07 06:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse`
--

DROP TABLE IF EXISTS `warehouse`;
CREATE TABLE IF NOT EXISTS `warehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `warehouse`
--

INSERT INTO `warehouse` (`id`, `code`, `name`, `sort`, `status`, `created_at`, `updated_at`) VALUES
(1, 'HCM', 'Hồ Chí Minh', 1, 1, '2022-05-17 08:28:55', '2022-05-17 08:28:55'),
(2, 'HN', 'Hà Nội', 2, 1, '2022-05-17 08:29:23', '2022-05-17 08:29:23'),
(3, 'DN', 'Đà Nẵng', 3, 1, '2022-05-17 08:29:33', '2022-05-17 08:29:33');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `languages_translations`
--
ALTER TABLE `languages_translations`
  ADD CONSTRAINT `languages_translations_languages_id_foreign` FOREIGN KEY (`languages_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news_translations`
--
ALTER TABLE `news_translations`
  ADD CONSTRAINT `FK_News_ID` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `structure` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
