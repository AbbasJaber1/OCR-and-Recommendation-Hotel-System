-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2026 at 07:31 AM
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
-- Database: `hotel-check-in/out`
--

-- --------------------------------------------------------

--
-- Table structure for table `check_logs`
--

CREATE TABLE `check_logs` (
  `log_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `checkout_time` datetime NOT NULL,
  `return_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `check_logs`
--

INSERT INTO `check_logs` (`log_id`, `guest_id`, `checkout_time`, `return_time`) VALUES
(454, 25, '2025-05-04 13:44:14', '2025-05-04 13:46:33'),
(455, 25, '2025-05-04 13:44:24', '2025-05-04 13:46:34'),
(456, 41, '2025-05-04 13:48:11', '2025-05-04 13:48:24'),
(457, 41, '2025-05-04 13:48:21', '2025-05-04 13:48:24'),
(458, 41, '2025-05-04 13:48:26', '2025-05-04 13:48:33'),
(459, 25, '2025-05-04 13:48:58', '2025-05-04 13:49:22'),
(460, 25, '2025-05-04 13:49:02', '2025-05-04 13:49:22'),
(461, 25, '2025-05-04 13:49:10', '2025-05-04 13:49:22'),
(462, 25, '2025-05-04 13:49:18', '2025-05-04 13:49:23'),
(463, 25, '2025-05-04 13:57:22', '2025-05-04 13:57:33'),
(464, 25, '2025-05-04 13:57:55', '2025-05-04 13:58:00'),
(465, 25, '2025-05-05 15:42:30', '2025-05-05 15:46:01'),
(466, 25, '2025-05-05 15:49:54', '2025-05-16 22:43:01'),
(467, 25, '2025-05-16 22:42:57', '2025-05-16 22:43:01'),
(468, 25, '2025-05-16 22:43:14', '2025-05-16 22:43:16'),
(469, 25, '2025-05-18 17:08:37', '2025-05-18 17:09:47'),
(470, 25, '2026-02-02 09:42:16', '2026-02-17 08:14:21'),
(471, 25, '2026-02-17 08:14:18', '2026-02-17 08:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `image_paths` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guest_id`, `guest_name`, `role`, `image_paths`) VALUES
(25, 'عباس زهير جابر', 'admin', './label/عباس زهير جابر/1.png,./label/عباس زهير جابر/2.png,./label/عباس زهير جابر/3.png,./label/عباس زهير جابر/4.png'),
(39, 'علي محمد فقيه', 'admin', './label/علي محمد فقيه/1.png,./label/علي محمد فقيه/2.png,./label/علي محمد فقيه/3.png,./label/علي محمد فقيه/4.png'),
(40, 'محسن علي اسماعيل', 'admin', './label/محسن علي اسماعيل/1.png,./label/محسن علي اسماعيل/2.png,./label/محسن علي اسماعيل/3.png,./label/محسن علي اسماعيل/4.png'),
(41, 'نور محمد ايوب', 'admin', './label/نور محمد ايوب/1.png,./label/نور محمد ايوب/2.png,./label/نور محمد ايوب/3.png,./label/نور محمد ايوب/4.png'),
(42, 'فاطمة حسن دغمان', 'admin', './label/فاطمة حسن دغمان/1.png,./label/فاطمة حسن دغمان/2.png,./label/فاطمة حسن دغمان/3.png,./label/فاطمة حسن دغمان/4.png'),
(43, 'زينب محمد سيد احمد', 'admin', './label/زينب محمد سيد احمد/1.png,./label/زينب محمد سيد احمد/2.png,./label/زينب محمد سيد احمد/3.png,./label/زينب محمد سيد احمد/4.png');

-- --------------------------------------------------------

--
-- Table structure for table `real_guests`
--

CREATE TABLE `real_guests` (
  `real_guest_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `birth_date` varchar(50) DEFAULT NULL,
  `passport_expiry` varchar(50) DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `room_number` int(11) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `real_guests`
--

INSERT INTO `real_guests` (`real_guest_id`, `full_name`, `nationality`, `birth_date`, `passport_expiry`, `passport_number`, `gender`, `room_number`, `check_in`, `check_out`) VALUES
(15, 'BAHIA SBEITY', 'Lebanon', '2004-11-26', '2027-02-21', 'LR25610370', 'Female', 101, '2025-03-26', '2025-03-27'),
(16, 'ABBAS JABER', 'Lebanon', '2004-10-15', '2028-01-03', '42452085', 'Male', 103, '2025-03-26', '2025-03-29'),
(19, 'ABBAS ARTIL', 'Lebanon', '2005-04-18', '2034-07-23', 'LR38242894', 'Male', 107, '2025-04-22', '2025-04-24'),
(20, 'NOUR AYOUB', 'Lebanon', '2006-10-01', '2028-08-21', 'LR33695473', 'Female', 313, '2025-05-04', '2033-10-15'),
(21, 'ALI DOGHMAN', 'Lebanon', '2004-08-21', '2018-01-23', 'LR02261986', 'Male', 410, '2025-05-17', '2033-10-18'),
(22, 'BAHIA SBEITY', 'Lebanon', '2004-11-26', '2027-02-21', 'LR25610370', 'Female', 108, '2026-02-02', '2026-02-19'),
(23, 'BAHIA SBEITY', 'Lebanon', '2004-11-26', '2027-02-21', 'LR25610370', 'Female', 116, '2026-02-17', '2026-02-26');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_number` int(11) NOT NULL,
  `availability` varchar(3) DEFAULT 'YES'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_number`, `availability`) VALUES
(101, 'NO'),
(102, 'YES'),
(103, 'NO'),
(104, 'YES'),
(105, 'YES'),
(106, 'YES'),
(107, 'NO'),
(108, 'NO'),
(109, 'YES'),
(110, 'YES'),
(111, 'YES'),
(112, 'YES'),
(113, 'YES'),
(114, 'YES'),
(115, 'YES'),
(116, 'NO'),
(117, 'YES'),
(118, 'YES'),
(201, 'YES'),
(202, 'YES'),
(203, 'YES'),
(204, 'YES'),
(205, 'YES'),
(206, 'YES'),
(207, 'YES'),
(208, 'YES'),
(209, 'YES'),
(210, 'YES'),
(211, 'YES'),
(212, 'YES'),
(213, 'YES'),
(214, 'YES'),
(215, 'YES'),
(216, 'YES'),
(217, 'YES'),
(218, 'YES'),
(301, 'YES'),
(302, 'YES'),
(303, 'YES'),
(304, 'YES'),
(305, 'YES'),
(306, 'YES'),
(307, 'YES'),
(308, 'YES'),
(309, 'YES'),
(310, 'YES'),
(311, 'YES'),
(312, 'YES'),
(313, 'NO'),
(314, 'YES'),
(315, 'YES'),
(316, 'YES'),
(317, 'YES'),
(318, 'YES'),
(401, 'YES'),
(402, 'YES'),
(403, 'YES'),
(404, 'YES'),
(405, 'YES'),
(406, 'YES'),
(407, 'YES'),
(408, 'YES'),
(409, 'YES'),
(410, 'NO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `check_logs`
--
ALTER TABLE `check_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`);

--
-- Indexes for table `real_guests`
--
ALTER TABLE `real_guests`
  ADD PRIMARY KEY (`real_guest_id`),
  ADD KEY `room_number` (`room_number`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `check_logs`
--
ALTER TABLE `check_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=472;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `real_guests`
--
ALTER TABLE `real_guests`
  MODIFY `real_guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `check_logs`
--
ALTER TABLE `check_logs`
  ADD CONSTRAINT `check_logs_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE;

--
-- Constraints for table `real_guests`
--
ALTER TABLE `real_guests`
  ADD CONSTRAINT `real_guests_ibfk_1` FOREIGN KEY (`room_number`) REFERENCES `rooms` (`room_number`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
