-- MySQL dump 10.13  Distrib 9.6.0, for macos26.2 (arm64)
--
-- Host: localhost    Database: lentera_project
-- ------------------------------------------------------
-- Server version	8.0.35

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_id` bigint unsigned DEFAULT NULL,
  `consultant_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `times` json NOT NULL,
  `hours` int unsigned NOT NULL DEFAULT '1',
  `rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(14,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `rating` tinyint unsigned DEFAULT NULL,
  `review` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_token_unique` (`token`),
  KEY `bookings_date_index` (`date`),
  KEY `bookings_member_id_index` (`member_id`),
  CONSTRAINT `bookings_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,'Calon Klien','calon.klien@example.com','0812-3456-7890','Tanya paket mentoring','Halo, saya tertarik dengan program mentoring 1-on-1. Bisa minta info jadwal dan detailnya?','new','2026-08-10 00:07:10','2026-08-10 00:07:10');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tagline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience_years` tinyint unsigned NOT NULL DEFAULT '0',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joined_at` date DEFAULT NULL,
  `consultation_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `telegram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tradingview` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `binance_api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `binance_api_secret` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `members_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'Azharuddin Arrosis','azharuddin-arrosis','Founder & Head of Trading','Strategi tanpa disiplin hanyalah harapan. Kami jalankan keduanya.','Azharuddin adalah founder Lentera Project sekaligus kepala tim trading. Berpengalaman lebih dari 8 tahun di pasar kripto & derivatif, ia menggabungkan analisis teknikal, data on-chain, dan konteks makro untuk mengambil keputusan yang terukur. Fokusnya membangun sistem trading yang konsisten dan mendidik anggota agar mandiri, bukan sekadar ikut sinyal.','/images/team/azhar.jpg','Crypto Futures · Macro & On-chain',8,'Surabaya, Indonesia','2018-01-10',500000.00,'https://t.me/azharuddinarrosis','https://instagram.com/azharuddin.arrosis','https://x.com/azhararrosis','https://tradingview.com/u/azharuddinarrosis','https://lentera.id',NULL,NULL,1,1,0,'2026-08-10 00:07:10','2026-08-10 09:58:13'),(2,'Krisna Catur Agung Perdana','krisna-catur-agung-perdana','Konsultan Trading · Crypto & Futures','Eksekusi disiplin, kelola risiko, tumbuh konsisten.','Krisna adalah konsultan trading di Lentera Project dengan fokus pada crypto & futures. Pendekatannya menekankan proses yang berulang: rencana jelas, manajemen risiko ketat, dan evaluasi berkala agar anggota mampu trading secara mandiri.',NULL,'Crypto Futures · Price Action',5,'Surabaya, Indonesia','2020-05-01',300000.00,'https://t.me/krisnacatur','https://instagram.com/krisna.trades',NULL,NULL,NULL,NULL,NULL,1,1,1,'2026-08-10 00:07:10','2026-08-10 09:58:13');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000000_create_passkeys_table',1),(5,'2025_08_14_170933_add_two_factor_columns_to_users_table',1),(6,'2026_08_09_100320_create_members_table',1),(7,'2026_08_09_100321_create_trades_table',1),(8,'2026_08_09_100322_create_services_table',1),(9,'2026_08_09_100323_create_testimonials_table',1),(10,'2026_08_09_100324_create_leads_table',1),(11,'2026_08_09_144648_create_bookings_table',1),(12,'2026_08_09_152946_add_payment_to_bookings_table',1),(13,'2026_08_09_152947_create_schedule_blocks_table',1),(14,'2026_08_09_155000_add_role_to_users_table',1),(15,'2026_08_09_160000_add_rating_and_proof_to_bookings_table',1),(16,'2026_08_10_100000_add_token_and_proof_meta_to_bookings_table',1),(17,'2026_08_10_110000_create_signals_table',1),(18,'2026_08_10_120000_create_withdrawals_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `passkeys`
--

DROP TABLE IF EXISTS `passkeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passkeys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credential_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credential` json NOT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `passkeys_credential_id_unique` (`credential_id`),
  KEY `passkeys_user_id_index` (`user_id`),
  CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `passkeys`
--

LOCK TABLES `passkeys` WRITE;
/*!40000 ALTER TABLE `passkeys` DISABLE KEYS */;
/*!40000 ALTER TABLE `passkeys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_blocks`
--

DROP TABLE IF EXISTS `schedule_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_blocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `times` json DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedule_blocks_member_id_foreign` (`member_id`),
  KEY `schedule_blocks_date_index` (`date`),
  CONSTRAINT `schedule_blocks_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_blocks`
--

LOCK TABLES `schedule_blocks` WRITE;
/*!40000 ALTER TABLE `schedule_blocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sparkles',
  `price_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `features` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Sinyal Trading Harian',NULL,'Sinyal entry, target, dan stop-loss yang jelas langsung ke Telegram, lengkap dengan alasan analisis.','signal','Mulai Rp299rb / bln','[\"Sinyal harian BTC/ETH & altcoin pilihan\", \"Entry, TP, SL jelas\", \"Update kondisi market\", \"Grup diskusi member\"]',1,1,1,'2026-08-10 00:07:10','2026-08-10 00:07:10'),(2,'Mentoring 1-on-1',NULL,'Bimbingan privat dari nol sampai bisa: dasar analisis, strategi, sampai psikologi & manajemen risiko.','graduation-cap','Rp1,5jt / paket','[\"6x sesi privat\", \"Kurikulum bertahap\", \"Review jurnal trading\", \"Konsultasi via chat\"]',1,1,2,'2026-08-10 00:07:10','2026-08-10 00:07:10'),(3,'Analisis Portofolio',NULL,'Audit portofolio & gaya trading Anda, temukan kebocoran dan susun rencana perbaikan yang terukur.','chart-pie','Rp500rb / sesi','[\"Review posisi & histori\", \"Evaluasi manajemen risiko\", \"Rekomendasi perbaikan\", \"Laporan tertulis\"]',1,0,3,'2026-08-10 00:07:10','2026-08-10 00:07:10'),(4,'Managed Insight',NULL,'Pendampingan intensif untuk klien serius: strategi disesuaikan dengan profil risiko & modal Anda.','briefcase','Custom','[\"Strategi personal\", \"Laporan performa berkala\", \"Prioritas konsultasi\", \"Evaluasi bulanan\"]',1,0,4,'2026-08-10 00:07:10','2026-08-10 00:07:10');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('6alw5633Wk1TGWxI0bEoxwCXIpRCaqyw9LUCmXVl',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI0c1dxaTNsN1Nwdkx4TFdLVGplOGIxMlBpZmQxSXBSSlAxOUZZc21sIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hdmF0YXJcLzIiLCJyb3V0ZSI6ImF2YXRhciJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1786381240),('PfpyLvKRA6Vu0Z9pDlykcC2y081XGpEgAEsKkn6r',NULL,'127.0.0.1','curl/8.7.1','eyJfdG9rZW4iOiJRYXZtb3llOVBUcVJaTllDc1RrVEtid3F0ZDVDeVcybThZZ0dhU0JXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1786381194);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signals`
--

DROP TABLE IF EXISTS `signals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned DEFAULT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tim Lentera',
  `pair` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `market` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'futures',
  `side` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'long',
  `timeframe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leverage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry` decimal(20,8) DEFAULT NULL,
  `entry_max` decimal(20,8) DEFAULT NULL,
  `stop_loss` decimal(20,8) DEFAULT NULL,
  `take_profits` json DEFAULT NULL,
  `analysis` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pnl_percent` decimal(8,2) DEFAULT NULL,
  `is_premium` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signals_member_id_foreign` (`member_id`),
  KEY `signals_status_published_at_index` (`status`,`published_at`),
  CONSTRAINT `signals_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signals`
--

LOCK TABLES `signals` WRITE;
/*!40000 ALTER TABLE `signals` DISABLE KEYS */;
/*!40000 ALTER TABLE `signals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quote` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint unsigned NOT NULL DEFAULT '5',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'Rizal H.','Member Sinyal · 8 bulan','Sinyalnya jelas dan yang paling penting diajari manajemen risiko. Akun saya akhirnya konsisten hijau.',5,NULL,1,1,'2026-08-10 00:07:10','2026-08-10 00:07:10'),(2,'Dewi K.','Alumni Mentoring','Dari benar-benar nol sampai paham baca chart. Mentoringnya sabar dan ngga menggurui. Recommended!',5,NULL,1,2,'2026-08-10 00:07:10','2026-08-10 00:07:10'),(3,'Yoga P.','Klien Analisis Portofolio','Baru sadar banyak kebocoran di cara trading saya setelah diaudit tim Lentera. Worth it banget.',5,NULL,1,3,'2026-08-10 00:07:10','2026-08-10 00:07:10');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trades`
--

DROP TABLE IF EXISTS `trades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `market` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'futures',
  `side` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'long',
  `leverage` decimal(6,2) DEFAULT NULL,
  `entry_price` decimal(20,8) DEFAULT NULL,
  `exit_price` decimal(20,8) DEFAULT NULL,
  `quantity` decimal(20,8) DEFAULT NULL,
  `pnl` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `roi` decimal(10,4) DEFAULT NULL,
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'closed',
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `binance_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `opened_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trades_member_id_binance_id_unique` (`member_id`,`binance_id`),
  KEY `trades_member_id_closed_at_index` (`member_id`,`closed_at`),
  CONSTRAINT `trades_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trades`
--

LOCK TABLES `trades` WRITE;
/*!40000 ALTER TABLE `trades` DISABLE KEYS */;
INSERT INTO `trades` VALUES (93,1,'BNBUSDT','futures','short',20.00,535.00000000,540.88500000,3.10000000,498.00000000,86.1600,9.96000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-02-10 05:58:13','2026-02-10 10:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(94,1,'SOLUSDT','futures','short',20.00,157.00000000,158.88400000,2.90000000,93.00000000,30.3900,1.86000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-02-12 15:58:13','2026-02-13 09:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(95,1,'BTCUSDT','futures','short',5.00,66305.00000000,65177.81500000,3.10000000,-172.80000000,-67.2400,3.46000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-02-15 10:58:13','2026-02-16 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(96,1,'ETHUSDT','futures','long',20.00,2952.00000000,3070.08000000,4.00000000,384.00000000,65.4200,7.68000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-02-20 04:58:13','2026-02-20 14:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(97,1,'ETHUSDT','futures','long',10.00,3140.00000000,3287.58000000,3.80000000,390.00000000,103.4500,7.80000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-02-22 21:58:13','2026-02-23 15:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(98,1,'SOLUSDT','futures','long',5.00,146.00000000,142.05800000,0.90000000,-56.70000000,-25.0900,1.13000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-02-26 21:58:13','2026-02-27 21:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(99,1,'SOLUSDT','futures','long',5.00,157.00000000,162.33800000,0.30000000,279.00000000,53.0400,5.58000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-01 16:58:13','2026-03-02 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(100,1,'SOLUSDT','futures','long',10.00,187.00000000,187.93500000,0.80000000,591.00000000,195.7000,11.82000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-06 15:58:13','2026-03-07 03:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(101,1,'SOLUSDT','futures','long',20.00,167.00000000,169.67200000,4.30000000,564.00000000,101.8100,11.28000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-08 17:58:13','2026-03-09 09:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(102,1,'SOLUSDT','futures','long',3.00,200.00000000,209.20000000,4.10000000,198.00000000,45.4100,3.96000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-13 03:58:13','2026-03-13 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(103,1,'ETHUSDT','futures','short',20.00,3744.00000000,3890.01600000,4.50000000,321.00000000,83.3800,6.42000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-15 06:58:13','2026-03-16 12:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(104,1,'BNBUSDT','futures','long',10.00,544.00000000,549.98400000,1.70000000,231.00000000,56.2000,4.62000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-20 16:58:13','2026-03-20 19:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(105,1,'BNBUSDT','futures','short',5.00,592.00000000,601.47200000,2.80000000,474.00000000,90.4600,9.48000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-23 19:58:13','2026-03-23 21:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(106,1,'BTCUSDT','futures','short',10.00,58051.00000000,60082.78500000,1.90000000,126.00000000,22.9500,2.52000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-27 17:58:13','2026-03-27 18:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(107,1,'BNBUSDT','futures','long',5.00,646.00000000,656.33600000,1.30000000,588.00000000,266.0600,11.76000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-29 15:58:13','2026-03-30 09:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(108,1,'BNBUSDT','futures','long',10.00,535.00000000,561.21500000,3.50000000,579.00000000,248.5000,11.58000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-04-01 21:58:13','2026-04-03 02:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(109,1,'BNBUSDT','futures','short',20.00,582.00000000,613.42800000,1.90000000,468.00000000,99.7900,9.36000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-04-06 09:58:13','2026-04-06 10:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(110,1,'SOLUSDT','futures','long',5.00,180.00000000,176.40000000,0.70000000,-82.80000000,-37.4700,1.66000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-04-09 14:58:13','2026-04-10 00:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(111,1,'BTCUSDT','futures','short',10.00,71093.00000000,72728.13900000,0.20000000,468.00000000,80.4100,9.36000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-04-13 03:58:13','2026-04-14 04:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(112,1,'ETHUSDT','futures','short',10.00,3513.00000000,3657.03300000,2.30000000,543.00000000,98.7300,10.86000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-04-15 17:58:13','2026-04-16 14:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(113,1,'BTCUSDT','futures','short',5.00,65934.00000000,67780.15200000,3.60000000,363.00000000,120.6000,7.26000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-04-20 12:58:13','2026-04-20 15:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(114,1,'ETHUSDT','futures','long',3.00,3507.00000000,3468.42300000,2.40000000,-156.00000000,-33.9100,3.12000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-04-22 21:58:13','2026-04-23 10:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(115,1,'SOLUSDT','futures','short',3.00,169.00000000,172.71800000,1.50000000,333.00000000,78.5400,6.66000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-04-27 05:58:13','2026-04-27 23:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(116,1,'BNBUSDT','futures','short',5.00,602.00000000,569.49200000,0.40000000,-80.64000000,-17.2300,1.61000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-04-29 14:58:13','2026-04-30 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(117,1,'ETHUSDT','futures','short',10.00,3724.00000000,3571.31600000,2.80000000,-336.00000000,-103.3800,6.72000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-04 15:58:13','2026-05-05 00:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(118,1,'BNBUSDT','futures','short',10.00,597.00000000,604.76100000,2.50000000,201.00000000,45.4800,4.02000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-05-06 11:58:13','2026-05-07 17:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(119,1,'ETHUSDT','futures','short',10.00,3518.00000000,3679.82800000,2.90000000,147.00000000,33.3300,2.94000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-05-10 05:58:13','2026-05-11 10:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(120,1,'BTCUSDT','futures','short',20.00,61163.00000000,58288.33900000,0.80000000,-212.16000000,-88.4000,4.24000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-14 01:58:13','2026-05-15 02:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(121,1,'ETHUSDT','futures','long',5.00,3737.00000000,3523.99100000,3.60000000,-383.46000000,-141.5000,7.67000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-16 16:58:13','2026-05-17 14:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(122,1,'BTCUSDT','futures','short',10.00,58625.00000000,55693.75000000,0.40000000,-201.39000000,-61.0300,4.03000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-20 09:58:13','2026-05-21 10:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(123,1,'SOLUSDT','futures','long',20.00,173.00000000,171.96200000,4.00000000,-222.30000000,-50.6400,4.45000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-23 18:58:13','2026-05-24 18:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(124,1,'BTCUSDT','futures','short',20.00,70620.00000000,73232.94000000,2.80000000,303.00000000,136.4900,6.06000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-05-28 08:58:13','2026-05-29 04:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(125,1,'BTCUSDT','futures','long',10.00,64593.00000000,62848.98900000,0.30000000,-192.24000000,-58.2500,3.84000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-30 23:58:13','2026-05-31 11:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(126,1,'ETHUSDT','futures','short',20.00,3801.00000000,3934.03500000,0.10000000,348.00000000,105.1400,6.96000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-06-04 22:58:13','2026-06-05 02:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(127,1,'BNBUSDT','futures','short',3.00,603.00000000,622.89900000,1.70000000,162.00000000,34.8400,3.24000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-06-07 07:58:13','2026-06-08 00:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(128,1,'BNBUSDT','futures','long',10.00,599.00000000,596.00500000,2.10000000,-66.78000000,-23.1900,1.34000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-11 08:58:13','2026-06-11 10:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(129,1,'ETHUSDT','futures','long',3.00,3310.00000000,3432.47000000,4.00000000,261.00000000,76.3200,5.22000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-06-14 10:58:13','2026-06-15 01:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(130,1,'ETHUSDT','futures','short',5.00,3131.00000000,3021.41500000,1.80000000,-285.12000000,-51.0100,5.70000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-18 05:58:13','2026-06-18 18:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(131,1,'BTCUSDT','futures','long',5.00,61930.00000000,62487.37000000,0.10000000,393.00000000,94.9300,7.86000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-06-21 13:58:13','2026-06-22 02:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(132,1,'BNBUSDT','futures','short',5.00,597.00000000,568.94100000,0.50000000,-418.32000000,-73.1300,8.37000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-26 01:58:13','2026-06-26 03:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(133,1,'BNBUSDT','futures','long',3.00,628.00000000,611.04400000,0.90000000,-291.51000000,-74.3600,5.83000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-28 21:58:13','2026-06-29 04:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(134,1,'ETHUSDT','futures','short',10.00,3274.00000000,3116.84800000,0.80000000,-145.53000000,-52.1600,2.91000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-07-01 09:58:13','2026-07-01 19:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(135,1,'ETHUSDT','futures','long',10.00,3340.00000000,3146.28000000,3.80000000,-299.04000000,-84.0000,5.98000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-07-06 03:58:13','2026-07-06 04:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(136,1,'SOLUSDT','futures','short',5.00,202.00000000,204.62600000,0.80000000,237.00000000,58.2300,4.74000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-08 01:58:13','2026-07-08 13:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(137,1,'BTCUSDT','futures','long',10.00,69049.00000000,65941.79500000,1.20000000,-93.96000000,-34.9300,1.88000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-07-12 04:58:13','2026-07-12 20:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(138,1,'BNBUSDT','futures','short',10.00,550.00000000,562.10000000,4.80000000,93.00000000,25.3400,1.86000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-14 20:58:13','2026-07-15 17:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(139,1,'SOLUSDT','futures','short',3.00,205.00000000,212.38000000,1.50000000,525.00000000,128.3600,10.50000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-19 08:58:13','2026-07-19 23:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(140,1,'SOLUSDT','futures','short',10.00,143.00000000,134.70600000,3.70000000,-287.10000000,-48.9100,5.74000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-07-22 15:58:13','2026-07-22 22:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(141,1,'ETHUSDT','futures','short',5.00,3552.00000000,3662.11200000,4.40000000,288.00000000,70.4200,5.76000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-26 22:58:13','2026-07-27 05:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(142,1,'ETHUSDT','futures','short',10.00,3486.00000000,3566.17800000,3.50000000,297.00000000,61.4900,5.94000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-28 15:58:13','2026-07-29 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(143,1,'SOLUSDT','futures','long',10.00,194.00000000,201.56600000,0.90000000,171.00000000,34.3400,3.42000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-08-03 02:58:13','2026-08-03 04:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(144,1,'ETHUSDT','futures','long',20.00,3690.00000000,3904.02000000,5.00000000,534.00000000,171.1500,10.68000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-08-04 16:58:13','2026-08-05 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(145,2,'BTCUSDT','futures','long',3.00,58851.00000000,55849.59900000,2.20000000,-188.80000000,-35.7600,3.78000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-02-09 06:58:13','2026-02-10 12:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(146,2,'SOLUSDT','futures','long',20.00,205.00000000,206.64000000,3.40000000,140.00000000,44.0300,2.80000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-02-14 17:58:13','2026-02-15 03:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(147,2,'BNBUSDT','futures','short',10.00,550.00000000,520.85000000,4.20000000,-232.96000000,-49.8800,4.66000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-02-19 09:58:13','2026-02-20 04:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(148,2,'BTCUSDT','futures','long',5.00,70662.00000000,71368.62000000,4.20000000,154.00000000,73.6800,3.08000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-02-23 18:58:13','2026-02-24 01:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(149,2,'SOLUSDT','futures','long',20.00,157.00000000,156.21500000,0.20000000,-135.72000000,-33.7600,2.71000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-02-27 20:58:13','2026-02-28 20:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(150,2,'BTCUSDT','futures','short',5.00,70951.00000000,71589.55900000,4.90000000,114.00000000,21.2300,2.28000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-04 12:58:13','2026-03-05 01:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(151,2,'BTCUSDT','futures','long',3.00,64979.00000000,68617.82400000,0.70000000,206.00000000,42.0400,4.12000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-09 06:58:13','2026-03-09 21:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(152,2,'SOLUSDT','futures','short',10.00,159.00000000,164.56500000,1.20000000,344.00000000,62.7700,6.88000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-13 17:58:13','2026-03-14 03:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(153,2,'ETHUSDT','futures','long',10.00,3793.00000000,3648.86600000,3.60000000,-174.84000000,-35.3200,3.50000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-03-17 21:58:13','2026-03-18 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(154,2,'SOLUSDT','futures','long',5.00,166.00000000,175.29600000,1.50000000,284.00000000,58.4400,5.68000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-21 18:58:13','2026-03-22 21:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(155,2,'BNBUSDT','futures','long',10.00,521.00000000,531.94100000,1.00000000,246.00000000,45.3900,4.92000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-03-26 17:58:13','2026-03-27 15:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(156,2,'BNBUSDT','futures','short',5.00,600.00000000,573.60000000,1.30000000,-121.04000000,-36.9000,2.42000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-03-31 14:58:13','2026-03-31 22:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(157,2,'BNBUSDT','futures','long',20.00,623.00000000,592.47300000,3.50000000,-77.22000000,-33.1400,1.54000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-04-05 15:58:13','2026-04-05 19:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(158,2,'SOLUSDT','futures','long',5.00,188.00000000,180.66800000,5.00000000,-72.36000000,-16.8300,1.45000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-04-09 09:58:13','2026-04-10 02:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(159,2,'BTCUSDT','futures','short',3.00,67229.00000000,71195.51100000,3.60000000,74.00000000,17.9600,1.48000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-04-15 04:58:13','2026-04-15 05:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(160,2,'BNBUSDT','futures','short',10.00,644.00000000,672.98000000,1.80000000,198.00000000,36.0000,3.96000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-04-17 12:58:13','2026-04-18 13:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(161,2,'BNBUSDT','futures','short',10.00,550.00000000,524.70000000,0.10000000,-221.52000000,-91.1600,4.43000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-04-23 17:58:13','2026-04-23 18:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(162,2,'BNBUSDT','futures','short',5.00,578.00000000,556.61400000,1.40000000,-190.24000000,-31.7100,3.80000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-04-26 21:58:13','2026-04-27 21:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(163,2,'BTCUSDT','futures','long',10.00,66487.00000000,69345.94100000,5.00000000,206.00000000,37.2500,4.12000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-05-01 20:58:13','2026-05-02 17:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(164,2,'SOLUSDT','futures','long',10.00,151.00000000,157.34200000,2.80000000,282.00000000,104.0600,5.64000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-05-06 14:58:13','2026-05-06 15:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(165,2,'SOLUSDT','futures','short',10.00,134.00000000,141.23600000,0.10000000,284.00000000,54.6200,5.68000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-05-11 05:58:13','2026-05-12 01:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(166,2,'BNBUSDT','futures','short',20.00,601.00000000,595.59100000,3.80000000,-59.50000000,-13.2500,1.19000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-15 21:58:13','2026-05-15 23:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(167,2,'SOLUSDT','futures','short',10.00,195.00000000,189.54000000,1.20000000,-54.02000000,-16.1700,1.08000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-20 20:58:13','2026-05-21 03:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(168,2,'BTCUSDT','futures','short',10.00,58644.00000000,57881.62800000,3.10000000,-64.32000000,-16.4100,1.29000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-05-24 23:58:13','2026-05-25 01:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(169,2,'BTCUSDT','futures','short',10.00,59804.00000000,62554.98400000,3.00000000,244.00000000,48.4100,4.88000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-05-29 05:58:13','2026-05-29 22:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(170,2,'ETHUSDT','futures','short',10.00,3280.00000000,3378.40000000,1.10000000,380.00000000,155.1000,7.60000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-06-02 09:58:13','2026-06-02 20:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(171,2,'BTCUSDT','futures','short',20.00,61573.00000000,59972.10200000,2.00000000,-180.18000000,-36.1800,3.60000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-07 14:58:13','2026-06-07 23:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(172,2,'ETHUSDT','futures','long',3.00,3848.00000000,3797.97600000,1.40000000,-172.48000000,-35.5600,3.45000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-11 08:58:13','2026-06-11 21:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(173,2,'BTCUSDT','futures','short',10.00,59662.00000000,58230.11200000,1.30000000,-106.48000000,-25.7800,2.13000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-15 19:58:13','2026-06-16 09:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(174,2,'BTCUSDT','futures','long',10.00,59843.00000000,57509.12300000,3.00000000,-67.86000000,-13.7600,1.36000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-20 11:58:13','2026-06-21 01:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(175,2,'SOLUSDT','futures','short',10.00,205.00000000,199.05500000,1.40000000,-199.50000000,-43.9400,3.99000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-24 16:58:13','2026-06-25 17:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(176,2,'BNBUSDT','futures','short',5.00,573.00000000,562.11300000,1.60000000,-240.00000000,-80.0000,4.80000000,'closed','manual',NULL,'Kena SL, market berbalik. Disiplin cut loss.','2026-06-29 05:58:13','2026-06-29 22:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(177,2,'ETHUSDT','futures','short',20.00,3192.00000000,3354.79200000,0.60000000,346.00000000,129.1000,6.92000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-04 08:58:13','2026-07-04 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(178,2,'SOLUSDT','futures','short',20.00,161.00000000,170.17700000,4.20000000,288.00000000,70.5900,5.76000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-07 18:58:13','2026-07-08 09:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(179,2,'BTCUSDT','futures','long',10.00,71208.00000000,72916.99200000,0.70000000,280.00000000,47.0600,5.60000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-12 19:58:13','2026-07-13 14:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(180,2,'ETHUSDT','futures','short',10.00,3566.00000000,3612.35800000,2.50000000,64.00000000,22.2200,1.28000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-16 23:58:13','2026-07-18 02:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(181,2,'SOLUSDT','futures','short',10.00,137.00000000,139.19200000,2.40000000,152.00000000,27.0500,3.04000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-22 14:58:13','2026-07-22 16:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(182,2,'BTCUSDT','futures','long',5.00,64632.00000000,66441.69600000,3.10000000,198.00000000,75.2900,3.96000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-25 19:58:13','2026-07-26 23:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(183,2,'BTCUSDT','futures','long',3.00,70415.00000000,72597.86500000,3.10000000,114.00000000,42.7000,2.28000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-07-31 09:58:13','2026-07-31 12:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13'),(184,2,'BNBUSDT','futures','short',10.00,600.00000000,612.00000000,2.90000000,60.00000000,10.8700,1.20000000,'closed','manual',NULL,'Setup sesuai plan, TP tercapai.','2026-08-04 20:58:13','2026-08-05 05:58:13','2026-08-10 09:58:13','2026-08-10 09:58:13');
/*!40000 ALTER TABLE `trades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `member_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_member_id_foreign` (`member_id`),
  CONSTRAINT `users_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Lentera','admin@lentera.id','admin',NULL,NULL,'$2y$12$8G0B29iIN.2eus2G.jMVN.9YYsGEuY.ydgen6f7fidNggFlfWGWC6',NULL,NULL,NULL,NULL,'2026-08-10 00:07:10','2026-08-10 09:58:13'),(2,'Azharuddin Arrosis','azharuddin@lentera.id','team',1,NULL,'$2y$12$G7dynkmf3ud3JGVyvTUCSOTe5yRuxOVjf6viK/iOpW5LoTuvjzLBm',NULL,NULL,NULL,NULL,'2026-08-10 00:07:10','2026-08-10 09:58:13'),(3,'Krisna Catur Agung Perdana','krisna@lentera.id','team',2,NULL,'$2y$12$SaajC.f2FbpCOwMNZnau8uv3FSpheDzytQjO.mMw01Ky1XFbWH59y',NULL,NULL,NULL,NULL,'2026-08-10 00:07:10','2026-08-10 09:58:13');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdrawals`
--

DROP TABLE IF EXISTS `withdrawals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdrawals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `withdrawn_at` date NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdrawals_member_id_withdrawn_at_index` (`member_id`,`withdrawn_at`),
  CONSTRAINT `withdrawals_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdrawals`
--

LOCK TABLES `withdrawals` WRITE;
/*!40000 ALTER TABLE `withdrawals` DISABLE KEYS */;
INSERT INTO `withdrawals` VALUES (1,1,1500.00000000,'2026-04-12','USDT TRC20','Tarik profit bulanan','2026-08-10 09:58:13','2026-08-10 09:58:13'),(2,1,2000.00000000,'2026-06-11','USDT TRC20','Realisasi profit','2026-08-10 09:58:13','2026-08-10 09:58:13'),(3,1,1200.00000000,'2026-07-21','Bank BCA',NULL,'2026-08-10 09:58:13','2026-08-10 09:58:13'),(4,2,800.00000000,'2026-05-12','USDT TRC20','Tarik profit','2026-08-10 09:58:13','2026-08-10 09:58:13'),(5,2,1000.00000000,'2026-07-16','Bank Mandiri',NULL,'2026-08-10 09:58:13','2026-08-10 09:58:13');
/*!40000 ALTER TABLE `withdrawals` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-11  0:04:09
