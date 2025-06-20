-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 08:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tu_choices`
--

-- --------------------------------------------------------

--
-- Table structure for table `distributed_students`
--

CREATE TABLE `distributed_students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL DEFAULT 0,
  `dist_id` int(11) NOT NULL DEFAULT 0,
  `dist_choice_id` int(11) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `distributed_students`
--

INSERT INTO `distributed_students` (`id`, `student_id`, `dist_id`, `dist_choice_id`, `deleted`, `created_at`, `updated_at`) VALUES
(16, 2, 36, 23, 0, '2025-06-08 15:16:10', '2025-06-08 15:16:10'),
(23, 2, 1, 11, 0, '2025-06-16 19:29:41', '2025-06-16 19:29:41'),
(24, 18, 1, 10, 0, '2025-06-16 19:29:41', '2025-06-16 19:29:41'),
(25, 19, 1, 10, 0, '2025-06-16 19:29:41', '2025-06-16 19:29:41');

-- --------------------------------------------------------

--
-- Table structure for table `distributions`
--

CREATE TABLE `distributions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `ident` varchar(50) NOT NULL,
  `semester_applicable` int(11) NOT NULL,
  `major` varchar(10) NOT NULL DEFAULT '0',
  `faculty` varchar(10) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL COMMENT '1=Избираема дисциплина, 2=Дипломен ръководител',
  `active` smallint(6) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `distributions`
--

INSERT INTO `distributions` (`id`, `name`, `ident`, `semester_applicable`, `major`, `faculty`, `type`, `active`, `deleted`, `created_at`, `updated_at`) VALUES
(1, 'Дипломни ръководители(ФПМИ)', 'fpmi-diplom', 8, '0', 'fpmi', 2, 0, 0, '2025-05-04 22:52:26', '2025-06-16 19:29:41'),
(14, 'test disciplinbbbbbbb', 'td123', 5, 'isigg', 'fa', 1, 1, 0, '2025-05-05 21:38:58', '2025-05-23 10:04:20'),
(17, 'ПМИ избираема 2', 'pmi-i2', 2, 'pmi', 'fpmi', 1, 1, 0, '2025-05-05 21:50:34', '2025-06-06 23:02:26'),
(18, 'Дипломни ръководители(ИСГГ)', 'isigg-diplom', 8, '0', 'fa', 2, 0, 0, '2025-05-08 10:28:59', '2025-05-15 11:21:58'),
(22, 'test dist new 2323', 'tdn2323', 4, 'tm22', 'ef', 1, 0, 0, '2025-05-15 11:21:07', '2025-05-15 11:40:07'),
(23, 'test diplom new 33', 'tdnef44', 8, '0', 'ef', 2, 0, 0, '2025-05-15 11:29:43', '2025-05-16 17:16:39'),
(33, 'Избираема дисциплина 6(ИСН)', 'isn-i6', 6, 'isn', 'fpmi', 0, 1, 0, '2025-05-16 17:22:10', '2025-05-16 22:39:38'),
(34, 'Избираема дисциплина 3(ИСН)', 'isn-i3', 3, 'isn', 'fpmi', 0, 1, 0, '2025-05-16 17:22:35', '2025-05-16 17:33:36'),
(35, 'Дипломни ръководители(ФА)', 'fa-diplom', 8, '0', 'fa', 2, 0, 0, '2025-05-29 23:55:03', '2025-05-29 23:55:03'),
(36, 'ИСН Избираема дисциплина 8', 'isn-i8', 8, 'isn', 'fpmi', 1, 1, 0, '2025-05-30 20:12:39', '2025-05-30 20:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `distribution_choices`
--

CREATE TABLE `distribution_choices` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `distribution` int(11) NOT NULL,
  `instructor` int(11) NOT NULL,
  `description` text NOT NULL,
  `min` int(11) NOT NULL DEFAULT 0,
  `max` int(11) NOT NULL DEFAULT 10000,
  `min_max_editble` smallint(6) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL COMMENT '1=Избираема дисциплина, 2=Дипломен ръководител',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `distribution_choices`
--

INSERT INTO `distribution_choices` (`id`, `name`, `distribution`, `instructor`, `description`, `min`, `max`, `min_max_editble`, `type`, `deleted`, `created_at`, `updated_at`) VALUES
(10, 'Избери Гого Гогов', 1, 3, 'Добра дисциплина', 1, 5, 0, 2, 0, '2025-05-23 19:50:00', '2025-06-07 10:24:49'),
(11, 'Избери Учо', 1, 16, 'ЪЧИЧИЧИЧИ', 1, 1, 0, 2, 0, '2025-05-23 19:50:00', '2025-06-07 10:24:59'),
(17, 'Дисциплина 1', 14, 6, '123', 3, 12, 1, 1, 0, '2025-05-23 23:16:28', '2025-05-23 23:36:44'),
(18, 'Дисциплина 2', 14, 13, 'jojo', 1, 5, 1, 1, 0, '2025-05-23 23:16:28', '2025-05-23 23:16:28'),
(19, 'Дисциплина 3', 14, 5, 'popo', 3, 7, 0, 1, 0, '2025-05-23 23:16:28', '2025-05-23 23:16:28'),
(20, 'Дисциплината на Гого', 17, 3, 'ГИГИ', 4, 20, 1, 1, 0, '2025-05-24 09:56:41', '2025-05-24 09:56:41'),
(21, 'Дисциплината на Учо', 17, 16, 'ддадададад', 5, 18, 1, 1, 0, '2025-05-24 09:56:41', '2025-05-24 09:56:41'),
(22, 'Дисциплината на Сисо', 17, 4, 'Сисосососососо', 1, 10, 1, 1, 0, '2025-05-24 09:56:41', '2025-05-24 09:56:45'),
(23, 'Дисциплина 1', 36, 3, 'луда', 2, 5, 1, 1, 0, '2025-05-30 20:13:42', '2025-05-30 20:13:42'),
(24, 'Дисциплина 2', 36, 4, 'кафява', 1, 7, 0, 1, 0, '2025-05-30 20:13:42', '2025-05-30 20:13:42'),
(25, 'Дисциплина 3', 36, 16, 'зелена', 3, 8, 1, 1, 0, '2025-05-30 20:13:42', '2025-05-30 20:13:42');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short` varchar(50) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `name`, `short`, `deleted`, `created_at`, `updated_at`) VALUES
(1, 'Факултет Приложна математика и информатика', 'fpmi', 0, '2025-05-04 22:38:15', '2025-05-04 22:38:15'),
(2, 'Факултет Автоматика', 'fa', 0, '2025-05-04 22:38:32', '2025-05-04 22:38:32'),
(3, 'Електротехнически Факултет', 'ef', 0, '2025-05-04 22:38:55', '2025-05-04 22:38:55'),
(12, 'Fac2', 'f2', 0, '2025-05-05 10:16:00', '2025-05-05 10:16:00'),
(14, 'Fac4', 'f4', 0, '2025-05-05 10:16:00', '2025-05-05 10:16:00'),
(17, 'Fac1', 'f1', 0, '2025-05-29 23:53:42', '2025-05-29 23:53:42'),
(18, 'Fac3', 'f3', 0, '2025-05-29 23:53:42', '2025-05-29 23:53:42');

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short` varchar(50) NOT NULL,
  `faculty` varchar(10) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`id`, `name`, `short`, `faculty`, `deleted`, `created_at`, `updated_at`) VALUES
(1, 'Информатика и софтуерни науки', 'isn', 'fpmi', 0, '2025-05-04 22:47:13', '2025-05-15 10:24:21'),
(2, 'Приложна математика и информатика', 'pmi', 'fpmi', 0, '2025-05-04 22:47:37', '2025-05-15 10:24:10'),
(3, 'Интелигентни системи в индустрията, града и дома', 'isigg', 'fa', 0, '2025-05-04 22:48:23', '2025-05-15 10:24:24'),
(4, 'Анализ на данни', 'ad', 'fpmi', 0, '2025-05-05 10:33:37', '2025-05-15 10:24:08'),
(5, 'Педагогика на обучението по математика, физика и информатика', 'pmfi', 'fpmi', 0, '2025-05-05 10:33:37', '2025-05-15 10:24:05'),
(6, 'Приложна физика и компютърно моделиране', 'pfkm', 'fpmi', 0, '2025-05-05 10:33:37', '2025-05-15 10:24:02'),
(9, 'testinggg 445', 'tgg455', 'f2', 0, '2025-05-15 10:32:01', '2025-05-15 10:32:01'),
(10, 'tmaj22', 'tm22', 'ef', 0, '2025-05-29 23:54:42', '2025-05-29 23:54:42');

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `id` int(11) NOT NULL,
  `student_fn` varchar(100) NOT NULL DEFAULT '0',
  `grade` float NOT NULL DEFAULT 0,
  `semester` int(11) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `student_grades`
--

INSERT INTO `student_grades` (`id`, `student_fn`, `grade`, `semester`, `deleted`, `created_at`, `updated_at`) VALUES
(58, '471221051', 5.51, 8, 0, '2025-05-30 20:07:29', '2025-05-30 20:07:29'),
(59, '471551055', 4.44, 8, 0, '2025-05-30 20:07:49', '2025-05-30 20:07:49'),
(60, '47634534', 3.81, 8, 0, '2025-05-30 20:16:45', '2025-05-30 20:16:45'),
(61, '471221051', 4.55, 7, 0, '2025-06-06 23:50:08', '2025-06-06 23:50:08'),
(62, '471551055', 3.25, 7, 0, '2025-06-06 23:50:27', '2025-06-06 23:50:27'),
(63, '47634534', 5.25, 7, 0, '2025-06-06 23:51:17', '2025-06-06 23:51:17');

-- --------------------------------------------------------

--
-- Table structure for table `s_d_scores`
--

CREATE TABLE `s_d_scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `distribution_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 1,
  `user_start_year` int(11) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `s_d_scores`
--

INSERT INTO `s_d_scores` (`id`, `user_id`, `distribution_id`, `choice_id`, `score`, `user_start_year`, `deleted`, `created_at`, `updated_at`) VALUES
(21, 18, 1, 10, 1, 2021, 0, '2025-05-30 20:11:35', '2025-05-30 20:11:35'),
(22, 18, 1, 11, 2, 2021, 0, '2025-05-30 20:11:35', '2025-05-30 20:11:35'),
(23, 2, 36, 23, 3, 2021, 0, '2025-05-30 20:14:54', '2025-05-30 20:14:54'),
(24, 2, 36, 24, 2, 2021, 0, '2025-05-30 20:14:54', '2025-05-30 20:14:54'),
(25, 2, 36, 25, 1, 2021, 0, '2025-05-30 20:14:54', '2025-05-30 20:14:54'),
(26, 19, 36, 23, 5, 2021, 0, '2025-05-30 20:17:37', '2025-05-30 20:17:37'),
(27, 19, 36, 24, 1, 2021, 0, '2025-05-30 20:17:37', '2025-05-30 20:17:37'),
(28, 19, 36, 25, 4, 2021, 0, '2025-05-30 20:17:37', '2025-05-30 20:17:37'),
(31, 19, 1, 10, 1, 2021, 0, '2025-06-07 00:07:03', '2025-06-07 00:07:03'),
(32, 19, 1, 11, 1, 2021, 0, '2025-06-07 00:07:03', '2025-06-07 00:07:03'),
(33, 2, 1, 10, 3, 2021, 0, '2025-06-07 10:23:27', '2025-06-07 10:23:27'),
(34, 2, 1, 11, 5, 2021, 0, '2025-06-07 10:23:27', '2025-06-07 10:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `names` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL DEFAULT '',
  `role` int(11) NOT NULL DEFAULT 0 COMMENT '1=student,2=teacher,3=admin',
  `fn` varchar(100) NOT NULL DEFAULT '',
  `major` varchar(10) NOT NULL DEFAULT '0',
  `faculty` varchar(10) NOT NULL DEFAULT '0',
  `start_year` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `names`, `email`, `pass`, `role`, `fn`, `major`, `faculty`, `start_year`, `active`, `deleted`, `created_at`, `updated_at`) VALUES
(1, 'admin1', 'Християн Кушев', 'hkushev@tu-soifa.bg', '$2a$12$WKi7lFGlhDRrnW5nwuEVcOv.ao0F4GVwekDz6kjUvKkVyjoxhl/o2', 3, '', '0', '0', NULL, 1, 0, '2025-05-04 22:36:51', '2025-05-08 00:25:19'),
(2, 'hris', 'Християн Кушев', 'hristiyan.kushev@abv.bg', '$2y$10$k2ud9yGXujrGn05TnV//jufoprAm18KCgD2CRWpcaFbJOQ5lEwoYa', 1, '471221051', 'isn', 'fpmi', 2021, 1, 0, '2025-05-04 22:49:20', '2025-05-16 17:10:00'),
(3, 'gogo', 'Гого Гогов', 'gogo@abv.bg', '$2y$10$k2ud9yGXujrGn05TnV//jufoprAm18KCgD2CRWpcaFbJOQ5lEwoYa', 2, '', '0', 'fpmi', NULL, 1, 0, '2025-05-04 22:50:09', '2025-05-15 22:42:27'),
(4, 'siso', 'Сисо Сисов', 'siso@gmail.com', '$2y$10$LiiyWCpxOJCkNaIB/49fr.TnGWEy3k2Q0eSftwaXQk3Su5BER63ju', 2, '', '0', 'fpmi', NULL, 1, 0, '2025-05-04 22:50:46', '2025-05-15 22:42:30'),
(5, 'pipi', 'Пипи Пипов', 'pipi@gmail.com', '$2y$10$H6h3F62sUN8drrxLLCup0OTd/BtOExWvDujJW50imsAwbiAM3JzKK', 2, '', '0', 'fa', NULL, 1, 0, '2025-05-04 22:51:11', '2025-05-15 22:42:43'),
(6, 'lolo', 'Лоло Лолов', 'lolo@abv.bg', '$2y$10$XDa29fS3uO2UrGhnqrwii.AdyXkZbYcycocat9.n2MxLGgmf7hjNe', 2, '', '0', 'fa', NULL, 1, 0, '2025-05-04 22:51:42', '2025-05-15 22:42:45'),
(10, 'goshooooo', 'Гошо Гошов', 'goshko@gmail.com', '', 1, '47890099', 'isigg', 'fa', 2023, 0, 0, '2025-05-08 00:43:28', '2025-05-15 22:42:42'),
(11, 'teach4', 'Опо Опов', 'opaaaaa@gmail.com', '$2y$10$FDDFzkojY683zVsweowVO.Awt1cmme6KrqKNN5DmpNBzIT5w3R1DS', 2, '', '0', 'fa', NULL, 1, 0, '2025-05-08 00:47:07', '2025-05-15 22:42:47'),
(12, 'roro', 'Роро Роров', 'roro@abv.bg', '', 1, '98989898', 'ad', 'fpmi', 2023, 0, 0, '2025-05-08 01:25:35', '2025-05-15 22:42:26'),
(13, 'teach3', 'Джоджо Джоджов', 'jojo@mailaa.bg', '$2y$10$WLN6M2cmmkJRzQ71TjHpt.kAnjKH4G1ltsMDudO806XxcbMmCnm5a', 2, '', '0', 'fa', NULL, 1, 0, '2025-05-08 01:26:17', '2025-05-15 22:42:49'),
(14, 'ivo', 'Иво Ивов', 'ivcho@gmail.com', '$2y$10$GOGIOosAqGSqVSihoBNUUu4mr6A6w.8E1GVaZWDcys0F0jZRwXgfq', 2, '', '0', 'fa', NULL, 1, 0, '2025-05-08 10:32:15', '2025-05-15 22:42:51'),
(15, 'pesho432', 'Петър Петров', 'peshuuu@abv.bg', '', 1, '561923485', 'isigg', 'fa', 2021, 0, 0, '2025-05-15 23:16:57', '2025-05-15 23:27:54'),
(16, 'ucho3', 'Учо Учителя', 'ucho@gmail.com', '$2y$10$6rA4oLrZw7XS/0BK3pf0dez5jDcA5X5C1EfdNbWObyDcInIAhdNUC', 2, '', '0', 'fpmi', NULL, 1, 0, '2025-05-15 23:18:43', '2025-05-15 23:26:40'),
(17, 'gosho', 'Георги Георгиев', 'gosho@abv.bg', '', 1, '123123123123', 'pmi', 'fpmi', 2022, 0, 0, '2025-05-22 17:01:22', '2025-05-22 17:01:22'),
(18, 'simo', 'Симон Симонов', 'simo@abv.bg', '$2y$10$k2ud9yGXujrGn05TnV//jufoprAm18KCgD2CRWpcaFbJOQ5lEwoYa', 1, '471551055', 'pmi', 'fpmi', 2021, 1, 0, '2025-05-30 20:00:39', '2025-05-30 20:04:51'),
(19, 'miro', 'Миро Миров', 'mir40@abv.bg', '$2y$10$k2ud9yGXujrGn05TnV//jufoprAm18KCgD2CRWpcaFbJOQ5lEwoYa', 1, '47634534', 'isn', 'fpmi', 2021, 1, 0, '2025-05-30 20:02:17', '2025-05-30 20:15:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `distributed_students`
--
ALTER TABLE `distributed_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `distributions`
--
ALTER TABLE `distributions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `distribution_choices`
--
ALTER TABLE `distribution_choices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `s_d_scores`
--
ALTER TABLE `s_d_scores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `distributed_students`
--
ALTER TABLE `distributed_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `distributions`
--
ALTER TABLE `distributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `distribution_choices`
--
ALTER TABLE `distribution_choices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_grades`
--
ALTER TABLE `student_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `s_d_scores`
--
ALTER TABLE `s_d_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
