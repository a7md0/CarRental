-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2021 at 08:59 PM
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
-- Database: `car_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_car`
--

CREATE TABLE `dbproj_car` (
  `car_id` int(10) UNSIGNED NOT NULL,
  `car_model_id` int(10) UNSIGNED DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `daily_rent_rate` decimal(10,3) UNSIGNED NOT NULL,
  `license_plate` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_identification_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('available','unavailable','servicing','repairing','sold','destroyed','stolen') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_image` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_car`
--

INSERT INTO `dbproj_car` (`car_id`, `car_model_id`, `color`, `daily_rent_rate`, `license_plate`, `vehicle_identification_number`, `status`, `preview_image`) VALUES
(1, 1000, 'Black', '11.999', '478415', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_black.jpg'),
(2, 1000, 'White', '11.999', '409310', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_white.jpg'),
(3, 1000, 'Beige', '11.999', '611306', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_beige.jpg'),
(4, 1000, 'Blue', '11.999', '828601', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_blue.jpg'),
(5, 1000, 'Gray', '11.999', '309090', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_gray.jpg'),
(6, 1000, 'Red', '11.999', '59644', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_red.jpg'),
(7, 1000, 'Silver', '11.999', '370954', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_silver.jpg'),
(8, 1003, 'Black', '11.999', '675836', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_black.jpg'),
(9, 1003, 'White', '11.999', '266317', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_white.jpg'),
(10, 1003, 'Beige', '11.999', '304080', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_beige.jpg'),
(11, 1003, 'Blue', '11.999', '721451', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_blue.jpg'),
(12, 1003, 'Gray', '11.999', '695012', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_gray.jpg'),
(13, 1003, 'Red', '11.999', '310710', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_red.jpg'),
(14, 1003, 'Silver', '11.999', '468515', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_silver.jpg'),
(15, 1010, 'Black', '11.999', '410444', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_black.jpg'),
(16, 1010, 'White', '11.999', '646678', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_white.jpg'),
(17, 1010, 'Blue', '11.999', '2057', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_blue.jpg'),
(18, 1010, 'Gray', '11.999', '70250', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_gray.jpg'),
(19, 1010, 'Red', '11.999', '345081', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_red.jpg'),
(20, 1010, 'Silver', '11.999', '514655', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_silver.jpg'),
(21, 1005, 'Black', '11.999', '538033', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_black.jpg'),
(22, 1005, 'White', '11.999', '146201', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_white.jpg'),
(23, 1005, 'Blue', '11.999', '116906', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_blue.jpg'),
(24, 1005, 'Gray', '11.999', '145927', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_gray.jpg'),
(25, 1005, 'Red', '11.999', '378919', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_red.jpg'),
(26, 1005, 'Silver', '11.999', '456813', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_silver.jpg'),
(27, 1035, 'Black', '11.999', '147311', NULL, 'available', 'assets/images/cars/honda_accord_2020_black.jpg'),
(28, 1035, 'White', '11.999', '366115', NULL, 'available', 'assets/images/cars/honda_accord_2020_white.jpg'),
(29, 1035, 'Blue', '11.999', '388641', NULL, 'available', 'assets/images/cars/honda_accord_2020_blue.jpg'),
(30, 1035, 'Red', '11.999', '844862', NULL, 'available', 'assets/images/cars/honda_accord_2020_red.jpg'),
(31, 1035, 'Silver', '11.999', '58387', NULL, 'available', 'assets/images/cars/honda_accord_2020_silver.jpg'),
(32, 1015, 'Black', '11.999', '757350', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_black.jpg'),
(33, 1015, 'White', '11.999', '611589', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_white.jpg'),
(34, 1015, 'Blue', '11.999', '785897', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_blue.jpg'),
(35, 1015, 'Red', '11.999', '94716', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_red.jpg'),
(37, 1020, 'White', '11.999', '115889', NULL, 'available', 'assets/images/cars/gmc_acadia_2020_white.jpg'),
(38, 1030, 'Black', '11.999', '295298', NULL, 'available', 'assets/images/cars/ford_mustang_2020_black.jpg'),
(39, 1030, 'Blue', '11.999', '128826', NULL, 'available', 'assets/images/cars/ford_mustang_2020_blue.jpg'),
(40, 1030, 'Lime', '11.999', '758235', NULL, 'available', 'assets/images/cars/ford_mustang_2020_lime.jpg'),
(41, 1030, 'Orange', '11.999', '404699', NULL, 'available', 'assets/images/cars/ford_mustang_2020_orange.jpg'),
(42, 1030, 'Red', '11.999', '748789', NULL, 'available', 'assets/images/cars/ford_mustang_2020_red.jpg'),
(43, 1030, 'White', '11.999', '529850', NULL, 'available', 'assets/images/cars/ford_mustang_2020_white.jpg'),
(44, 1025, 'Black', '11.999', '402882', NULL, 'available', 'assets/images/cars/ford_explorer_2020_black.jpg'),
(45, 1025, 'Blue', '11.999', '424859', NULL, 'available', 'assets/images/cars/ford_explorer_2020_blue.jpg'),
(46, 1025, 'Silver', '11.999', '915653', NULL, 'available', 'assets/images/cars/ford_explorer_2020_silver.jpg'),
(47, 1010, 'Silver', '11.999', '345081', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_silver.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_car_accessory`
--

CREATE TABLE `dbproj_car_accessory` (
  `car_accessory_id` int(10) UNSIGNED NOT NULL,
  `car_type_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_image` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge` decimal(10,3) UNSIGNED NOT NULL DEFAULT 0.000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_car_accessory`
--

INSERT INTO `dbproj_car_accessory` (`car_accessory_id`, `car_type_id`, `name`, `preview_image`, `charge`) VALUES
(100, 100, 'Toddler safety seat (1-3 years)', NULL, '0.000'),
(101, 101, 'Toddler safety seat (1-3 years)', NULL, '0.000'),
(102, 100, 'Navigation System', NULL, '0.000'),
(103, 101, 'Navigation System', NULL, '0.000'),
(104, 102, 'Navigation System', NULL, '0.000'),
(105, 103, 'Navigation System', NULL, '0.000'),
(107, 100, 'Dash cam', NULL, '0.000'),
(108, 101, 'Dash cam', NULL, '0.000'),
(109, 102, 'Dash cam', NULL, '0.000'),
(110, 103, 'Dash cam', NULL, '0.000'),
(111, 100, 'Backup Camera', NULL, '0.000'),
(112, 101, 'Backup Camera', NULL, '0.000'),
(113, 100, 'Rear-seat Entertainment System', NULL, '0.000'),
(114, 101, 'Rear-seat Entertainment System', NULL, '0.000'),
(115, 100, '\r\nChild safety seat (4-7 years)', NULL, '0.000'),
(116, 101, '\r\nChild safety seat (4-7 years)', NULL, '0.000'),
(117, 100, '\r\nBaby safety seat (0-12 months)', NULL, '0.000'),
(118, 101, 'Baby safety seat (0-12 months)', NULL, '0.000');

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_car_model`
--

CREATE TABLE `dbproj_car_model` (
  `car_model_id` int(10) UNSIGNED NOT NULL,
  `car_type_id` int(10) UNSIGNED DEFAULT NULL,
  `brand` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` year(4) NOT NULL,
  `number_of_seats` int(11) DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_car_model`
--

INSERT INTO `dbproj_car_model` (`car_model_id`, `car_type_id`, `brand`, `model`, `year`, `number_of_seats`) VALUES
(1000, 100, 'Hyundai', 'Accent', 2020, 4),
(1001, 100, 'Hyundai', 'Accent', 2019, 4),
(1002, 100, 'Hyundai', 'Accent', 2018, 4),
(1003, 100, 'Hyundai', 'Accent', 2017, 4),
(1004, 100, 'Hyundai', 'Accent', 2016, 4),
(1005, 100, 'Hyundai', 'Elantra', 2020, 4),
(1006, 100, 'Hyundai', 'Elantra', 2019, 4),
(1007, 100, 'Hyundai', 'Elantra', 2018, 4),
(1008, 100, 'Hyundai', 'Elantra', 2017, 4),
(1009, 100, 'Hyundai', 'Elantra', 2016, 4),
(1010, 100, 'Hyundai', 'Sonata', 2020, 4),
(1011, 100, 'Hyundai', 'Sonata', 2019, 4),
(1012, 100, 'Hyundai', 'Sonata', 2018, 4),
(1013, 100, 'Hyundai', 'Sonata', 2017, 4),
(1014, 100, 'Hyundai', 'Sonata', 2016, 4),
(1015, 101, 'GMC', 'Yukon', 2020, 4),
(1016, 101, 'GMC', 'Yukon', 2019, 4),
(1017, 101, 'GMC', 'Yukon', 2018, 4),
(1018, 101, 'GMC', 'Yukon', 2017, 4),
(1019, 101, 'GMC', 'Yukon', 2016, 4),
(1020, 101, 'GMC', 'Acadia', 2020, 4),
(1021, 101, 'GMC', 'Acadia', 2019, 4),
(1022, 101, 'GMC', 'Acadia', 2018, 4),
(1023, 101, 'GMC', 'Acadia', 2017, 4),
(1024, 101, 'GMC', 'Acadia', 2016, 4),
(1025, 101, 'Ford', 'Explorer', 2020, 4),
(1026, 101, 'Ford', 'Explorer', 2019, 4),
(1027, 101, 'Ford', 'Explorer', 2018, 4),
(1028, 101, 'Ford', 'Explorer', 2017, 4),
(1029, 101, 'Ford', 'Explorer', 2016, 4),
(1030, 102, 'Ford', 'Mustang', 2020, 4),
(1031, 102, 'Ford', 'Mustang', 2019, 4),
(1032, 102, 'Ford', 'Mustang', 2018, 4),
(1033, 102, 'Ford', 'Mustang', 2017, 4),
(1034, 102, 'Ford', 'Mustang', 2016, 4),
(1035, 100, 'Honda', 'Accord', 2020, 4),
(1036, 100, 'Honda', 'Accord', 2019, 4),
(1037, 100, 'Honda', 'Accord', 2018, 4),
(1038, 100, 'Honda', 'Accord', 2017, 4),
(1039, 100, 'Honda', 'Accord', 2016, 4);

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_car_reservation_accessory`
--

CREATE TABLE `dbproj_car_reservation_accessory` (
  `user_car_reservation_id` int(10) UNSIGNED NOT NULL,
  `car_accessory_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_car_type`
--

CREATE TABLE `dbproj_car_type` (
  `car_type_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_car_type`
--

INSERT INTO `dbproj_car_type` (`car_type_id`, `type`) VALUES
(100, 'Sedan'),
(101, 'SUV'),
(102, 'Convertible'),
(103, 'Sports');

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_sales_invoice`
--

CREATE TABLE `dbproj_sales_invoice` (
  `sales_invoice_id` int(10) UNSIGNED NOT NULL,
  `status` enum('unpaid','paid','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_amount` decimal(10,3) UNSIGNED DEFAULT 0.000,
  `grand_total` decimal(10,3) UNSIGNED DEFAULT 0.000,
  `remark` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_sales_invoice_item`
--

CREATE TABLE `dbproj_sales_invoice_item` (
  `sales_invoice_item_id` int(10) UNSIGNED NOT NULL,
  `sales_invoice_id` int(10) UNSIGNED NOT NULL,
  `item` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_transaction`
--

CREATE TABLE `dbproj_transaction` (
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `sales_invoice_id` int(10) UNSIGNED NOT NULL,
  `user_address_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,3) UNSIGNED NOT NULL,
  `method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('completed','refunded','declined') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime(4) NOT NULL DEFAULT current_timestamp(4),
  `updated_at` datetime(4) NOT NULL DEFAULT current_timestamp(4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_user`
--

CREATE TABLE `dbproj_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_type_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpr` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_user`
--

INSERT INTO `dbproj_user` (`user_id`, `user_type_id`, `first_name`, `last_name`, `email`, `password`, `cpr`, `nationality`, `gender`, `phone`, `created_at`, `updated_at`) VALUES
(1, 2, 'Ahmed', 'Naser', '201700099@student.polytechnic.bh', '$2y$10$szCP.rJmBHYlN5KJ.dQR4uauAdbdTNReCI5tjoe7AkUcJ7dC5PhlC', '999999999', 'bahraini', 'male', NULL, '2021-05-19 20:14:09', '2021-05-19 20:14:09'),
(3, 1, 'Naser', 'Ahmed', 'naser@csr.local', '$2y$10$m9mL5YkKu8OwdoCf2FYet.wzT.KGxcz4YEnSfLHJbsvFwkF.dg5ni', '999999999', 'bahraini', 'male', '33333333', '2021-05-22 19:22:14', '2021-05-22 19:22:14');

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_user_address`
--

CREATE TABLE `dbproj_user_address` (
  `user_address_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('billing','resident') COLLATE utf8mb4_unicode_ci NOT NULL,
  `address1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_user_car_reservation`
--

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
-- Dumping data for table `dbproj_user_car_reservation`
--

INSERT INTO `dbproj_user_car_reservation` (`user_car_reservation_id`, `user_id`, `car_id`, `pickup_date`, `return_date`, `status`, `sales_invoice_id`, `created_at`, `updated_at`) VALUES
(1, 1, 6, '2021-05-13', '2021-05-16', 'confirmed', NULL, '2021-05-19 20:15:34', '2021-05-19 20:15:34'),
(2, 1, 7, '2021-05-11', '2021-05-14', 'confirmed', NULL, '2021-05-19 20:15:35', '2021-05-19 20:15:35'),
(3, 1, 7, '2021-05-18', '2021-05-21', 'confirmed', NULL, '2021-05-19 20:15:36', '2021-05-19 20:15:36'),
(4, 1, 5, '2021-05-10', '2021-05-20', 'confirmed', NULL, '2021-05-19 20:15:37', '2021-05-19 20:15:37'),
(5, 1, 5, '2021-05-10', '2021-05-12', 'confirmed', NULL, '2021-05-19 20:15:38', '2021-05-19 20:15:38'),
(6, 1, 5, '2021-05-18', '2021-05-18', 'confirmed', NULL, '2021-05-19 20:15:45', '2021-05-19 20:15:45'),
(7, 1, 5, '2021-05-20', '2021-05-21', 'confirmed', NULL, '2021-05-19 20:37:34', '2021-05-19 20:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_user_type`
--

CREATE TABLE `dbproj_user_type` (
  `user_type_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_level` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_user_type`
--

INSERT INTO `dbproj_user_type` (`user_type_id`, `type`, `access_level`) VALUES
(1, 'User', 0),
(2, 'Admin', 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbproj_car`
--
ALTER TABLE `dbproj_car`
  ADD PRIMARY KEY (`car_id`),
  ADD KEY `IXFK_car_car_model` (`car_model_id`);

--
-- Indexes for table `dbproj_car_accessory`
--
ALTER TABLE `dbproj_car_accessory`
  ADD PRIMARY KEY (`car_accessory_id`),
  ADD KEY `IXFK_car_accessory_car_type` (`car_type_id`);

--
-- Indexes for table `dbproj_car_model`
--
ALTER TABLE `dbproj_car_model`
  ADD PRIMARY KEY (`car_model_id`),
  ADD KEY `IXFK_car_model_car_type` (`car_type_id`);
ALTER TABLE `dbproj_car_model` ADD FULLTEXT KEY `brand_model_ft` (`brand`,`model`);

--
-- Indexes for table `dbproj_car_reservation_accessory`
--
ALTER TABLE `dbproj_car_reservation_accessory`
  ADD PRIMARY KEY (`user_car_reservation_id`,`car_accessory_id`),
  ADD KEY `IXFK_car_reservation_accessory_car_accessory` (`car_accessory_id`),
  ADD KEY `IXFK_car_reservation_accessory_user_car_reservation` (`user_car_reservation_id`);

--
-- Indexes for table `dbproj_car_type`
--
ALTER TABLE `dbproj_car_type`
  ADD PRIMARY KEY (`car_type_id`);

--
-- Indexes for table `dbproj_sales_invoice`
--
ALTER TABLE `dbproj_sales_invoice`
  ADD PRIMARY KEY (`sales_invoice_id`);

--
-- Indexes for table `dbproj_sales_invoice_item`
--
ALTER TABLE `dbproj_sales_invoice_item`
  ADD PRIMARY KEY (`sales_invoice_item_id`),
  ADD KEY `IXFK_sales_invoice_item_sales_invoice` (`sales_invoice_id`);

--
-- Indexes for table `dbproj_transaction`
--
ALTER TABLE `dbproj_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `IXFK_transaction_sales_invoice` (`sales_invoice_id`),
  ADD KEY `IXFK_transaction_user_address` (`user_address_id`);

--
-- Indexes for table `dbproj_user`
--
ALTER TABLE `dbproj_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `IXFK_user_user_type` (`user_type_id`);

--
-- Indexes for table `dbproj_user_address`
--
ALTER TABLE `dbproj_user_address`
  ADD PRIMARY KEY (`user_address_id`),
  ADD KEY `IXFK_user_address_user` (`user_id`);

--
-- Indexes for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  ADD PRIMARY KEY (`user_car_reservation_id`),
  ADD KEY `IXFK_user_car_reservation_car` (`car_id`),
  ADD KEY `IXFK_user_car_reservation_sales_invoice` (`sales_invoice_id`),
  ADD KEY `IXFK_user_car_reservation_user` (`user_id`);

--
-- Indexes for table `dbproj_user_type`
--
ALTER TABLE `dbproj_user_type`
  ADD PRIMARY KEY (`user_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbproj_car`
--
ALTER TABLE `dbproj_car`
  MODIFY `car_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `dbproj_car_accessory`
--
ALTER TABLE `dbproj_car_accessory`
  MODIFY `car_accessory_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `dbproj_car_model`
--
ALTER TABLE `dbproj_car_model`
  MODIFY `car_model_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1040;

--
-- AUTO_INCREMENT for table `dbproj_car_type`
--
ALTER TABLE `dbproj_car_type`
  MODIFY `car_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `dbproj_sales_invoice`
--
ALTER TABLE `dbproj_sales_invoice`
  MODIFY `sales_invoice_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbproj_sales_invoice_item`
--
ALTER TABLE `dbproj_sales_invoice_item`
  MODIFY `sales_invoice_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbproj_transaction`
--
ALTER TABLE `dbproj_transaction`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbproj_user`
--
ALTER TABLE `dbproj_user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dbproj_user_address`
--
ALTER TABLE `dbproj_user_address`
  MODIFY `user_address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  MODIFY `user_car_reservation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dbproj_user_type`
--
ALTER TABLE `dbproj_user_type`
  MODIFY `user_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbproj_car`
--
ALTER TABLE `dbproj_car`
  ADD CONSTRAINT `FK_car_car_model` FOREIGN KEY (`car_model_id`) REFERENCES `dbproj_car_model` (`car_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbproj_car_accessory`
--
ALTER TABLE `dbproj_car_accessory`
  ADD CONSTRAINT `FK_car_accessory_car_type` FOREIGN KEY (`car_type_id`) REFERENCES `dbproj_car_type` (`car_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbproj_car_model`
--
ALTER TABLE `dbproj_car_model`
  ADD CONSTRAINT `FK_car_model_car_type` FOREIGN KEY (`car_type_id`) REFERENCES `dbproj_car_type` (`car_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbproj_car_reservation_accessory`
--
ALTER TABLE `dbproj_car_reservation_accessory`
  ADD CONSTRAINT `FK_car_reservation_accessory_car_accessory` FOREIGN KEY (`car_accessory_id`) REFERENCES `dbproj_car_accessory` (`car_accessory_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_car_reservation_accessory_user_car_reservation` FOREIGN KEY (`user_car_reservation_id`) REFERENCES `dbproj_user_car_reservation` (`user_car_reservation_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbproj_sales_invoice_item`
--
ALTER TABLE `dbproj_sales_invoice_item`
  ADD CONSTRAINT `FK_sales_invoice_item_sales_invoice` FOREIGN KEY (`sales_invoice_id`) REFERENCES `dbproj_sales_invoice` (`sales_invoice_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbproj_transaction`
--
ALTER TABLE `dbproj_transaction`
  ADD CONSTRAINT `FK_transaction_sales_invoice` FOREIGN KEY (`sales_invoice_id`) REFERENCES `dbproj_sales_invoice` (`sales_invoice_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_transaction_user_address` FOREIGN KEY (`user_address_id`) REFERENCES `dbproj_user_address` (`user_address_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbproj_user`
--
ALTER TABLE `dbproj_user`
  ADD CONSTRAINT `FK_user_user_type` FOREIGN KEY (`user_type_id`) REFERENCES `dbproj_user_type` (`user_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbproj_user_address`
--
ALTER TABLE `dbproj_user_address`
  ADD CONSTRAINT `FK_user_address_user` FOREIGN KEY (`user_id`) REFERENCES `dbproj_user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  ADD CONSTRAINT `FK_user_car_reservation_car` FOREIGN KEY (`car_id`) REFERENCES `dbproj_car` (`car_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_user_car_reservation_sales_invoice` FOREIGN KEY (`sales_invoice_id`) REFERENCES `dbproj_sales_invoice` (`sales_invoice_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_user_car_reservation_user` FOREIGN KEY (`user_id`) REFERENCES `dbproj_user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
