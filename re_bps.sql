-- --------------------------------------------------------
-- Barangay Profile System (re_bps) - Database Setup Script
-- Generated from source code analysis
-- Compatible with: MySQL 8.0+
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- --------------------------------------------------------
-- DATABASE
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `re_bps`
  /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */
  /*!80016 DEFAULT ENCRYPTION='N' */;

USE `re_bps`;


-- --------------------------------------------------------
-- TABLE: classifications
-- Used by: Classifications model, ConstituentsClassifications model
-- Codes used in code: 'SC', 'OFW', 'OSY', 'PWD', 'OSC', 'IP', 'STUDENT'
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `classifications` (
  `id`           int          NOT NULL AUTO_INCREMENT,
  `code`         varchar(10)  NOT NULL,
  `name`         varchar(100) NOT NULL,
  `organization` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Seeded classifications matching actual database values
INSERT INTO `classifications` (`code`, `name`, `organization`) VALUES
  ('SC',          'SENIOR CITIZEN',                  'OSCA'),
  ('LABOR',       'Labor/Employed',                  NULL),
  ('UNEMP',       'Unemployed',                      NULL),
  ('PWD',         'PWD (Person with Disability)',     'PWD ID'),
  ('OFW',         'OFW (Overseas Filipino Worker)',   'OFW ID'),
  ('SOLOPARENT',  'Solo Parent',                     'Solo Parent ID'),
  ('OSY',         'Out of School Youth (OSY)',        NULL),
  ('OSC',         'Out of School Children (OSC)',     NULL),
  ('IP',          'IP (Indigenous People)',           NULL),
  ('STUDENT',     'Student',                         NULL);


-- --------------------------------------------------------
-- TABLE: constituents
-- Used by: Constituents model, ConstituentsClassifications, Households,
--          Family, BarangayOfficials, User models
-- Notes:
--   - education_attainment ENUM maps to:
--       1=Daycare, 2=Nursery, 3=Kinder,
--       4=Elementary, 5=ALS High School, 6=High School,
--       7=Junior High, 8=Senior High, 9=Vocational,
--       10=College, 11=Post Graduate
--   - removed_at used for soft-delete (archive) feature
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `constituents` (
  `id`                   int          NOT NULL AUTO_INCREMENT,
  `psn`                  varchar(16)  CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'PhilSys Number - optional, must be unique',
  `last_name`            varchar(50)  NOT NULL,
  `first_name`           varchar(100) NOT NULL,
  `middle_name`          varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `suffix`               varchar(10)  CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sex`                  enum('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
  `birthdate`            date         NOT NULL,
  `birthplace`           varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'TACLOBAN CITY',
  `civil_status`         enum('SINGLE','MARRIED','DIVORCED','SEPARATED','WIDOWED') NOT NULL DEFAULT 'SINGLE',
  `religion`             varchar(100) NOT NULL DEFAULT 'ROMAN CATHOLIC',
  `citizenship`          varchar(100) NOT NULL DEFAULT 'FILIPINO' COMMENT 'FILIPINO or custom value when OTHERS is selected',
  `occupation`           varchar(100) DEFAULT NULL,
  `contact`              varchar(15)  DEFAULT NULL,
  `email`                varchar(255) DEFAULT NULL,
  `education_attainment` enum('1','2','3','4','5','6','7','8','9','10','11') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_graduate`          enum('YES','NO') NOT NULL DEFAULT 'NO',
  `registered_voter`     enum('YES','NO') NOT NULL DEFAULT 'NO',
  `removed_at`           timestamp    NULL DEFAULT NULL COMMENT 'Soft delete timestamp - NULL means active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `psn` (`psn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: constituents_classifications
-- Used by: ConstituentsClassifications model
-- Notes:
--   - org_id_no unique key name is 'org_id_no' (used in parseDuplicateOrgIdError)
--   - The unique key on org_id_no enforces one org ID per classification entry
--   - unique_org_per_classification is the key name referenced in error handling
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `constituents_classifications` (
  `id`                int         NOT NULL AUTO_INCREMENT,
  `constituent_id`    int         NOT NULL,
  `classification_id` int         NOT NULL,
  `org_id_no`         varchar(50) DEFAULT NULL COMMENT 'Organization ID number - optional, unique per classification',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_org_per_classification` (`org_id_no`, `classification_id`),
  KEY `constituent_id` (`constituent_id`),
  KEY `classification_id` (`classification_id`),
  CONSTRAINT `FK_constituents_classifications_classifications`
    FOREIGN KEY (`classification_id`) REFERENCES `classifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_constituents_classifications_constituents`
    FOREIGN KEY (`constituent_id`) REFERENCES `constituents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
  COMMENT='Many-to-many relationship between constituents and classifications';


-- --------------------------------------------------------
-- TABLE: households
-- Used by: Households model
-- Notes:
--   - All address fields have barangay-specific defaults
--   - purok is stored in the 'purok' column (mapped from 'zone' POST field)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `households` (
  `id`                   int         NOT NULL AUTO_INCREMENT,
  `household_number`     varchar(50) DEFAULT NULL,
  `region`               varchar(50) DEFAULT 'REGION VIII',
  `province`             varchar(50) DEFAULT 'LEYTE',
  `city_municipality`    varchar(50) DEFAULT 'TACLOBAN CITY',
  `barangay_code`        varchar(50) DEFAULT '36-A',
  `barangay_name`        varchar(50) DEFAULT 'IMELDA VILLAGE',
  `street_name`          varchar(50) DEFAULT NULL,
  `zip_code`             varchar(10) DEFAULT '6500',
  `purok`                varchar(50) DEFAULT NULL,
  `block_number`         varchar(50) DEFAULT NULL,
  `lot_number`           varchar(50) DEFAULT NULL,
  `house_building_number` varchar(50) DEFAULT NULL,
  `unit_number`          varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: families
-- Used by: Family model, Households model
-- Notes:
--   - date_resided used in getTotalRecentFamiliesCount (5-year filter)
--   - head_constituent_id can be NULL (cleared when head is removed)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `families` (
  `id`                   int         NOT NULL AUTO_INCREMENT,
  `household_id`         int         NOT NULL DEFAULT '0',
  `family_name`          varchar(50) DEFAULT NULL,
  `head_constituent_id`  int         DEFAULT NULL COMMENT 'NULL when no head assigned or head is removed',
  `date_resided`         date        DEFAULT NULL COMMENT 'Used for recent family tracking (5-year window)',
  PRIMARY KEY (`id`),
  KEY `head_constituent_id` (`head_constituent_id`),
  KEY `household_id` (`household_id`),
  CONSTRAINT `FK_families_constituents`
    FOREIGN KEY (`head_constituent_id`) REFERENCES `constituents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_families_households`
    FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: constituents_families_households
-- Used by: Households model, Family model
-- Notes:
--   - family_id can be NULL (constituent in household but not yet in a family)
--   - is_head tracks the household head (not family head - that's in families table)
--   - role stores relationship role (e.g., 'Father', 'Mother', 'Son', etc.)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `constituents_families_households` (
  `id`             int         NOT NULL AUTO_INCREMENT,
  `family_id`      int         DEFAULT NULL COMMENT 'NULL if constituent not yet assigned to a family',
  `household_id`   int         NOT NULL,
  `constituent_id` int         NOT NULL,
  `role`           varchar(50) DEFAULT NULL COMMENT 'Relationship role within the household',
  `is_head`        enum('YES','NO') NOT NULL DEFAULT 'NO' COMMENT 'Household head flag',
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`),
  KEY `household_id` (`household_id`),
  KEY `constituent_id` (`constituent_id`),
  CONSTRAINT `FK_cfh_families`
    FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_cfh_households`
    FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_cfh_constituents`
    FOREIGN KEY (`constituent_id`) REFERENCES `constituents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: officials
-- Used by: BarangayOfficials model, Constituents model (remove checks officials)
-- Notes:
--   - Roles: PUNONG BARANGAY, SECRETARY, TREASURER, KONSEHAL
--   - hasRoleAssigned() checks if a role slot is filled
--   - When a constituent is archived, they are removed from this table
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `officials` (
  `id`             int  NOT NULL AUTO_INCREMENT,
  `constituent_id` int  NOT NULL,
  `role`           enum('PUNONG BARANGAY','SECRETARY','TREASURER','KONSEHAL')
                        CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
                        NOT NULL DEFAULT 'KONSEHAL',
  PRIMARY KEY (`id`),
  KEY `constituent_id` (`constituent_id`),
  CONSTRAINT `FK_officials_constituents`
    FOREIGN KEY (`constituent_id`) REFERENCES `constituents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: transactions
-- Used by: Transactions model, FormsController, HouseholdsController
-- Notes:
--   - Records every document/form generation
--   - document_location stores relative path e.g. 'public/forms/filename.pdf'
--   - date_of_transaction stored as DATETIME (Y-m-d H:i:s format from PHP)
--   - Paginated in dashboard (10 per page)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `transactions` (
  `id`                  int          NOT NULL AUTO_INCREMENT,
  `transaction`         varchar(255) NOT NULL COMMENT 'Form/document type name',
  `requested_by`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `generated_by`        varchar(255) NOT NULL COMMENT 'Username of logged-in user',
  `document_location`   text         CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Relative file path to generated PDF',
  `date_of_transaction` datetime     NOT NULL COMMENT 'Stored as Y-m-d H:i:s',
  `purpose`             varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: users
-- Used by: User model, AuthController, UsersController
-- Notes:
--   - password stored as bcrypt hash via password_hash() (VARCHAR 255)
--   - role: 'admin' for officials, 'constituent' for residents
--   - status: account approval state (pending/approved/rejected)
--   - constituent_id links user account to a constituent record (optional)
--   - fullname: display name of the account holder
--   - deleted_at: soft delete timestamp
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id`             int                                          NOT NULL AUTO_INCREMENT,
  `username`       varchar(50)                                  NOT NULL,
  `fullname`       varchar(100)                                 NOT NULL DEFAULT '',
  `email`          varchar(100)                                 DEFAULT NULL,
  `password`       varchar(255)                                 NOT NULL,
  `role`           enum('admin','constituent')                  NOT NULL DEFAULT 'admin',
  `status`         enum('pending','approved','rejected','deactivated') NOT NULL DEFAULT 'approved',
  `constituent_id` int                                          DEFAULT NULL,
  `deleted_at`     timestamp                                    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `constituent_id` (`constituent_id`),
  CONSTRAINT `FK_users_constituents`
    FOREIGN KEY (`constituent_id`) REFERENCES `constituents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Default admin account (password: admin123)
-- bcrypt hash of 'admin123'
INSERT INTO `users` (`username`, `fullname`, `email`, `password`, `role`, `status`) VALUES
  ('admin', 'Barangay Secretary', 'admin@brgy36a.gov.ph', '$2y$10$pLgezyG35ZkvzmPG0hWv1et5AWGF9nte80uQJTphXNiUpTUoM/ynO', 'admin', 'approved');
-- ⚠️  IMPORTANT: Change the default admin password AND email after first login!


-- --------------------------------------------------------
-- TABLE: email_otps
-- Used by: OTP/email verification feature
-- Notes:
--   - Stores one-time passwords sent via email
--   - expires_at used to invalidate old OTPs
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `email_otps` (
  `id`         int          NOT NULL AUTO_INCREMENT,
  `email`      varchar(100) NOT NULL,
  `otp`        varchar(10)  NOT NULL,
  `expires_at` datetime     NOT NULL,
  `created_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email_otp` (`email`, `otp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: constituent_profile_requests
-- Used by: ConstituentController, UsersController
-- Notes:
--   - Stores constituent-submitted profile payloads for admin approval
--   - Payload kept as JSON text to avoid writing unverified data to live records
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `constituent_profile_requests` (
  `id`             int NOT NULL AUTO_INCREMENT,
  `user_id`        int NOT NULL,
  `constituent_id` int DEFAULT NULL,
  `payload_json`   longtext NOT NULL,
  `status`         enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes`    text DEFAULT NULL,
  `reviewed_by`    int DEFAULT NULL,
  `reviewed_at`    datetime DEFAULT NULL,
  `created_at`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cpr_user` (`user_id`),
  KEY `idx_cpr_status` (`status`),
  KEY `idx_cpr_constituent` (`constituent_id`),
  KEY `idx_cpr_reviewed_by` (`reviewed_by`),
  CONSTRAINT `FK_cpr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_cpr_constituent` FOREIGN KEY (`constituent_id`) REFERENCES `constituents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_cpr_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: login_attempts
-- Used by: AuthController (rate limiting)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id`           int         NOT NULL AUTO_INCREMENT,
  `ip_address`   varchar(45) NOT NULL,
  `username`     varchar(50) NOT NULL DEFAULT '',
  `attempted_at` datetime    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_time` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- TABLE: password_resets
-- Used by: AuthController (email-based token reset)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         int         NOT NULL AUTO_INCREMENT,
  `email`      varchar(100) NOT NULL,
  `token`      varchar(64)  NOT NULL,
  `created_at` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- Restore settings
-- --------------------------------------------------------

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;