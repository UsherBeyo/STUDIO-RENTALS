-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2024 at 03:09 AM
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
-- Database: `studio_rentals`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `studio_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `receipt_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `studio_id`, `name`, `email`, `booking_date`, `start_time`, `end_time`, `total_price`, `receipt_path`) VALUES
(8, 8, 'Jp Daquis', 'johnpaul.daquis31@gmail.com', '2024-12-21', '13:00:00', '15:00:00', 61.50, NULL),
(10, 8, 'Jp Daquis', 'johnpaul.daquis31@gmail.com', '2024-12-18', '10:00:00', '11:00:00', 61.50, 'uploads/receipts/_DSC0043.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `instruments`
--

CREATE TABLE `instruments` (
  `id` int(11) NOT NULL,
  `studio_id` int(11) NOT NULL,
  `instrument_name` varchar(255) NOT NULL,
  `instrument_image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instruments`
--

INSERT INTO `instruments` (`id`, `studio_id`, `instrument_name`, `instrument_image_path`) VALUES
(1, 8, 'yes', 'uploads/instruments/IMG_4732.JPG'),
(2, 8, 'gitar', 'uploads/instruments/5931ff70-2eee-408a-8246-646f4fb81869.jfif'),
(3, 9, 'hgguitar', 'uploads/instruments/DSC_3305.JPG'),
(4, 9, 'dawdaw', 'uploads/instruments/DSC_3282.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `pending_bookings`
--

CREATE TABLE `pending_bookings` (
  `id` int(11) NOT NULL,
  `studio_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `receipt_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studios`
--

CREATE TABLE `studios` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_occupancy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studios`
--

INSERT INTO `studios` (`id`, `image_path`, `name`, `description`, `price`, `max_occupancy`) VALUES
(8, 'uploads/-sykria.jfif', 'daw', 'dwad', 123.00, 2),
(9, 'uploads/1000055442.jpg', 'raw', 'raw', 156.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `terms_conditions`
--

CREATE TABLE `terms_conditions` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `terms_conditions`
--

INSERT INTO `terms_conditions` (`id`, `description`) VALUES
(1, 'By booking a studio, you agree to follow all rules and policies of K2 Band Rehearsal Studios. Payments must be completed before the reservation time. Cancellations must be made 24 hours in advance.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `token_key` varchar(255) DEFAULT NULL,
  `verified` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0=no,1=yes',
  `role` enum('admin','staff','user') NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `token_key`, `verified`, `role`, `profile_picture`) VALUES
(1, 'admin', 'admin', 'First', 'Admin', 'admin@gmail.com', 'dw', 1, 'admin', NULL),
(14, 'jp.xtras', '123', 'Jp', 'Daquis', 'johnpaul.daquis31@gmail.com', '8e806d17c475b004910a63779b0191f5', 1, 'user', 'uploads/1734395463__DSC0033.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `waiting`
--

CREATE TABLE `waiting` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waiting`
--

INSERT INTO `waiting` (`id`, `description`) VALUES
(1, 'If your requested time slot is unavailable, you may enter the waiting room. You will be notified via email if the slot becomes available due to cancellation. Please ensure your email is accurate for notifications. We do accept waiting customers, but please be aware that this involves a degree of uncertainty, as availability depends on potential cancellations. If a slot becomes available for your desired time, we will notify you via email.');

-- --------------------------------------------------------

--
-- Table structure for table `waiting_bookings`
--

CREATE TABLE `waiting_bookings` (
  `id` int(11) NOT NULL,
  `studio_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waiting_bookings`
--

INSERT INTO `waiting_bookings` (`id`, `studio_id`, `name`, `email`, `booking_date`, `start_time`, `end_time`, `created_at`) VALUES
(3, 8, 'Jp Daquis', 'johnpaul.daquis31@gmail.com', '2024-12-25', '10:00:00', '11:00:00', '2024-12-16 22:40:17'),
(4, 8, 'Jp Daquis', 'johnpaul.daquis31@gmail.com', '2024-12-25', '10:00:00', '11:00:00', '2024-12-16 22:42:35'),
(5, 8, 'Jp Daquis', 'johnpaul.daquis31@gmail.com', '2024-12-25', '10:00:00', '11:00:00', '2024-12-16 22:45:58'),
(6, 8, 'Jp Daquis', 'johnpaul.daquis31@gmail.com', '2024-12-25', '10:00:00', '11:00:00', '2024-12-16 22:46:12'),
(7, 8, 'Jp Daquis', 'johnpaul.daquis31@gmail.com', '2024-12-25', '10:00:00', '11:00:00', '2024-12-16 22:46:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `studio_id` (`studio_id`);

--
-- Indexes for table `instruments`
--
ALTER TABLE `instruments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `studio_id` (`studio_id`);

--
-- Indexes for table `pending_bookings`
--
ALTER TABLE `pending_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `studios`
--
ALTER TABLE `studios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms_conditions`
--
ALTER TABLE `terms_conditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waiting`
--
ALTER TABLE `waiting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waiting_bookings`
--
ALTER TABLE `waiting_bookings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `instruments`
--
ALTER TABLE `instruments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pending_bookings`
--
ALTER TABLE `pending_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `studios`
--
ALTER TABLE `studios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `terms_conditions`
--
ALTER TABLE `terms_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `waiting`
--
ALTER TABLE `waiting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `waiting_bookings`
--
ALTER TABLE `waiting_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`studio_id`) REFERENCES `studios` (`id`);

--
-- Constraints for table `instruments`
--
ALTER TABLE `instruments`
  ADD CONSTRAINT `instruments_ibfk_1` FOREIGN KEY (`studio_id`) REFERENCES `studios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
