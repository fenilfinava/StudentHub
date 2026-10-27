-- MySQL dump 10.13  Distrib 26.7.0, for macos27.0 (arm64)
--
-- Host: localhost    Database: studenthub_db
-- ------------------------------------------------------
-- Server version	26.7.0

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
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'SUCCESS',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,6,'admin01','ADMIN_LOGIN','Administrator admin01 authenticated successfully via password verify.','127.0.0.1',NULL,'SUCCESS','2026-10-27 10:39:28'),(2,6,'admin01','VIEW_DASHBOARD','Administrator accessed central management console.','127.0.0.1',NULL,'SUCCESS','2026-10-27 10:44:28'),(3,6,'admin01','RBAC_VERIFY','Role authorization confirmed: role=admin permissions granted.','127.0.0.1',NULL,'SUCCESS','2026-10-27 10:49:28'),(4,1,'student01','ACCESS_DENIED','Unauthorized student access to admin_dashboard.php blocked with HTTP 403.','127.0.0.1',NULL,'SECURITY_BLOCK','2026-10-27 10:54:28'),(5,6,'admin01','STUDENT_CREATE','Added new student record: Rohan Patel (Cyber Security).','127.0.0.1',NULL,'SUCCESS','2026-10-27 11:04:28'),(6,6,'admin01','EVENT_UPDATE','Updated event details and poster for Odoo Hackathon 2026.','127.0.0.1',NULL,'SUCCESS','2026-10-27 11:09:28'),(7,2,'fenilfinava','STUDENT_LOGIN','Student fenilfinava (25CS011) authenticated and accessed Student Dashboard.','127.0.0.1',NULL,'SUCCESS','2026-10-27 11:19:28'),(8,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 11:32:58'),(9,1,'student01','USER_LOGIN','User logged in with role=student','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 11:32:59'),(10,1,'student01','VIEW_STUDENT_DASHBOARD','Student accessed dashboard.','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 11:32:59'),(11,1,'student01','ACCESS_DENIED','Unauthorized role=\'student\' attempted to access admin_dashboard.php','127.0.0.1','curl/8.7.1','SECURITY_BLOCK','2026-10-27 11:32:59'),(12,NULL,'admin01gmail.com','LOGIN_FAILED','Non-existent account attempted: admin01gmail.com','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Antigravity/2.26.0 Chrome/152.0.7977.130 Electron/44.4.3 Safari/537.36','FAILED','2026-10-27 11:39:06'),(13,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Antigravity/2.26.0 Chrome/152.0.7977.130 Electron/44.4.3 Safari/537.36','SUCCESS','2026-10-27 11:39:18'),(14,6,'admin01','VIEW_STUDENT_DASHBOARD','Student accessed dashboard.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Antigravity/2.26.0 Chrome/152.0.7977.130 Electron/44.4.3 Safari/537.36','SUCCESS','2026-10-27 11:39:25'),(15,1,'student01','USER_LOGIN','User logged in with role=student','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 11:57:49'),(16,1,'student01','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 11:57:49'),(17,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 11:58:03'),(18,NULL,'fenilfinava','LOGIN_FAILED','Invalid password attempt for: fenilfinava','127.0.0.1','Python-urllib/3.9','FAILED','2026-10-27 11:58:58'),(19,2,'fenilfinava','USER_LOGIN','User logged in with role=student','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 11:59:36'),(20,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 11:59:36'),(21,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 11:59:36'),(22,2,'fenilfinava','USER_LOGIN','User logged in with role=student','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:00'),(23,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:00'),(24,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:00'),(25,2,'fenilfinava','USER_LOGIN','User logged in with role=student','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:09'),(26,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:09'),(27,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:09'),(28,2,'fenilfinava','USER_LOGIN','User logged in with role=student','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:53'),(29,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:53'),(30,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:00:53'),(31,1,'student01','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:01:48'),(32,1,'student01','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:02:17'),(33,1,'student01','USER_LOGOUT','User logged out successfully.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:02:22'),(34,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:02:41'),(35,6,'admin01','USER_LOGOUT','User logged out successfully.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:02:47'),(36,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:07:59'),(37,6,'admin01','USER_LOGOUT','User logged out successfully.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:08:07'),(38,1,'student01','USER_LOGIN','User logged in with role=student','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:08:20'),(39,1,'student01','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:08:20'),(40,7,'charusat123','USER_REGISTER','New student self-registered: charusat (charusat123@gmail.com)','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:11:45'),(41,1,'student01','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:11:45'),(42,1,'student01','ACCESS_DENIED','Unauthorized role=\'student\' attempted to access admin_dashboard.php','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SECURITY_BLOCK','2026-10-27 12:14:16'),(43,1,'student01','USER_LOGOUT','User logged out successfully.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:14:19'),(44,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:14:29'),(45,7,'charusat123','USER_LOGIN','User logged in with role=student','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:16:50'),(46,7,'charusat123','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:16:50'),(47,7,'charusat123','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:16:50'),(48,8,'priyank2026','USER_REGISTER','New student registered and auto-logged in: Priyank Patel (priyank2026@gmail.com)','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:17:01'),(49,8,'priyank2026','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Python-urllib/3.9','SUCCESS','2026-10-27 12:17:01'),(50,6,'admin01','EXPORT_CSV','Admin exported student records to CSV.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:17:48'),(51,6,'admin01','USER_LOGOUT','User logged out successfully.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:19:15'),(52,1,'student01','USER_LOGIN','User logged in with role=student','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:19:22'),(53,1,'student01','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:19:22'),(54,1,'student01','ACCESS_DENIED','Unauthorized role=\'student\' attempted to access admin_dashboard.php','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SECURITY_BLOCK','2026-10-27 12:19:51'),(55,1,'student01','USER_LOGOUT','User logged out successfully.','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:20:11'),(56,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','SUCCESS','2026-10-27 12:20:51'),(57,2,'fenilfinava','USER_LOGIN','User logged in with role=student','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 12:30:52'),(58,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 12:30:52'),(59,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 12:31:01'),(60,2,'fenilfinava','ACCESS_DENIED','Unauthorized role=\'student\' attempted to access admin_dashboard.php','127.0.0.1','curl/8.7.1','SECURITY_BLOCK','2026-10-27 12:31:01'),(61,6,'admin01','USER_LOGIN','User logged in with role=admin','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 12:31:02'),(62,2,'fenilfinava','VIEW_DASHBOARD','Student viewed academic dashboard.','127.0.0.1','curl/8.7.1','SUCCESS','2026-10-27 12:31:07');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `location` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Odoo Hackathon 2026','2026-08-22','LDCE Ahmedabad',NULL,'Hackathon','uploads/odoo_hackathon.jpg','2026-10-19 05:50:00'),(2,'TechFest Spoural 2026','2026-09-10','CSPIT A7 Building',NULL,'Workshop','uploads/spoural_fest.jpg','2026-10-19 05:50:00'),(3,'AI & Cloud Computing Seminar','2026-09-15','Auditorium CHARUSAT',NULL,'Conference','uploads/ai_cloud.jpg','2026-10-19 05:50:00'),(4,'Robotics & Automation Exhibition','2026-11-10','CSPIT Foyer',NULL,'Workshop','uploads/robotics_expo.jpg','2026-10-19 05:50:00'),(5,'Full-Stack Web Dev Bootcamp','2026-10-07','CSPIT Lab 3',NULL,'Workshop',NULL,'2026-10-19 05:50:00');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_role` enum('user','editor','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'bi-link',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,'Student Dashboard','student_dashboard.php','user','bi-speedometer2'),(2,'My Profile & Contact','student_dashboard.php','user','bi-person-badge'),(3,'Campus Events','student_dashboard.php#events','user','bi-calendar-event'),(4,'Faculty Console','faculty.php','editor','bi-mortarboard'),(5,'Attendance System','student_dashboard.php#attendance','editor','bi-check2-square'),(6,'Admin Management Console','admin_dashboard.php','admin','bi-shield-lock'),(7,'Student Management (CRUD)','admin_dashboard.php?tab=students','admin','bi-people'),(8,'Campus Event Manager','admin_dashboard.php?tab=events','admin','bi-calendar-plus'),(9,'Asynchronous SPA Manager','../Practical-13/index.html','admin','bi-lightning-charge'),(10,'Security Audit Logs','admin_dashboard.php?tab=audits','admin','bi-journal-text');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `enrollment_no` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` int DEFAULT '3',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'25CS011','Fenil Finava','25cs011@charusat.edu.in','Computer Science','Male',3,'Active','2026-10-14 16:39:26'),(2,'25CE045','Diya Shah','diya@charusat.edu.in','Web Development','Female',5,'Active','2026-10-14 16:39:26'),(3,'25IT012','Meet Desai','meet@charusat.edu.in','Data Science','Male',3,'Active','2026-10-14 16:39:26'),(4,'25CS089','Kavya Mehta','kavya@charusat.edu.in','Web Development','Female',7,'Active','2026-10-14 16:39:26'),(5,'25CS104','Rohan Patel','rohan@charusat.edu.in','Cyber Security','Male',3,'Active','2026-10-14 17:29:40'),(6,'25CE078','Pooja Sharma','pooja@charusat.edu.in','AI & Machine Learning','Female',5,'Active','2026-10-26 09:36:35'),(7,'25IT056','John Doe','john@example.com','Computer Science','Male',3,'Inactive','2026-10-14 16:39:26'),(8,'25CS744','charusat','charusat123@gmail.com','Information Technology','Female',3,'Active','2026-10-27 12:11:45'),(9,'26IT520','Priyank Patel','priyank2026@gmail.com','Information Technology','Male',3,'Active','2026-10-27 12:17:01');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `remember_token_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'student01','student01@gmail.com','$2y$12$iNOfqPbbyjHWTuxcAKgALuhlhLYQadPvLwvKIuktRsmXvK7k94Km2','student','2026-10-14 07:53:35','2026-10-27 17:49:22',NULL,NULL),(2,'fenilfinava','25cs011@charusat.edu.in','$2y$12$CJUG..y1.ptMkkDbWYuw5ON3GjIjP8ddvTrtHUUJ21xn9SH4x233q','student','2026-10-14 08:06:26','2026-10-27 18:00:52',NULL,NULL),(3,'diya_shah','diya@charusat.edu.in','$2y$12$to8lSIHQJ7c49VodUsO6WeBe/j6nDsMaT/ia8EgzeSr31fuo5bNZu','student','2026-10-14 08:16:09',NULL,NULL,NULL),(4,'meet_desai','meet@charusat.edu.in','$2y$12$Ll8MUQZ1ggfOFdbQxCB6yets/w/lY6BrQUAHfcG0XNoifgjYQZlZy','student','2026-10-14 08:16:58',NULL,NULL,NULL),(5,'prof_patel','prof.patel@charusat.edu.in','$2y$12$9bBdgV5MdYQdKX.PYIIKyeqM05.kDW7JN6QbZlC8XCI7ak1kr7J8G','editor','2026-10-15 04:30:00',NULL,NULL,NULL),(6,'admin01','admin01@gmail.com','$2y$12$9bBdgV5MdYQdKX.PYIIKyeqM05.kDW7JN6QbZlC8XCI7ak1kr7J8G','admin','2026-10-14 08:22:50','2026-10-27 18:01:02',NULL,NULL),(7,'charusat123','charusat123@gmail.com','$2y$12$ewuKOe/.goDTZqt0N6q5I.S1kmicezLogs4W6.jQ5d8niCiH9vmLK','student','2026-10-27 12:11:45','2026-10-27 17:46:50',NULL,NULL),(8,'priyank2026','priyank2026@gmail.com','$2y$12$FxFzsVukWQZcRPkUsPNSKug5YLNqyX26RBwDM5hn.poalGAoVtkku','student','2026-10-27 12:17:01',NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-27 18:05:54
