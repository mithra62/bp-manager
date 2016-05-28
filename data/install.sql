-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2016 at 04:18 PM
-- Server version: 5.6.16
-- PHP Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `dev_backup_pro_server`
--

-- --------------------------------------------------------

--
-- Table structure for table `ips`
--

DROP TABLE IF EXISTS `ips`;
CREATE TABLE IF NOT EXISTS `ips` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `ip` int(11) NOT NULL,
  `ip_raw` char(20) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `confirm_key` varchar(80) DEFAULT NULL,
  `creator` int(10) NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `migration_version`
--

DROP TABLE IF EXISTS `migration_version`;
CREATE TABLE IF NOT EXISTS `migration_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `migration_version`
--

INSERT INTO `migration_version` (`id`, `version`) VALUES
(1, 20141210161142),
(2, 20141214173541),
(3, 20141215143333),
(4, 20141218001350),
(5, 20150110153638),
(6, 20150110212351),
(7, 20150125003019),
(8, 20150125125936),
(9, 20150125154949);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `option_value` longtext CHARACTER SET latin1,
  `option_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `account_id`, `option_value`, `option_name`, `created_date`, `last_modified`) VALUES
(1, 1, 'Backup Pro Manager', 'site_name', '2016-01-15 13:26:47', '2016-05-28 21:04:44'),
(2, 1, 'http://eric.bp-server.com', 'site_url', '2016-01-15 13:26:47', '2016-05-28 21:04:44'),
(3, 1, 'no-reply@mithra62.com', 'mail_reply_to_email', '2016-01-17 00:36:12', '2016-01-21 01:24:02'),
(4, 1, 'mithra62', 'mail_reply_to_name', '2016-01-17 00:36:12', '2016-01-21 01:24:02'),
(5, 1, 'no-reply@mithra62.com', 'mail_sender_email', '2016-01-17 00:36:12', '2016-01-21 01:24:02'),
(6, 1, 'mithra62', 'mail_sender_name', '2016-01-17 00:36:12', '2016-01-21 01:24:02'),
(7, 1, 'no-reply@mithra62.com', 'mail_from_email', '2016-01-17 00:36:12', '2016-01-21 01:24:02'),
(8, 1, 'mithra62', 'mail_from_name', '2016-01-17 00:36:12', '2016-01-21 01:24:02'),
(9, 1, '0', 'enable_ip', '2016-01-19 20:41:30', '2016-02-13 18:37:11');

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

DROP TABLE IF EXISTS `sites`;
CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `api_endpoint_url` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `site_name` varchar(255) NOT NULL,
  `platform` varchar(100) NOT NULL,
  `errors` text NOT NULL,
  `file_backup_total` int(4) NOT NULL,
  `database_backup_total` int(4) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `api_secret` varchar(255) NOT NULL,
  `first_backup` datetime NOT NULL,
  `last_backup` datetime NOT NULL,
  `owner_id` int(10) NOT NULL,
  `last_modified` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `site_teams`
--

DROP TABLE IF EXISTS `site_teams`;
CREATE TABLE IF NOT EXISTS `site_teams` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `user_id` (`user_id`),
  KEY `site_id_to_user_id` (`site_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `site_teams`
--

INSERT INTO `site_teams` (`id`, `site_id`, `user_id`, `created_date`, `last_modified`) VALUES
(2, 22, 1, NULL, NULL),
(7, 27, 1, '2016-04-03 03:33:53', '2016-04-03 03:33:53');

-- --------------------------------------------------------

--
-- Table structure for table `user2role`
--

DROP TABLE IF EXISTS `user2role`;
CREATE TABLE IF NOT EXISTS `user2role` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `account_id` int(10) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user2role`
--

INSERT INTO `user2role` (`user_id`, `account_id`, `role_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `password` varchar(128) CHARACTER SET latin1 NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_hash` varchar(100) DEFAULT NULL,
  `verified_date` timestamp NULL DEFAULT NULL,
  `verified_sent_date` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `hash` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `pw_forgotten` timestamp NULL DEFAULT NULL,
  `forgotten_hash` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UserStatus` (`verified`),
  KEY `Joined` (`created_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=14 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `verified`, `verified_hash`, `verified_date`, `verified_sent_date`, `last_login`, `hash`, `pw_forgotten`, `forgotten_hash`, `created_date`, `last_modified`) VALUES
(1, 'default@mithra62.com', 'a29f42d035029ec1cf026e4c07a9125e7de34483f418ebdcc163c884821a05b09732ae0075ac62bf5d71de54134fc8a81cf232108e708116c7f6fca150dc3eee', 1, NULL, NULL, NULL, '2016-05-27 23:43:45', 'fd924584891c8f928e4517f6f1651cce', NULL, NULL, '2016-01-04 05:47:28', '2016-05-27 23:43:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_accounts`
--

DROP TABLE IF EXISTS `user_accounts`;
CREATE TABLE IF NOT EXISTS `user_accounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `account_id` int(10) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user_accounts`
--

INSERT INTO `user_accounts` (`id`, `user_id`, `account_id`, `created_date`) VALUES
(1, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

DROP TABLE IF EXISTS `user_data`;
CREATE TABLE IF NOT EXISTS `user_data` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `option_value` longtext CHARACTER SET latin1,
  `option_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user_data`
--

INSERT INTO `user_data` (`id`, `user_id`, `option_value`, `option_name`, `created_date`, `last_modified`) VALUES
(1, 1, 'America/Los_Angeles', 'timezone', '2016-02-14 03:47:40', '2016-02-14 03:47:40'),
(2, 1, 'en_US', 'locale', '2016-02-14 03:47:40', '2016-02-14 03:47:40'),
(3, 1, '1', 'enable_rel_time', '2016-02-14 03:47:40', '2016-02-14 03:47:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `name` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `description` text CHARACTER SET latin1,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `account_id`, `name`, `description`, `last_modified`, `created_date`) VALUES
(1, 1, 'Administrator', 'Moji System Administrators', '2016-02-29 09:12:52', '2016-01-04 05:47:28'),
(2, 1, 'User', 'Can manage users', '2016-03-23 02:42:38', '2016-01-04 05:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `user_role_2_permissions`
--

DROP TABLE IF EXISTS `user_role_2_permissions`;
CREATE TABLE IF NOT EXISTS `user_role_2_permissions` (
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permission_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_role_2_permissions`
--

INSERT INTO `user_role_2_permissions` (`role_id`, `permission_id`) VALUES
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(2, 22),
(2, 23),
(2, 24),
(2, 25);

-- --------------------------------------------------------

--
-- Table structure for table `user_role_permissions`
--

DROP TABLE IF EXISTS `user_role_permissions`;
CREATE TABLE IF NOT EXISTS `user_role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `site_area` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `user_role_permissions`
--

INSERT INTO `user_role_permissions` (`id`, `name`, `site_area`) VALUES
(14, 'admin_access', 'admin'),
(15, 'view_users_data', 'users'),
(16, 'manage_users', 'users'),
(17, 'manage_roles', 'roles'),
(18, 'manage_ips', 'ips'),
(22, 'access_rest_api', 'rest-api'),
(23, 'self_allow_ip', 'ips'),
(24, 'view_sites', 'sites'),
(25, 'manage_sites', 'sites');
