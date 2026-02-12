-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for re_bps
CREATE DATABASE IF NOT EXISTS `re_bps` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `re_bps`;

-- Dumping structure for table re_bps.classifications
CREATE TABLE IF NOT EXISTS `classifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `organization` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table re_bps.constituents
CREATE TABLE IF NOT EXISTS `constituents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `psn` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `suffix` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sex` enum('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
  `birthdate` date NOT NULL,
  `birthplace` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'TACLOBAN CITY',
  `civil_status` enum('SINGLE','MARRIED','DIVORCED','SEPARATED','WIDOWED') NOT NULL DEFAULT 'SINGLE',
  `religion` varchar(100) NOT NULL DEFAULT 'ROMAN CATHOLIC',
  `citizenship` enum('FILIPINO','OTHERS') NOT NULL DEFAULT 'FILIPINO',
  `occupation` varchar(100) DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `education_attainment` enum('1','2','3','4','5','6','7','8','9','10','11') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_graduate` enum('YES','NO') NOT NULL,
  `registered_voter` enum('YES','NO') NOT NULL,
  `removed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `psn` (`psn`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table re_bps.constituents_classifications
CREATE TABLE IF NOT EXISTS `constituents_classifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `constituent_id` int NOT NULL,
  `classification_id` int NOT NULL,
  `org_id_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_id_no` (`org_id_no`),
  KEY `constituent_id` (`constituent_id`),
  KEY `classification_id` (`classification_id`),
  CONSTRAINT `FK_constituents_classifications_classifications` FOREIGN KEY (`classification_id`) REFERENCES `classifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_constituents_classifications_constituents` FOREIGN KEY (`constituent_id`) REFERENCES `constituents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='This table will serve as many-to-many relationship between the constituents and classifications table';

-- Data exporting was unselected.

-- Dumping structure for table re_bps.constituents_families_households
CREATE TABLE IF NOT EXISTS `constituents_families_households` (
  `id` int NOT NULL AUTO_INCREMENT,
  `family_id` int DEFAULT NULL,
  `household_id` int NOT NULL,
  `constituent_id` int NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `is_head` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`),
  KEY `household_id` (`household_id`),
  KEY `constituent_id` (`constituent_id`),
  CONSTRAINT `FK_constituents_families_households_constituents` FOREIGN KEY (`constituent_id`) REFERENCES `constituents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_constituents_families_households_families` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_constituents_families_households_households` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table re_bps.families
CREATE TABLE IF NOT EXISTS `families` (
  `id` int NOT NULL AUTO_INCREMENT,
  `household_id` int NOT NULL DEFAULT '0',
  `family_name` varchar(50) DEFAULT NULL,
  `head_constituent_id` int DEFAULT NULL,
  `date_resided` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `head_constituent_id` (`head_constituent_id`),
  KEY `household_id` (`household_id`),
  CONSTRAINT `FK__constituents` FOREIGN KEY (`head_constituent_id`) REFERENCES `constituents` (`id`),
  CONSTRAINT `FK__households` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table re_bps.households
CREATE TABLE IF NOT EXISTS `households` (
  `id` int NOT NULL AUTO_INCREMENT,
  `household_number` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT 'REGION VIII',
  `province` varchar(50) DEFAULT 'LEYTE',
  `city_municipality` varchar(50) DEFAULT 'TACLOBAN CITY',
  `barangay_code` varchar(50) DEFAULT '36-A',
  `barangay_name` varchar(50) DEFAULT 'IMELDA VILLAGE',
  `street_name` varchar(50) DEFAULT NULL,
  `zip_code` varchar(50) DEFAULT '6500',
  `purok` varchar(50) DEFAULT NULL,
  `block_number` varchar(50) DEFAULT NULL,
  `lot_number` varchar(50) DEFAULT NULL,
  `house_building_number` varchar(50) DEFAULT NULL,
  `unit_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table re_bps.officials
CREATE TABLE IF NOT EXISTS `officials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `constituent_id` int NOT NULL,
  `role` enum('PUNONG BARANGAY','SECRETARY','TREASURER','KONSEHAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'KONSEHAL',
  PRIMARY KEY (`id`),
  KEY `constituent_id` (`constituent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table re_bps.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction` varchar(255) NOT NULL,
  `requested_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `generated_by` varchar(255) NOT NULL,
  `document_location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `date_of_transaction` timestamp NOT NULL,
  `purpose` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table re_bps.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `constituent_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `constituent_id` (`constituent_id`),
  CONSTRAINT `FK_users_constituents` FOREIGN KEY (`constituent_id`) REFERENCES `constituents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
