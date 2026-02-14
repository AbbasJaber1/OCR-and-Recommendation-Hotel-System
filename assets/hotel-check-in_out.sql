-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 07:05 PM
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
  `return_time` datetime DEFAULT NULL,
  `reason_for_leaving` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `check_logs`
--

INSERT INTO `check_logs` (`log_id`, `guest_id`, `checkout_time`, `return_time`, `reason_for_leaving`) VALUES
(368, 25, '2025-01-30 23:38:52', '2025-02-01 19:13:07', 'زيارة'),
(369, 25, '2025-01-31 11:02:40', '2025-02-01 19:13:07', 'تنزه'),
(382, 25, '2025-02-01 20:03:58', '2025-02-01 21:18:36', 'زيارة'),
(384, 25, '2025-02-01 21:19:33', '2025-02-01 21:32:59', 'زيارة'),
(385, 25, '2025-02-01 21:19:42', '2025-02-01 21:32:59', 'زيارة'),
(388, 25, '2025-02-01 21:32:54', '2025-02-01 21:32:59', 'زيارة'),
(389, 25, '2025-02-01 21:33:15', '2025-02-01 21:33:22', 'زيارة'),
(390, 25, '2025-02-01 22:09:14', '2025-02-02 00:01:02', 'زيارة'),
(391, 25, '2025-02-01 22:21:44', '2025-02-02 00:01:03', 'زيارة'),
(392, 25, '2025-02-01 22:22:29', '2025-02-02 00:01:04', 'زيارة'),
(393, 29, '2025-02-01 23:48:08', '2025-02-02 00:01:05', 'زيارة'),
(394, 27, '2025-02-01 23:48:40', '2025-02-02 00:01:05', 'زيارة'),
(395, 29, '2025-02-01 23:58:16', '2025-02-02 00:01:06', 'زيارة'),
(396, 28, '2025-02-02 00:01:28', '2025-02-03 10:09:37', 'زيارة'),
(397, 25, '2025-02-03 10:09:47', '2025-02-03 10:12:56', 'زيارة'),
(398, 27, '2025-02-03 10:12:25', '2025-02-03 10:12:57', 'زيارة'),
(399, 30, '2025-02-03 10:15:01', NULL, 'زيارة');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `room_number` int(11) DEFAULT NULL,
  `passwords` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `image_paths` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guest_id`, `guest_name`, `room_number`, `passwords`, `role`, `image_paths`) VALUES
(25, 'عباس زهير جابر', 102, '44', 'admin', './label/عباس زهير جابر/1.png,./label/عباس زهير جابر/2.png,./label/عباس زهير جابر/3.png,./label/عباس زهير جابر/4.png'),
(27, 'نور محمد ايوب', 111, '66', 'guest', './label/نور محمد ايوب/1.png,./label/نور محمد ايوب/2.png,./label/نور محمد ايوب/3.png,./label/نور محمد ايوب/4.png'),
(28, 'بهية انور سبيتي', 312, '77', 'guest', './label/بهية انور سبيتي/1.png,./label/بهية انور سبيتي/2.png,./label/بهية انور سبيتي/3.png,./label/بهية انور سبيتي/4.png'),
(29, 'علي محمد فقيه', 201, '88', 'guest', './label/علي محمد فقيه/1.png,./label/علي محمد فقيه/2.png,./label/علي محمد فقيه/3.png,./label/علي محمد فقيه/4.png'),
(30, 'علي احمد مرعي', 105, '99', 'guest', './label/علي احمد مرعي/1.png,./label/علي احمد مرعي/2.png,./label/علي احمد مرعي/3.png,./label/علي احمد مرعي/4.png');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_number`) VALUES
(101),
(102),
(103),
(104),
(105),
(106),
(107),
(108),
(109),
(110),
(111),
(112),
(113),
(114),
(115),
(116),
(117),
(118),
(201),
(202),
(203),
(204),
(205),
(206),
(207),
(208),
(209),
(210),
(211),
(212),
(213),
(214),
(215),
(216),
(217),
(218),
(301),
(302),
(303),
(304),
(305),
(306),
(307),
(308),
(309),
(310),
(311),
(312),
(313),
(314),
(315),
(316),
(317),
(318),
(401),
(402),
(403),
(404),
(405),
(406),
(407),
(408),
(409),
(410);

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
  ADD PRIMARY KEY (`guest_id`),
  ADD KEY `fk_room_number` (`room_number`);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=400;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `check_logs`
--
ALTER TABLE `check_logs`
  ADD CONSTRAINT `check_logs_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE;

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `fk_room_number` FOREIGN KEY (`room_number`) REFERENCES `rooms` (`room_number`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
