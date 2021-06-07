-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2021 at 09:59 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db201700099`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_user_car_reservation`
--

DROP TABLE IF EXISTS `dbproj_user_car_reservation`;
CREATE TABLE `dbproj_user_car_reservation` (
  `user_car_reservation_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `car_id` int(10) UNSIGNED NOT NULL,
  `pickup_date` date NOT NULL,
  `return_date` date NOT NULL,
  `status` enum('confirmed','unconfirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_invoice_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `dbproj_user_car_reservation`:
--   `car_id`
--       `dbproj_car` -> `car_id`
--   `sales_invoice_id`
--       `dbproj_sales_invoice` -> `sales_invoice_id`
--   `user_id`
--       `dbproj_user` -> `user_id`
--

--
-- Dumping data for table `dbproj_user_car_reservation`
--

INSERT INTO `dbproj_user_car_reservation` (`user_car_reservation_id`, `user_id`, `car_id`, `pickup_date`, `return_date`, `status`, `sales_invoice_id`, `created_at`, `updated_at`) VALUES
(1, 1, 6, '2021-05-23', '2021-05-26', 'confirmed', NULL, '2021-05-19 20:15:34', '2021-05-19 20:15:34'),
(2, 1, 7, '2021-05-21', '2021-05-24', 'confirmed', NULL, '2021-05-19 20:15:35', '2021-05-19 20:15:35'),
(3, 1, 7, '2021-05-28', '2021-05-30', 'confirmed', NULL, '2021-05-19 20:15:36', '2021-05-19 20:15:36'),
(4, 1, 4, '2021-05-20', '2021-05-30', 'confirmed', NULL, '2021-05-19 20:15:37', '2021-05-19 20:15:37'),
(5, 1, 5, '2021-05-20', '2021-05-22', 'confirmed', NULL, '2021-05-19 20:15:38', '2021-05-19 20:15:38'),
(6, 1, 5, '2021-05-28', '2021-05-28', 'confirmed', NULL, '2021-05-19 20:15:45', '2021-05-19 20:15:45'),
(7, 1, 8, '2021-05-20', '2021-05-21', 'confirmed', NULL, '2021-05-19 20:37:34', '2021-05-19 20:37:34'),
(9, 1, 1, '2021-05-26', '2021-05-28', 'unconfirmed', 14, '2021-05-27 15:02:48', '2021-05-27 15:02:48'),
(10, 1, 39, '2021-05-28', '2021-05-28', 'unconfirmed', 15, '2021-05-28 12:52:21', '2021-05-28 12:52:21'),
(11, 1, 1, '2021-06-05', '2021-06-05', 'unconfirmed', 16, '2021-05-31 00:33:39', '2021-05-31 00:33:39'),
(12, 1, 1, '2021-06-08', '2021-07-10', 'unconfirmed', 17, '2021-06-07 03:10:58', '2021-06-07 03:10:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  ADD PRIMARY KEY (`user_car_reservation_id`),
  ADD KEY `IXFK_user_car_reservation_car` (`car_id`),
  ADD KEY `IXFK_user_car_reservation_sales_invoice` (`sales_invoice_id`),
  ADD KEY `IXFK_user_car_reservation_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  MODIFY `user_car_reservation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1654321;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  ADD CONSTRAINT `FK_user_car_reservation_car` FOREIGN KEY (`car_id`) REFERENCES `dbproj_car` (`car_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_car_reservation_sales_invoice` FOREIGN KEY (`sales_invoice_id`) REFERENCES `dbproj_sales_invoice` (`sales_invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_car_reservation_user` FOREIGN KEY (`user_id`) REFERENCES `dbproj_user` (`user_id`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
