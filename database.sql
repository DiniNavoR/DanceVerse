-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2026 at 07:35 PM
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
-- Database: `danceverse`
--

-- --------------------------------------------------------

--
-- Table structure for table `challenge_submissions`
--

CREATE TABLE `challenge_submissions` (
  `id` int(11) NOT NULL,
  `challenge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `handle` varchar(50) NOT NULL,
  `video_filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `points` int(11) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_submissions`
--

INSERT INTO `challenge_submissions` (`id`, `challenge_id`, `user_id`, `handle`, `video_filename`, `original_name`, `file_size`, `points`, `status`, `submitted_at`) VALUES
(1, 1, 4, '@cutie', 'cutie_1773944669_a040b42a.mp4', 'WhatsApp Video 2026-03-19 at 11.53.44 PM.mp4', 1062879, 0, 'pending', '2026-03-19 23:54:29');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `is_read`, `created_at`) VALUES
(1, 'LAPATI', 'lapati@gmail.com', 'hjgdufjnglfdkndlkjfkkfkfkk', 0, '2026-03-19 23:34:51'),
(2, 'tharuu', 'gitmiyovimma@gmail.com', 'gfdhgfhgffvjvssesretrfvc', 0, '2026-03-19 23:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `handle` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dance_style` varchar(50) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `handle`, `email`, `password`, `dance_style`, `created_at`) VALUES
(1, 'Test', 'Dancer', '@test_dancer', 'test@danceverse.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hip Hop', '2026-03-19 23:17:55'),
(2, 'tharu', 'sub', '@gh', 'yovinmaranawaka@gmail.com', '$2y$10$V6w4O9cVqAYKe3jEXGdbyuVGY3jXDaPcAb7LSlbsUbhcN95QHSzvy', 'Traditional', '2026-03-19 23:19:46'),
(3, 'Dinithi', 'Navodya', '@hj', 'dinithi@gmail.com', '$2y$10$zyVWM1UkSgVlafD1rMluNu493Hm/VvZ8shDvTVFlEShzSmokTk.4S', 'Freestyle', '2026-03-19 23:22:13'),
(4, 'githmi', 'yovinma', '@cutie', 'gitmiyovimma@gmail.com', '$2y$10$Zxp78gBeG23roGVEOwfGjuVYjQAushuqX9Or8mJ0cJ42uw3sO7oO6', 'Freestyle', '2026-03-19 23:25:17');

-- --------------------------------------------------------

--
-- Table structure for table `weekly_challenges`
--

CREATE TABLE `weekly_challenges` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `weekly_challenges`
--

INSERT INTO `weekly_challenges` (`id`, `title`, `description`, `is_active`, `created_at`) VALUES
(1, 'Hip Hop Freestyle Challenge', 'Upload your freestyle video and win the leaderboard', 1, '2026-03-19 23:51:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `challenge_id` (`challenge_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `handle` (`handle`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `weekly_challenges`
--
ALTER TABLE `weekly_challenges`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `weekly_challenges`
--
ALTER TABLE `weekly_challenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  ADD CONSTRAINT `challenge_submissions_ibfk_1` FOREIGN KEY (`challenge_id`) REFERENCES `weekly_challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_submissions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
