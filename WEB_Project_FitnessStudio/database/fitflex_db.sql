-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2026 at 09:28 PM
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
-- Database: `fitflex_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_number` varbinary(255) DEFAULT NULL,
  `cvc` char(3) NOT NULL,
  `expire_month` tinyint(4) NOT NULL,
  `expire_year` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `user_id`, `card_number`, `cvc`, `expire_month`, `expire_year`) VALUES
(1, 1, 0x38374a30354733626267535047746a59456b77774d5a76305a78505431717a757a487273625477692b53593d, '123', 12, 2025),
(2, 2, 0x6471682f3943546c4835415476457976475a2f4a7345676d426b72366b686b6963725151696d44435366553d, '369', 3, 2028),
(3, 3, 0x747273384f584552486b584650624d6233756a6879336e45493237734a30424f556e547346524d4a6955673d, '456', 11, 2030),
(4, 4, 0x48782b395553636d6354713654466b49374433436b5843367649422f50306e656e6a506b7262564143574d3d, '258', 9, 2026),
(5, 5, 0x38414e7a6b636a38713732486f3544514b396a446b69766f3875667a49344e484b78546966567a67334d513d, '159', 4, 2027);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `class_id`, `booked_at`) VALUES
(3, 1, 3, '2025-05-26 21:34:37'),
(4, 1, 10, '2025-05-28 23:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('Mind & Body','High Intensity','Core Strength','Cardio Boost','Dance Fitness','Strength Training') NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `name`, `category`, `description`, `image_url`, `created_at`) VALUES
(3, 'Pilates', 'Core Strength', 'Pilates sessions for a stronger core', 'pilates.jpg', '2025-05-25 20:44:36'),
(5, 'Zumba', 'Dance Fitness', 'Dance‐inspired cardio to upbeat music', 'zumba.jpg', '2025-05-25 20:44:36'),
(6, 'CrossFit', 'Strength Training', 'Functional strength & conditioning workouts', 'crossfit.jpg', '2025-05-25 20:44:36'),
(10, 'Yoga', 'Mind & Body', 'Mindful flows to strengthen and relax.', 'class_68379ee7e12892.57704899.jpg', '2025-05-28 23:40:23'),
(11, 'Boxing', 'High Intensity', 'Power punches, peak fitness.', 'class_68379f7c1f2885.22974299.jpg', '2025-05-28 23:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `subscriber_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_plans`
--

CREATE TABLE `nutrition_plans` (
  `plan_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_classes`
--

CREATE TABLE `saved_classes` (
  `saved_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saved_classes`
--

INSERT INTO `saved_classes` (`saved_id`, `user_id`, `class_id`, `saved_at`) VALUES
(6, 1, 5, '2025-05-27 21:46:53'),
(8, 1, 6, '2025-05-28 23:57:26');

-- --------------------------------------------------------

--
-- Table structure for table `tips`
--

CREATE TABLE `tips` (
  `tip_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `author` varchar(50) DEFAULT NULL,
  `published_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `full_name`, `specialization`, `bio`, `image_url`, `rating`, `created_at`) VALUES
(1, 'Sarah Williams', 'Mind & Body', 'Certified yoga instructor with 10+ years experience.', 'trainer2.jpg', 4.9, '2025-05-26 21:53:31'),
(2, 'Alex Johnson', 'High Intensity', 'HIIT specialist who’s worked with pro athletes.', 'trainer1.jpg', 4.7, '2025-05-26 21:53:31'),
(3, 'Maria Lee', 'Dance Fitness', 'Zumba master trainer—makes every class a party!', 'trainer3.jpg', 4.8, '2025-05-26 21:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `date_of_birth`, `phone`, `address`, `profile_picture`, `role`, `username`, `email`, `password_hash`, `created_at`, `updated_at`) VALUES
(1, 'Alice', 'Johnson', '1990-04-12', '+15551234567', '123 Elm St, Springfield', NULL, 'customer', 'alicej', 'alice@gmail.com', '$2y$10$jflTLwtQgCPiRpCTWkwcMei.5DNJ/6QPYwAjKzLP5wbOZoQhK9I6m', '2025-05-25 20:11:17', '2025-05-29 07:16:47'),
(2, 'Bob', 'Martinez', '1985-09-30', '+15557654326', '456 Oak Ave, Metropolis', NULL, 'admin', 'bobm', 'bob@gmail.com', '$2y$10$jflTLwtQgCPiRpCTWkwcMei.5DNJ/6QPYwAjKzLP5wbOZoQhK9I6m', '2025-05-25 20:11:17', '2025-05-28 23:55:23'),
(3, 'Liam', 'Johnson', '1995-07-08', '+15559871234', '789 Pine Rd, Gotham', NULL, 'customer', 'liamj', 'liam@gmail.com', '$2y$10$uvwxyzabcd1234567890', '2025-05-25 20:11:17', '2025-05-28 23:56:15'),
(4, 'Ethan', 'Kim', '1988-11-22', '+15553456789', '321 Maple Blvd, Star City', NULL, 'customer', 'ethank', 'ethan@gmail.com', '$2y$10$efghijklmn1234567890', '2025-05-25 20:11:17', '2025-05-28 23:54:59'),
(5, 'Isabella', 'Davis', '1992-01-15', '+15556789012', '654 Birch Ln, Central City', NULL, 'admin', 'isabell', 'isabella@gmail.com', '$2y$10$opqrstuvwx1234567890', '2025-05-25 20:11:17', '2025-05-28 23:54:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `ux_user_class` (`user_id`,`class_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`subscriber_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `saved_classes`
--
ALTER TABLE `saved_classes`
  ADD PRIMARY KEY (`saved_id`),
  ADD UNIQUE KEY `ux_saved` (`user_id`,`class_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `tips`
--
ALTER TABLE `tips`
  ADD PRIMARY KEY (`tip_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `subscriber_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_classes`
--
ALTER TABLE `saved_classes`
  MODIFY `saved_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `tip_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `bank_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_classes`
--
ALTER TABLE `saved_classes`
  ADD CONSTRAINT `saved_classes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_classes_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
