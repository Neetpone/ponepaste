/*M!999999\- enable the sandbox mode */
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ponepaste_beta
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-0+deb13u1 from Debian

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_logs` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `time` datetime NOT NULL DEFAULT current_timestamp(),
                              `action` varchar(255) NOT NULL,
                              `ip` varchar(64) NOT NULL,
                              `message` varchar(128) DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `admin_logs_users_id_fk` (`user_id`),
                              CONSTRAINT `admin_logs_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ban_user`
--

DROP TABLE IF EXISTS `ban_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ban_user` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `ip` varchar(16) NOT NULL,
                            `last_date` varchar(15) NOT NULL,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mod_messages`
--

DROP TABLE IF EXISTS `mod_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mod_messages` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `user_id` int(11) NOT NULL,
                                `created_at` datetime NOT NULL,
                                `updated_at` datetime DEFAULT NULL,
                                `message` varchar(255) NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `mod_messages_users_id_fk` (`user_id`),
                                CONSTRAINT `mod_messages_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_view`
--

DROP TABLE IF EXISTS `page_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_view` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `date` varchar(255) DEFAULT NULL,
                             `tpage` varchar(255) DEFAULT NULL,
                             `tvisit` varchar(255) DEFAULT NULL,
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
                         `page_name` varchar(255) NOT NULL,
                         `last_date` varchar(255) DEFAULT NULL,
                         `page_title` longtext DEFAULT NULL,
                         `page_content` longtext DEFAULT NULL,
                         PRIMARY KEY (`page_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paste_taggings`
--

DROP TABLE IF EXISTS `paste_taggings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paste_taggings` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `paste_id` int(11) NOT NULL,
                                  `tag_id` int(11) NOT NULL,
                                  PRIMARY KEY (`id`),
                                  UNIQUE KEY `paste_taggings_uindex` (`paste_id`,`tag_id`),
                                  KEY `tag_id` (`tag_id`),
                                  CONSTRAINT `paste_taggings_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`),
                                  CONSTRAINT `paste_taggings_ibfk_3` FOREIGN KEY (`paste_id`) REFERENCES `pastes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pastes`
--

DROP TABLE IF EXISTS `pastes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pastes` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `title` longtext DEFAULT NULL,
                          `content` longtext DEFAULT NULL,
                          `visible` longtext DEFAULT NULL,
                          `code` longtext DEFAULT NULL,
                          `expiry` longtext DEFAULT NULL,
                          `password` longtext DEFAULT NULL,
                          `encrypt` longtext DEFAULT NULL,
                          `ip` longtext DEFAULT NULL,
                          `views` int(11) DEFAULT NULL,
                          `s_date` longtext DEFAULT NULL,
                          `tagsys` longtext DEFAULT NULL,
                          `user_id` int(11) DEFAULT NULL,
                          `created_at` datetime DEFAULT NULL,
                          `updated_at` datetime DEFAULT NULL,
                          `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
                          `hidden` tinyint(1) NOT NULL DEFAULT 0,
                          `deleted_at` datetime DEFAULT NULL,
                          `deleted_by_id` int(11) DEFAULT NULL,
                          `mark` varchar(16) DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `users_id_fkey` (`user_id`),
                          CONSTRAINT `pastes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `paste_id` int(11) NOT NULL,
                           `user_id` int(11) NOT NULL,
                           `open` tinyint(1) NOT NULL DEFAULT 1,
                           `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                           `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
                           `reason` varchar(255) NOT NULL,
                           PRIMARY KEY (`id`),
                           KEY `reports_users_id_fk` (`user_id`),
                           KEY `reports_pastes_id_fk` (`paste_id`),
                           CONSTRAINT `reports_pastes_id_fk` FOREIGN KEY (`paste_id`) REFERENCES `pastes` (`id`) ON DELETE CASCADE,
                           CONSTRAINT `reports_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` varchar(255) NOT NULL,
                        `slug` varchar(255) NOT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `tags_name_uindex` (`name`),
                        UNIQUE KEY `tags_slug_uindex` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_favourites`
--

DROP TABLE IF EXISTS `user_favourites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_favourites` (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `paste_id` int(11) DEFAULT NULL,
                                   `user_id` int(11) NOT NULL,
                                   `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                   PRIMARY KEY (`id`),
                                   KEY `user_id_fk` (`user_id`),
                                   KEY `pins_ibfk_2` (`paste_id`),
                                   CONSTRAINT `user_favourites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
                                   CONSTRAINT `user_favourites_ibfk_2` FOREIGN KEY (`paste_id`) REFERENCES `pastes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_reports`
--

DROP TABLE IF EXISTS `user_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_reports` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `m_report` longtext NOT NULL,
                                `p_report` int(11) NOT NULL,
                                `rep_reason` tinyint(1) NOT NULL,
                                `t_report` longtext NOT NULL,
                                PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_sessions` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `user_id` int(11) NOT NULL,
                                 `token` varchar(255) NOT NULL,
                                 `expire_at` datetime NOT NULL,
                                 `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                 `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                 PRIMARY KEY (`id`),
                                 UNIQUE KEY `user_sessions_token_uindex` (`token`),
                                 KEY `user_sessions_users_id_fk` (`user_id`),
                                 CONSTRAINT `user_sessions_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `oauth_uid` longtext DEFAULT NULL,
                         `username` varchar(255) DEFAULT NULL,
                         `full_name` longtext DEFAULT NULL,
                         `platform` longtext DEFAULT NULL,
                         `password` varchar(255) DEFAULT NULL,
                         `verified` tinyint(1) NOT NULL DEFAULT 0,
                         `picture` longtext DEFAULT NULL,
                         `date` longtext DEFAULT NULL,
                         `ip` varchar(255) DEFAULT NULL,
                         `badge` tinyint(1) unsigned zerofill NOT NULL DEFAULT 0,
                         `banned` tinyint(1) NOT NULL DEFAULT 0,
                         `recovery_code_hash` varchar(255) NOT NULL,
                         `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                         `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                         `role` tinyint(4) NOT NULL DEFAULT 0,
                         `admin` tinyint(1) NOT NULL DEFAULT 0,
                         `admin_password_hash` varchar(255) DEFAULT NULL,
                         PRIMARY KEY (`id`),
                         UNIQUE KEY `users_username_uindex` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-04-05 20:57:00
