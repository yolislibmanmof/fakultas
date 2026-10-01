-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 21, 2026 at 02:44 PM
-- Server version: 5.7.39
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fakultas`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendar`
--

CREATE TABLE `academic_calendar` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'akademik',
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `academic_calendar`
--

INSERT INTO `academic_calendar` (`id`, `event_name`, `start_date`, `end_date`, `category`, `description`, `is_active`, `created_at`) VALUES
(1, 'Pendaftaran Mahasiswa Baru (Gelombang I)', '2026-06-01', '2026-07-15', 'pmb', 'Pendaftaran mahasiswa baru gelombang 1', 1, '2026-08-26 16:00:56'),
(2, 'Daftar Ulang & OMB', '2026-07-16', '2026-08-21', 'pmb', 'Orientasi Mahasiswa Baru', 1, '2026-08-26 16:00:56'),
(3, 'Kuliah Perdana Ganjil', '2026-08-24', '2026-08-24', 'akademik', 'Awal perkuliahan semester ganjil', 1, '2026-08-26 16:00:56'),
(4, 'UTS Ganjil', '2026-10-12', '2026-10-24', 'ujian', 'Ujian Tengah Semester', 1, '2026-08-26 16:00:56'),
(5, 'UAS Ganjil', '2027-01-04', '2027-01-16', 'ujian', 'Ujian Akhir Semester', 1, '2026-08-26 16:00:56'),
(6, 'Libur Antar Semester', '2027-01-25', '2027-02-05', 'libur', 'Libur semester', 1, '2026-08-26 16:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `student_name` varchar(150) NOT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `study_program_id` int(11) DEFAULT NULL,
  `achievement_title` varchar(255) NOT NULL,
  `level` enum('faculty','university','regional','national','international') DEFAULT 'national',
  `year` year(4) NOT NULL,
  `organizer` varchar(150) DEFAULT NULL,
  `document_proof` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `student_name`, `nim`, `study_program_id`, `achievement_title`, `level`, `year`, `organizer`, `document_proof`, `created_at`) VALUES
(1, 'Yolis Libman', '2022030357', 2, 'Juara 1 Lomba Mabuk', 'international', '2026', '', '627732378d2c0f7d40b92b73baed8de8.pdf', '2026-08-29 11:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `nim` varchar(50) DEFAULT NULL,
  `study_program_id` int(11) DEFAULT NULL,
  `graduation_year` year(4) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `current_position` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Indonesia',
  `linkedin_url` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `bio` text,
  `achievements` text,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`id`, `full_name`, `nim`, `study_program_id`, `graduation_year`, `email`, `phone`, `password_hash`, `photo`, `current_position`, `company`, `industry`, `city`, `country`, `linkedin_url`, `website_url`, `bio`, `achievements`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', '2018110001', 1, '2022', 'budi.santoso@example.com', '+62 812-3456-7890', NULL, 'alumni/1ee6f4b944dd268886752caa45588f86.jpg', 'Senior Software Engineer', 'Tokopedia', 'Technology', 'Jakarta', 'Indonesia', 'https://linkedin.com/in/budisantoso', '', 'Lulusan Teknik Informatika 2022 yang fokus pada backend engineering dan sistem terdistribusi.', 'Juara 1 Hackathon Nasional 2021\r\nSpeaker di konferensi developer 2023', 'approved', '2026-08-27 08:52:51', '2026-08-27 11:27:32'),
(2, 'Siti Nurhaliza', '2017110045', 1, '2021', 'siti.nurhaliza@example.com', '+62 821-9876-5432', NULL, 'alumni/49da87567046400b5791e0d28a90e12d.jpg', 'Data Scientist', 'Gojek', 'Technology', 'Jakarta', 'Indonesia', 'https://linkedin.com/in/sitinurhaliza', '', 'Data scientist dengan minat pada machine learning untuk produk konsumen.', 'Cumlaude\r\nPublikasi internasional 2022', 'approved', '2026-08-27 08:52:51', '2026-08-27 11:27:42'),
(3, 'Andi Wijaya', '2016120012', 2, '2020', 'andi.wijaya@example.com', '+62 813-5555-7777', NULL, NULL, 'Project Manager', 'Wika', 'Construction', 'Surabaya', 'Indonesia', 'https://linkedin.com/in/andiwijaya', NULL, 'Mengelola proyek infrastruktur digital dan smart building.', '-', 'approved', '2026-08-27 08:52:51', '2026-08-27 08:52:51'),
(4, 'Maria Tanuwijaya', '2019110088', 1, '2023', 'maria.t@example.com', '+62 819-2222-3333', NULL, NULL, 'Founder & CEO', 'EduTech Startup', 'Education', 'Bandung', 'Indonesia', 'https://linkedin.com/in/mariat', NULL, 'Membangun startup edukasi yang berfokus pada pembelajaran adaptif.', 'Mahasiswa Berprestasi Nasional 2022', 'approved', '2026-08-27 08:52:51', '2026-08-27 08:52:51'),
(5, 'Yolis Libman', '2022030357', 4, '2017', 'julianusmitankesik57@gmail.com', '+62 821-4720-1903', '$2y$10$OloeBkYGF.j8g8k2XZcZbeMknzhd6J.KvsXk4AxV/MZxLRVNc8Qau', 'alumni/f1c69ffcc8a4d9aaf523600942ffebbe.jpg', 'Data Scientist', 'Startup Lokal', 'Retail', 'Jakarta', 'Indonesia', '', '', 'Mandiri', 'Mabuk', 'approved', '2026-08-29 11:56:37', '2026-08-29 11:56:37');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `target_table` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `target_table`, `target_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 09:19:38'),
(2, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 11:41:49'),
(3, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 12:31:23'),
(4, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 14:46:33'),
(5, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 14:47:49'),
(6, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 14:47:52'),
(7, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 16:52:58'),
(8, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 16:53:11'),
(9, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 21:29:27'),
(10, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 07:01:11'),
(11, 1, 'update_lecturer', NULL, NULL, 'Edit dosen: Ahmad Fauzi', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 10:19:07'),
(12, 1, 'update_lecturer', NULL, NULL, 'Edit dosen: Budi Santoso', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 10:19:20'),
(13, 1, 'update_lecturer', NULL, NULL, 'Edit dosen: Siti Aminah', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 10:19:30'),
(14, 1, 'create_course', NULL, NULL, 'Tambah MK: Informatika', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 15:55:21'),
(15, 1, 'update_tracer', NULL, NULL, 'Perbarui pengaturan Tracer Study', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 16:33:10'),
(16, 1, 'update_course', NULL, NULL, 'Edit MK: Algoritma & Pemrograman', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 17:34:49'),
(17, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:18:53'),
(18, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:19:48'),
(19, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:24:29'),
(20, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:34:08'),
(21, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 23:34:27'),
(22, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 05:17:36'),
(23, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 05:17:42'),
(24, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 05:33:16'),
(25, 1, 'update_post', NULL, NULL, 'Edit berita: Pembukaan Pendaftaran Mahasiswa Baru 2026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 05:46:30'),
(26, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 07:19:11'),
(27, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 08:46:09'),
(28, 1, 'update_post', NULL, NULL, 'Edit berita: Pembukaan Pendaftaran Mahasiswa Baru 2026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 09:45:42'),
(29, 1, 'update_post', NULL, NULL, 'Edit berita: Pembukaan Pendaftaran Mahasiswa Baru 2026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 09:48:56'),
(30, 1, 'update_post', NULL, NULL, 'Edit berita: Pembukaan Pendaftaran Mahasiswa Baru 2026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 09:49:05'),
(31, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 13:07:38'),
(32, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 13:34:59'),
(33, 1, 'update_event', NULL, NULL, 'Edit agenda: UTS Ganjil', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 14:05:45'),
(34, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 16:47:37'),
(35, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 17:57:58'),
(36, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 18:16:28'),
(37, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 22:01:43'),
(38, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 22:09:02'),
(39, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 22:29:20'),
(40, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-28 22:29:49'),
(41, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 05:40:49'),
(42, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 06:19:00'),
(43, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 06:21:03'),
(44, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 06:51:51'),
(45, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 06:56:03'),
(46, 1, 'create_facility', NULL, NULL, 'Tambah fasilitas: Gedung Perpustakaan', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 07:08:18'),
(47, 1, 'create_facility', NULL, NULL, 'Tambah fasilitas: Laboratorium', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 07:38:28'),
(48, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 08:43:32'),
(49, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 10:46:27'),
(50, 1, 'create_post', NULL, NULL, 'Tulis berita: Penerimaan Mahasiswa Pindahan', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 10:19:58'),
(51, 1, 'bulk_delete_posts', NULL, NULL, 'Bulk delete: 1 berita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 10:24:46'),
(52, 1, 'bulk_delete_posts', NULL, NULL, 'Bulk delete: 2 berita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 10:24:59'),
(53, 1, 'create_post', NULL, NULL, 'Tulis berita: PMB 2027', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:02:20'),
(54, 1, 'create_blog', NULL, NULL, 'Tulis blog: CODING', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:13:38'),
(55, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:38:46'),
(56, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:45:18'),
(57, 1, 'create_research', NULL, NULL, 'Tambah riset: SABAR', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:46:23'),
(58, 1, 'create_program', NULL, NULL, 'Tambah prodi: Administrasi Kesehatan', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:48:05'),
(59, 1, 'create_course', NULL, NULL, 'Tambah MK: Uchiha Sasuke', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:54:09'),
(60, 1, 'create_lecturer', NULL, NULL, 'Tambah dosen: Yolis Libman', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:55:26'),
(61, 1, 'create_achievement', NULL, NULL, 'Tambah prestasi: Juara 1 Lomba Mabuk', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:57:33'),
(62, 1, 'create_portal', NULL, NULL, 'Tambah portal: MABUK', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 11:59:22'),
(63, 1, 'update_achievement', NULL, NULL, 'Edit prestasi: Juara 1 Lomba Mabuk', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 12:08:03'),
(64, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 12:33:06'),
(65, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 15:52:24'),
(66, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 17:27:15'),
(67, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 17:39:55'),
(68, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 18:05:39'),
(69, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 18:34:12'),
(70, 1, 'update_post', NULL, NULL, 'Edit berita: PMB 2027', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 18:35:11'),
(71, 1, 'logout', NULL, NULL, 'Logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 20:35:06'),
(72, 1, 'login', NULL, NULL, 'Berhasil login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-29 20:35:13');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`) VALUES
(1, 'Berita Akademik', 'berita-akademik', 'Berita seputar kegiatan akademik', NULL, '2026-08-26 08:43:42'),
(2, 'Pengumuman', 'pengumuman', 'Pengumuman resmi fakultas', NULL, '2026-08-26 08:43:42'),
(3, 'Agenda', 'agenda', 'Jadwal kegiatan fakultas', NULL, '2026-08-26 08:43:42'),
(4, 'Prestasi', 'prestasi', 'Prestasi mahasiswa dan dosen', NULL, '2026-08-26 08:43:42'),
(5, 'Riset', 'riset', 'Kegiatan penelitian dan pengabdian', NULL, '2026-08-26 08:43:42');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `sks` tinyint(2) NOT NULL,
  `semester` tinyint(2) NOT NULL,
  `course_type` enum('wajib','pilihan') DEFAULT 'wajib',
  `description` text,
  `syllabus_file` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `name`, `sks`, `semester`, `course_type`, `description`, `syllabus_file`, `is_active`) VALUES
(1, 'IF101', 'Algoritma & Pemrograman', 4, 1, 'wajib', 'Matkuliah Inti', NULL, 1),
(2, 'IF102', 'Matematika Diskrit', 3, 1, 'wajib', NULL, NULL, 1),
(3, 'IF201', 'Struktur Data', 4, 2, 'wajib', NULL, NULL, 1),
(4, 'IF301', 'Basis Data', 3, 3, 'wajib', NULL, NULL, 1),
(5, 'IF401', 'Kecerdasan Buatan', 3, 5, 'pilihan', NULL, NULL, 1),
(6, 'IF230', 'Informatika', 3, 1, 'wajib', 'Mata kuliah wajib', '506636957a537c13f3fbe761301ed99d.pdf', 1),
(7, 'MK101', 'Uchiha Sasuke', 3, 1, 'wajib', 'GOOD', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_study_program`
--

CREATE TABLE `course_study_program` (
  `course_id` int(11) NOT NULL,
  `study_program_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `course_study_program`
--

INSERT INTO `course_study_program` (`course_id`, `study_program_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 4);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'umum',
  `title` varchar(255) NOT NULL,
  `description` text,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `elearning_links`
--

CREATE TABLE `elearning_links` (
  `id` int(11) NOT NULL,
  `platform_name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT 'fa-laptop',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `elearning_links`
--

INSERT INTO `elearning_links` (`id`, `platform_name`, `url`, `description`, `icon`, `is_active`, `created_at`) VALUES
(1, 'Moodle Fakultas', 'https://moodle.fakultas.ac.id', 'Platform e-learning utama', 'fa-graduation-cap', 1, '2026-08-26 16:00:56'),
(2, 'Google Classroom', 'https://classroom.google.com', 'Kelas virtual', 'fa-chalkboard', 1, '2026-08-26 16:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` enum('classroom','laboratory','library','mosque','sport','other') NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `description` text,
  `capacity` int(11) DEFAULT NULL,
  `images` json DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `name`, `type`, `location`, `description`, `capacity`, `images`, `is_active`) VALUES
(1, 'Gedung Perpustakaan', '', 'Gedung B Lt. 2', 'Gedung Perpustakaan adalah fasilitas pendukung dengan kapasitas 120 orang untuk menunjang kegiatan akademik dan non-akademik.', 120, '[\"facilities/e14a79c860e5925551c56d03979d1749.jpeg\"]', 1),
(2, 'Laboratorium', 'laboratory', 'Gedung A Lt. 2', 'Laboratorium adalah laboratorium modern dengan kapasitas 50 orang yang dilengkapi dengan peralatan terkini untuk mendukung kegiatan praktikum dan riset mahasiswa.', 50, '[\"facilities/740bffb28e7d06571a22cd6404476442.jpg\"]', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `title_front` varchar(50) DEFAULT NULL,
  `title_back` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `expertise` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `google_scholar_url` varchar(255) DEFAULT NULL,
  `sinta_url` varchar(255) DEFAULT NULL,
  `scopus_url` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `nidn`, `name`, `title_front`, `title_back`, `email`, `phone`, `expertise`, `education`, `google_scholar_url`, `sinta_url`, `scopus_url`, `photo`, `is_active`, `created_at`) VALUES
(1, '0101018501', 'Budi Santoso', 'Dr.', 'M.Kom.', 'budi@fakultas.ac.id', '', 'Artificial Intelligence, Machine Learning', 'S3 Ilmu Komputer', '', '', NULL, '9f02e4712eb703e0d0361dfb70364f1f.jpg', 1, '2026-08-26 08:43:42'),
(2, '0202029002', 'Siti Aminah', 'Dr.', 'M.Si.', 'siti@fakultas.ac.id', '', 'Sistem Informasi, Business Process', 'S3 Sistem Informasi', '', '', NULL, 'fe5bf716299fb2436301f863c6e6aec4.jpg', 1, '2026-08-26 08:43:42'),
(3, '0303038803', 'Ahmad Fauzi', 'Ahmad', 'M.T.', 'ahmad@fakultas.ac.id', '', 'Jaringan Komputer, IoT', 'S2 Teknik Komputer', '', '', NULL, '7b3bdaf4adf38f3d967b847b11ef2162.jpg', 1, '2026-08-26 08:43:42'),
(4, '1111122222', 'Yolis Libman', '', ', S.Kom.', 'libmanozhez57@gmail.com', '082147201903', 'Internet of Things', 'S3 Teknik Informatika', '', '', NULL, NULL, 1, '2026-08-29 11:55:26');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_posts`
--

CREATE TABLE `lecturer_posts` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` text NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `views` int(11) DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lecturer_posts`
--

INSERT INTO `lecturer_posts` (`id`, `lecturer_id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `status`, `views`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mengapa Artificial Intelligence Harus Memanusiakan Manusia', 'ai-memanusiakan-manusia', 'Refleksi tentang arah pengembangan AI yang berpusat pada nilai kemanusiaan.', 'Artificial Intelligence berkembang sangat pesat. Namun pertanyaan terbesarnya bukan \"secerdas apa mesin bisa berpikir\", melainkan \"sebijak apa manusia mengarahkannya\".\n\nDalam penelitian kami di laboratorium, kami selalu memulai dari masalah nyata masyarakat: diagnosis kesehatan yang lebih adil, pertanian presisi untuk petani kecil, dan pendidikan adaptif untuk anak negeri.\n\nAI yang baik adalah AI yang memanusiakan manusia — bukan menggantikan peran manusia, melainkan memperkuat kapasitas manusia.', NULL, 'published', 1, '2026-08-27 07:05:24', '2026-08-27 07:05:24', '2026-08-28 22:23:13'),
(2, 1, 'Tips Riset untuk Mahasiswa: Mulai dari Masalah, Bukan dari Tools', 'tips-riset-mahasiswa', 'Banyak mahasiswa terjebak memilih tools dulu. Padahal riset yang baik lahir dari masalah yang jelas.', 'Kesalahan paling umum mahasiswa saat memulai riset adalah langsung memilih tools: \"Saya mau pakai deep learning\", \"Saya mau pakai blockchain\".\n\nMulailah dari masalah. Bicara dengan pengguna. Amati prosesnya. Ukur rasa sakitnya. Baru setelah itu pilih metode yang paling sederhana yang mampu menyelesaikan masalah.\n\nRiset yang baik bukan yang paling canggih, tapi yang paling bermanfaat.', NULL, 'published', 5, '2026-08-27 07:05:24', '2026-08-27 07:05:24', '2026-08-28 22:22:32'),
(3, 3, 'CODING', 'coding', 'CODING', 'CODING', 'blog/24cf20c90c5bedc14c8bea74d52b3a18.png', 'draft', 0, NULL, '2026-08-29 11:13:38', '2026-08-29 11:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_study_program`
--

CREATE TABLE `lecturer_study_program` (
  `lecturer_id` int(11) NOT NULL,
  `study_program_id` int(11) NOT NULL,
  `status` enum('tetap','tidak_tetap') DEFAULT 'tetap'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lecturer_study_program`
--

INSERT INTO `lecturer_study_program` (`lecturer_id`, `study_program_id`, `status`) VALUES
(1, 1, 'tetap'),
(2, 2, 'tetap'),
(3, 1, 'tetap'),
(4, 3, 'tetap'),
(4, 4, 'tetap');

-- --------------------------------------------------------

--
-- Table structure for table `pmb_applications`
--

CREATE TABLE `pmb_applications` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_program_id` int(11) DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('new','reviewed','accepted','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portal_content`
--

CREATE TABLE `portal_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `link_url` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fa-link',
  `color` varchar(50) DEFAULT 'blue',
  `category` varchar(50) DEFAULT 'umum',
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `portal_content`
--

INSERT INTO `portal_content` (`id`, `title`, `description`, `link_url`, `icon`, `color`, `category`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'KRS Online', 'Kartu Rencana Studi', '#', 'fa-edit', 'blue', 'akademik', 1, 0, '2026-08-26 16:00:56'),
(2, 'KHS Online', 'Kartu Hasil Studi', '#', 'fa-file-alt', 'green', 'akademik', 1, 0, '2026-08-26 16:00:56'),
(3, 'Jadwal Kuliah', 'Jadwal perkuliahan semester ini', '#', 'fa-calendar-alt', 'purple', 'akademik', 1, 0, '2026-08-26 16:00:56'),
(4, 'Nilai Transkrip', 'Lihat transkrip nilai', '#', 'fa-star', 'yellow', 'akademik', 1, 0, '2026-08-26 16:00:56'),
(5, 'Pembayaran UKT', 'Info pembayaran kuliah', '#', 'fa-money-bill', 'red', 'keuangan', 1, 0, '2026-08-26 16:00:56'),
(6, 'Beasiswa', 'Info dan pendaftaran beasiswa', '#', 'fa-award', 'orange', 'keuangan', 1, 0, '2026-08-26 16:00:56'),
(7, 'MABUK', 'BOLEH', 'https://unimof.ac.id/id', 'fa-user', 'navy', 'umum', 1, 2, '2026-08-29 11:59:22');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text,
  `type` enum('news','announcement','agenda') DEFAULT 'news',
  `featured_image` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `category_id`, `user_id`, `title`, `slug`, `content`, `excerpt`, `type`, `featured_image`, `views`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(4, 2, 1, 'PMB 2027', 'pmb-2027', 'Telah dibuka penerimaan mahasiswa baru, daftar sekarang ! Daftarkan dirimu sekarang juga', 'PMB 2027 — Informasi resmi dari Fakultas Teknik Ilmu Komputer. Simak detail lengkap, tanggal penting, dan panduan terkait pada pengumuman ini.', 'announcement', 'posts/b56a25741455bc577d9d70eb7eb5ff4d.png', 5, 'published', '2026-08-29 11:02:20', '2026-08-29 11:02:20', '2026-08-29 18:35:11');

-- --------------------------------------------------------

--
-- Table structure for table `post_attachments`
--

CREATE TABLE `post_attachments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `research`
--

CREATE TABLE `research` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('research','community_service') NOT NULL,
  `year` year(4) NOT NULL,
  `funding_source` varchar(100) DEFAULT NULL,
  `amount` bigint(20) DEFAULT NULL,
  `abstract` text,
  `document_file` varchar(255) DEFAULT NULL,
  `status` enum('proposed','ongoing','completed') DEFAULT 'ongoing',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `research`
--

INSERT INTO `research` (`id`, `lecturer_id`, `title`, `type`, `year`, `funding_source`, `amount`, `abstract`, `document_file`, `status`, `created_at`) VALUES
(1, 3, 'SABAR', 'research', '2026', 'Mandiri', 141, 'Sabar adalah Anugerah terindah', '1814ad95df7a93d5957efc2c5abe6e57.pdf', 'ongoing', '2026-08-29 11:46:23');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('text','number','image','json') DEFAULT 'text',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `updated_at`) VALUES
(1, 'site_name', 'Fakultas Teknik Ilmu Komputer', 'text', '2026-08-29 10:47:37'),
(2, 'site_tagline', 'Unggul dalam Ilmu dan Teknologi', 'text', '2026-08-26 08:43:42'),
(3, 'site_email', 'info@fakultas.ac.id', 'text', '2026-08-26 08:43:42'),
(4, 'site_phone', '(021) 1234-5678', 'text', '2026-08-26 08:43:42'),
(5, 'site_address', 'Jl. Pendidikan No. 123, Jakarta', 'text', '2026-08-26 08:43:42'),
(6, 'dean_name', 'Prof. Dr. H. Nama Dekan, M.Sc.', 'text', '2026-08-26 08:43:42'),
(7, 'faculty_history', 'Fakultas Teknologi & Ilmu Komputer  didirikan pada tahun 1995 sebagai bagian dari pengembangan universitas menuju pusat permabukan yang abadi.\r\n\r\nDimulai dengan satu program studi, kini fakultas telah berkembang menjadi tiga program studi terakreditasi dengan puluhan dosen berkualifikasi doktor dan magister, serta laboratorium modern yang mendukung pembelajaran berbasis riset.', 'text', '2026-08-28 10:41:50'),
(8, 'faculty_vision', 'Menjadi fakultas yang unggul, inovatif, dan berdaya saing global di bidang Teknologi dan Informasi pada tahun 2030.', 'text', '2026-08-27 23:15:24'),
(9, 'faculty_mission', '1. Menyelenggarakan pendidikan berkualitas yang berorientasi pada kebutuhan industri.\r\n2. Mengembangkan penelitian inovatif yang memberikan kontribusi nyata bagi masyarakat.\r\n3. Melaksanakan pengabdian kepada masyarakat berbasis hasil penelitian.\r\n4. Membangun kerjasama nasional dan internasional di bidang tridharma perguruan tinggi.', 'text', '2026-08-26 10:49:54'),
(10, 'dean_message', 'Selamat datang di website resmi Fakultas Teknologi & Ilmu Komputer\r\n\r\nKami berkomitmen mencetak lulusan yang tidak hanya unggul secara akademik, tetapi juga berkarakter dan siap menghadapi tantangan revolusi industri 5.0. Melalui kurikulum yang adaptif, dosen yang kompeten, dan fasilitas modern, kami mengajak Anda menjadi bagian dari keluarga besar fakultas.\r\n\r\nMari berinovasi bersama untuk Indonesia yang lebih baik!', 'text', '2026-08-27 23:15:24'),
(11, 'wadek_1_name', 'Dr. Rudi Hartono, M.T.', 'text', '2026-08-26 10:43:47'),
(15, 'font_family', 'elegant', 'text', '2026-08-27 09:58:33'),
(16, 'font_scale', 'md', 'text', '2026-08-27 10:01:39'),
(17, 'hero_animation', 'waves', 'text', '2026-08-28 22:29:45'),
(18, 'hero_particles', '1', 'text', '2026-08-26 19:17:06'),
(19, 'hero_label', 'Faculty of Engineering and Computer Science', 'text', '2026-08-29 17:40:37'),
(20, 'hero_title', 'Menyalakan Terang *Pengetahuan*, Menghadirkan *Harapan*', 'text', '2026-08-27 20:44:38'),
(21, 'hero_subtitle', 'Faculty of Engineering and Computer Science', 'text', '2026-08-29 17:40:37'),
(22, 'welcome_text', 'WELCOME TO Fakultas Teknologi & Ilmu Komputer', 'text', '2026-08-27 20:44:38'),
(23, 'hero_photo', 'hero/da3062d7645fb07be62b2fd2ca292e14.png', 'text', '2026-08-27 07:02:20'),
(33, 'video_tour_enabled', '1', 'text', '2026-08-26 20:28:39'),
(34, 'video_tour_type', 'youtube', 'text', '2026-08-26 20:28:39'),
(35, 'video_tour_youtube', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'text', '2026-08-26 20:28:39'),
(36, 'video_tour_file', '', 'text', '2026-08-26 20:28:39'),
(37, 'video_tour_title', 'Explore Our Campus', 'text', '2026-08-26 20:28:39'),
(38, 'video_tour_subtitle', 'Ambil virtual tour dan rasakan atmosfer akademik kelas dunia kami dari dekat.', 'text', '2026-08-26 20:28:39'),
(39, 'video_tour_poster', '', 'text', '2026-08-26 20:28:39'),
(40, 'ga_measurement_id', '', 'text', '2026-08-26 20:28:39'),
(41, 'ga_dashboard_url', '', 'text', '2026-08-26 20:28:39'),
(42, 'site_logo', 'brand/0f17828341fb1b3b37fc6735e2d8807f.png', 'text', '2026-08-27 09:58:34'),
(43, 'site_favicon', 'brand/20f0700c8a4eb985b4fd8e1d2160bc1c.png', 'text', '2026-08-27 09:58:34'),
(45, 'dean_photo', 'profile/dean/9d94186b2971a18af84c46412192fd0c.jpg', 'text', '2026-08-27 10:17:27'),
(46, 'wadek_1_photo', 'profile/wadek_1/3e5d7354a8d24809b32c5d7590a00f8b.jpg', 'text', '2026-08-27 10:17:27'),
(50, 'facebook_url', 'https://www.facebook.com/?ref=homescreenpwa', 'text', '2026-08-27 11:21:14'),
(51, 'instagram_url', 'https://www.instagram.com/rak_kat_mof/?__pwa=1', 'text', '2026-08-27 11:21:14'),
(52, 'linkedin_url', 'https://www.linkedin.com/', 'text', '2026-08-27 11:21:14'),
(53, 'youtube_url', 'https://www.youtube.com/@LIBMAN5790', 'text', '2026-08-27 11:21:14'),
(54, 'twitter_url', 'https://x.com/', 'text', '2026-08-27 11:21:14'),
(55, 'tiktok_url', 'https://www.tiktok.com/foryou', 'text', '2026-08-27 11:21:14'),
(56, 'contact_phone', '(021) 1234-5678', 'text', '2026-08-27 10:29:56'),
(57, 'contact_email', 'info@fakultas.ac.id', 'text', '2026-08-27 10:29:56'),
(58, 'contact_address', 'Jl. Pendidikan No. 123, Maumere-Flores, 12345, Indonesia', 'text', '2026-08-27 11:21:14'),
(59, 'contact_map_embed', '', 'text', '2026-08-27 10:29:56'),
(60, 'footer_tagline', 'Berpikir Kritis, Berkarya Kreatif', 'text', '2026-08-27 20:44:38'),
(61, 'footer_copyright', 'Fakultas Teknik Ilmu Komputer', 'text', '2026-08-29 10:47:37'),
(62, 'hero_alignment', 'left', 'text', '2026-08-29 06:22:03'),
(63, 'section_alignment', 'left', 'text', '2026-08-29 06:22:03'),
(64, 'content_alignment', 'left', 'text', '2026-08-29 06:22:03'),
(65, 'tracer_title', 'Tracer Study Alumni', 'text', '2026-08-27 16:33:10'),
(66, 'tracer_description', 'Mari para Alumni, masukan datamu agar menjadi bagian dari Fakultas tercinta kalian ! ', 'text', '2026-08-27 16:33:10'),
(67, 'tracer_form_url', '', 'text', '2026-08-27 16:33:10'),
(68, 'tracer_is_active', '1', 'text', '2026-08-27 16:33:10'),
(69, 'wadek_label', 'Wakil Dekan', 'text', '2026-08-27 22:44:48'),
(70, 'org_layout', 'tiered', 'text', '2026-08-27 22:44:48'),
(71, 'wadek_2_name', '', 'text', '2026-08-28 16:18:31'),
(72, 'wadek_3_name', '', 'text', '2026-08-28 16:18:31'),
(73, 'tu_head_name', '', 'text', '2026-08-28 16:18:31'),
(74, 'faculty_milestones', '', 'text', '2026-08-28 16:18:31'),
(75, 'core_values', '', 'text', '2026-08-28 16:18:31'),
(76, 'dean_priorities', '', 'text', '2026-08-28 16:18:31'),
(77, 'site_name_en', 'Faculty of Engineering and Computer Science', 'text', '2026-08-29 17:40:37'),
(78, 'last_auto_backup', '2026-08-29 19:29:31', 'text', '2026-08-29 19:29:31');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `nip` varchar(25) NOT NULL,
  `name` varchar(150) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `nip`, `name`, `position`, `unit`, `email`, `phone`, `photo`, `is_active`) VALUES
(1, '198501012010011001', 'Dewi Lestari', 'Kepala Tata Usaha', 'Administrasi', 'dewi@fakultas.ac.id', NULL, NULL, 1),
(2, '199002022015012002', 'Rina Wati', 'Staf Akademik', 'Akademik', 'rina@fakultas.ac.id', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `study_programs`
--

CREATE TABLE `study_programs` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `degree` enum('D3','S1','S2','S3','Sp1','Sp2') NOT NULL,
  `accreditation` varchar(20) DEFAULT NULL,
  `accreditation_until` date DEFAULT NULL,
  `head_of_study_program` varchar(100) DEFAULT NULL,
  `head_photo` varchar(255) DEFAULT NULL,
  `description` text,
  `vision` text,
  `mission` text,
  `logo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `study_programs`
--

INSERT INTO `study_programs` (`id`, `name`, `slug`, `degree`, `accreditation`, `accreditation_until`, `head_of_study_program`, `head_photo`, `description`, `vision`, `mission`, `logo`, `is_active`, `created_at`) VALUES
(1, 'Teknik Informatika', 'teknik-informatika', 'S1', 'Unggul', NULL, 'Dr. Budi Santoso, M.Kom.', 'programs/042fc66e105808ab92c4516f28201fa8.jpg', 'Program studi yang mempelajari ilmu komputer dan pemrograman', NULL, NULL, NULL, 1, '2026-08-26 08:43:42'),
(2, 'Sistem Informasi', 'sistem-informasi', 'S1', 'Baik Sekali', NULL, 'Dr. Siti Aminah, M.Si.', 'programs/f8fc159195bedc3c970d04dda591ba9c.jpg', 'Program studi yang mempelajari integrasi teknologi dan bisnis', NULL, NULL, NULL, 1, '2026-08-26 08:43:42'),
(3, 'Teknik Komputer', 'teknik-komputer', 'D3', 'Baik', NULL, 'Ahmad Fauzi, M.T.', 'programs/11a978c39b94e73ab9036e73fcb20a4b.jpg', 'Program vokasi bidang teknik komputer', NULL, NULL, NULL, 1, '2026-08-26 08:43:42'),
(4, 'Administrasi Kesehatan', 'administrasi-kesehatan', 'S1', 'Baik', '2029-08-04', 'Yolis Libman', 'programs/603653567ba7df96555c1855a0c81549.jpg', 'Program Studi Administrasi Kesehatan', 'Menyala', 'Menyala Bos Ku', NULL, 1, '2026-08-29 11:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `tracer_settings`
--

CREATE TABLE `tracer_settings` (
  `id` int(11) NOT NULL,
  `form_url` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT 'Yuk Isi Tracer Study!',
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tracer_settings`
--

INSERT INTO `tracer_settings` (`id`, `form_url`, `title`, `description`, `is_active`, `updated_at`) VALUES
(1, 'https://forms.gle/example', 'Alumni? Yuk Isi Tracer Study!', 'Bantu fakultas meningkatkan kualitas lulusan dengan mengisi formulir tracer study. Data Anda akan dijaga kerahasiaannya.', 1, '2026-08-26 16:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','editor') DEFAULT 'editor',
  `is_active` tinyint(1) DEFAULT '1',
  `login_attempts` int(11) DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_active`, `login_attempts`, `locked_until`, `last_login`, `created_at`) VALUES
(1, 'admin', 'admin@fakultas.ac.id', '$2y$10$8r59UOBD768B08yGMuICJOFKcDPfAflBwvY4uNDjjmypdPuZ9ye5O', 'Super Administrator', 'super_admin', 1, 0, NULL, '2026-08-29 20:35:13', '2026-08-26 08:43:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `study_program_id` (`study_program_id`),
  ADD KEY `idx_level_year` (`level`,`year`);

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `status` (`status`),
  ADD KEY `graduation_year` (`graduation_year`),
  ADD KEY `study_program_id` (`study_program_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_action` (`user_id`,`action`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `course_study_program`
--
ALTER TABLE `course_study_program`
  ADD PRIMARY KEY (`course_id`,`study_program_id`),
  ADD KEY `study_program_id` (`study_program_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `elearning_links`
--
ALTER TABLE `elearning_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nidn` (`nidn`),
  ADD KEY `idx_nidn` (`nidn`);

--
-- Indexes for table `lecturer_posts`
--
ALTER TABLE `lecturer_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `lecturer_study_program`
--
ALTER TABLE `lecturer_study_program`
  ADD PRIMARY KEY (`lecturer_id`,`study_program_id`),
  ADD KEY `study_program_id` (`study_program_id`);

--
-- Indexes for table `pmb_applications`
--
ALTER TABLE `pmb_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `portal_content`
--
ALTER TABLE `portal_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_type_status` (`type`,`status`);

--
-- Indexes for table `post_attachments`
--
ALTER TABLE `post_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `research`
--
ALTER TABLE `research`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `idx_year_type` (`year`,`type`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indexes for table `study_programs`
--
ALTER TABLE `study_programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `tracer_settings`
--
ALTER TABLE `tracer_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `alumni`
--
ALTER TABLE `alumni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `elearning_links`
--
ALTER TABLE `elearning_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lecturer_posts`
--
ALTER TABLE `lecturer_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pmb_applications`
--
ALTER TABLE `pmb_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portal_content`
--
ALTER TABLE `portal_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `post_attachments`
--
ALTER TABLE `post_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `research`
--
ALTER TABLE `research`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `study_programs`
--
ALTER TABLE `study_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tracer_settings`
--
ALTER TABLE `tracer_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`study_program_id`) REFERENCES `study_programs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_study_program`
--
ALTER TABLE `course_study_program`
  ADD CONSTRAINT `course_study_program_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_study_program_ibfk_2` FOREIGN KEY (`study_program_id`) REFERENCES `study_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturer_study_program`
--
ALTER TABLE `lecturer_study_program`
  ADD CONSTRAINT `lecturer_study_program_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lecturer_study_program_ibfk_2` FOREIGN KEY (`study_program_id`) REFERENCES `study_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_attachments`
--
ALTER TABLE `post_attachments`
  ADD CONSTRAINT `post_attachments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `research`
--
ALTER TABLE `research`
  ADD CONSTRAINT `research_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
