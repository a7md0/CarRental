-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2021 at 05:22 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`u201700099`@`%` PROCEDURE `amend_reservation_dates` (IN `userCarReservationId` INT UNSIGNED, IN `pickupDate` DATE, IN `returnDate` DATE)  BEGIN
    SELECT @carId:=UCR.`car_id`,
            @salesInvoiceId:=UCR.`sales_invoice_id`,
            @dailyRentRate:=C.`daily_rent_rate`,
            @grandTotal:=SI.`grand_total`
            
		FROM `dbproj_user_car_reservation` AS UCR
		
		INNER JOIN `dbproj_car` AS C
			ON UCR.`car_id` = C.`car_id`
		
		INNER JOIN `dbproj_sales_invoice` AS SI
			ON UCR.`sales_invoice_id` = SI.`sales_invoice_id`
			
		WHERE UCR.`user_car_reservation_id` = userCarReservationId;
    
    IF is_car_reserved_except(@carId, userCarReservationId, pickupDate, returnDate) = true THEN
		SET @errMsg = CONCAT('Car is not available between ', pickupDate, ' and ', returnDate);
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @errMsg;
    END IF;
    
    SET @reservationPeriod = (returnDate - pickupDate) + 1;
    
    INSERT INTO `dbproj_sales_invoice_item`(`sales_invoice_id`, `item`, `price`) VALUES (@salesInvoiceId, 'Amendation fees', @grandTotal * 0.10);
    
    UPDATE `dbproj_user_car_reservation` AS UCR
		INNER JOIN `dbproj_sales_invoice_item` AS SII
			ON UCR.`sales_invoice_id` = SII.`sales_invoice_id` AND SII.`item` = 'Car rent'
		SET UCR.`pickup_date` = pickupDate, UCR.`return_date` = returnDate, UCR.`is_amended` = true, SII.`price` = @reservationPeriod * @dailyRentRate
		WHERE UCR.`user_car_reservation_id` = userCarReservationId;
	
    CALL update_sales_invoice(@salesInvoiceId);
END$$

CREATE DEFINER=`u201700099`@`%` PROCEDURE `apply_amend_fees` (IN `salesInvoiceId` INT UNSIGNED)  NO SQL
BEGIN

SELECT @grandTotal:=`grand_total` FROM `dbproj_sales_invoice` WHERE `sales_invoice_id` = salesInvoiceId;

INSERT INTO `dbproj_sales_invoice_item`(`sales_invoice_id`, `item`, `price`) VALUES (salesInvoiceId, 'Amendation fees', @grandTotal * 0.10);

END$$

CREATE DEFINER=`u201700099`@`%` PROCEDURE `cancel_reservation` (IN `user_car_reservation_id` INT UNSIGNED)  BEGIN
    DECLARE sales_invoice_id INT UNSIGNED;
    
    UPDATE `dbproj_user_car_reservation` AS UCR
    
		INNER JOIN `dbproj_sales_invoice` AS SI
			ON UCR.`sales_invoice_id` = SI.`sales_invoice_id`
		LEFT JOIN `dbproj_transaction` AS T
			ON UCR.`sales_invoice_id` = T.`sales_invoice_id`
			AND T.`status` = 'completed'
			
		SET UCR.`status` = 'cancelled', SI.`status` = 'cancelled', SI.paid_amount = 0.000, T.`status` = 'refunded'

		WHERE UCR.`user_car_reservation_id` = user_car_reservation_id;
END$$

CREATE DEFINER=`u201700099`@`%` PROCEDURE `update_sales_invoice` (IN `salesInvoiceId` INT UNSIGNED)  BEGIN
    SELECT @totalAmount:=COALESCE(SUM(SII.`price`), 0), @paidAmount:=SI.`paid_amount`
    FROM `dbproj_sales_invoice` AS SI
		INNER JOIN `dbproj_sales_invoice_item` AS SII
			ON SI.`sales_invoice_id` = SII.`sales_invoice_id`
		WHERE SI.`sales_invoice_id` = salesInvoiceId;
    
    UPDATE `dbproj_sales_invoice`
		SET `grand_total` = @totalAmount, `status` = if(@paidAmount >= @totalAmount, 'paid', 'unpaid'), `paid_amount` = if(@paidAmount > @totalAmount, @totalAmount, @paidAmount)
		WHERE `sales_invoice_id` = salesInvoiceId;
END$$

--
-- Functions
--
CREATE DEFINER=`u201700099`@`%` FUNCTION `is_car_reserved` (`carId` INT UNSIGNED, `pickupDate` DATE, `returnDate` DATE) RETURNS TINYINT(1) BEGIN
	DECLARE matches INT;
    
	SELECT COUNT(*) INTO matches FROM `dbproj_user_car_reservation`
		WHERE `car_id`= carId
		AND `status` = 'confirmed'
		AND `return_date` >= pickupDate AND `pickup_date` <= returnDate;
    
	RETURN matches > 0;
END$$

CREATE DEFINER=`u201700099`@`%` FUNCTION `is_car_reserved_except` (`carId` INT UNSIGNED, `userCarReservationId` INT UNSIGNED, `pickupDate` DATE, `returnDate` DATE) RETURNS TINYINT(1) BEGIN
	DECLARE matches INT;
    
	SELECT COUNT(*) INTO matches FROM `dbproj_user_car_reservation`
		WHERE `car_id`= carId
        AND `user_car_reservation_id` <> userCarReservationId
		AND `status` = 'confirmed'
		AND `return_date` >= pickupDate AND `pickup_date` <= returnDate;
    
	RETURN matches > 0;
END$$

DELIMITER ;

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
  `preview_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_car`
--

INSERT INTO `dbproj_car` (`car_id`, `car_model_id`, `color`, `daily_rent_rate`, `license_plate`, `vehicle_identification_number`, `status`, `preview_image`) VALUES
(243, 9, 'Black', '15.000', '891242', NULL, 'available', 'assets/images/cars/ford_explorer_2020_black.jpg'),
(268, 3, 'Black', '25.000', '855195', NULL, 'available', 'assets/images/cars/ford_mustang_2020_black.jpg'),
(636, 22, 'Black', '7.000', '761485', NULL, 'available', 'assets/images/cars/honda_accord_2020_black.jpg'),
(658, 35, 'Red', '6.000', '889925', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_red.jpg'),
(682, 15, 'White', '16.000', '218071', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_white.jpg'),
(704, 35, 'Silver', '6.000', '443149', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_silver.jpg'),
(715, 35, 'Black', '6.000', '413095', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_black.jpg'),
(796, 22, 'White', '7.000', '125886', NULL, 'available', 'assets/images/cars/honda_accord_2020_white.jpg'),
(1148, 37, 'Gray', '8.000', '984133', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_gray.jpg'),
(1236, 9, 'Silver', '15.000', '919860', NULL, 'available', 'assets/images/cars/ford_explorer_2020_silver.jpg'),
(1354, 3, 'Orange', '5.000', '587268', NULL, 'available', 'assets/images/cars/ford_mustang_2020_orange.jpg'),
(1446, 29, 'Silver', '5.500', '299887', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_silver.jpg'),
(1638, 29, 'White', '5.500', '341257', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_white.jpg'),
(2211, 22, 'Blue', '7.000', '344977', NULL, 'available', 'assets/images/cars/honda_accord_2020_blue.jpg'),
(2376, 36, 'Blue', '5.000', '762985', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_blue.jpg'),
(2416, 15, 'Blue', '16.000', '697346', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_blue.jpg'),
(2711, 9, 'Blue', '15.000', '718717', NULL, 'available', 'assets/images/cars/ford_explorer_2020_blue.jpg'),
(2742, 29, 'Black', '5.500', '979880', 'XYZ', 'unavailable', 'assets/images/cars/hyundai_accent_2020_black.jpg'),
(2891, 15, 'Red', '16.000', '832517', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_red.jpg'),
(3539, 37, 'Blue', '8.000', '783806', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_blue.jpg'),
(3568, 29, 'Blue', '5.500', '809458', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_blue.jpg'),
(3788, 29, 'Gray', '5.500', '747352', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_gray.jpg'),
(4342, 13, 'White', '18.000', '70549', NULL, 'available', 'assets/images/cars/gmc_acadia_2020_white.jpg'),
(4737, 37, 'Red', '8.000', '569249', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_red.jpg'),
(5063, 36, 'Red', '5.000', '751576', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_red.jpg'),
(5553, 36, 'Gray', '5.000', '408106', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_gray.jpg'),
(6327, 36, 'White', '5.000', '971682', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_white.jpg'),
(6841, 3, 'Lime', '25.000', '756050', NULL, 'available', 'assets/images/cars/ford_mustang_2020_lime.jpg'),
(6936, 22, 'Silver', '7.000', '701203', NULL, 'available', 'assets/images/cars/honda_accord_2020_silver.jpg'),
(7906, 3, 'Red', '25.000', '668194', NULL, 'available', 'assets/images/cars/ford_mustang_2020_red.jpg'),
(7925, 36, 'Silver', '5.000', '533565', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_silver.jpg'),
(7928, 35, 'Silver', '6.000', '363356', '4', 'available', 'assets/images/cars/hyundai_sonata_2020_silver.jpg'),
(7992, 37, 'Silver', '8.000', '893846', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_silver.jpg'),
(8264, 36, 'Beige', '5.000', '135606', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_beige.jpg'),
(8448, 29, 'Beige', '5.500', '766646', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_beige.jpg'),
(8647, 35, 'White', '6.000', '464781', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_white.jpg'),
(8798, 37, 'White', '8.000', '644965', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_white.jpg'),
(8912, 36, 'Black', '5.000', '574268', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_black.jpg'),
(9097, 3, 'Blue', '25.000', '64327', NULL, 'available', 'assets/images/cars/ford_mustang_2020_blue.jpg'),
(9136, 15, 'Black', '16.000', '464336', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_black.jpg'),
(9191, 22, 'Red', '7.000', '347227', NULL, 'available', 'assets/images/cars/honda_accord_2020_red.jpg'),
(9252, 35, 'Blue', '6.000', '84620', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_blue.jpg'),
(9505, 35, 'Gray', '6.000', '28757', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_gray.jpg'),
(9520, 29, 'Red', '5.500', '308389', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_red.jpg'),
(9600, 37, 'Black', '8.000', '147007', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_black.jpg'),
(9974, 3, 'White', '25.000', '579165', NULL, 'available', 'assets/images/cars/ford_mustang_2020_white.jpg');

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
(100, 100, '\r\nToddler safety seat', 'assets/images/accessories/toddler_safety_seat.jpg', '9.000'),
(101, 101, '\r\nToddler safety seat', 'assets/images/accessories/toddler_safety_seat.jpg', '9.000'),
(102, 100, 'Navigation System', 'assets/images/accessories/navigational_system.jpg', '5.000'),
(103, 101, 'Navigation System', 'assets/images/accessories/navigational_system.jpg', '5.000'),
(104, 102, 'Navigation System', 'assets/images/accessories/navigational_system.jpg', '5.000'),
(105, 103, 'Navigation System', 'assets/images/accessories/navigational_system.jpg', '5.000'),
(107, 100, 'Dash cam', 'assets/images/accessories/dash_camera.jpg', '6.000'),
(108, 101, 'Dash cam', 'assets/images/accessories/dash_camera.jpg', '6.000'),
(109, 102, 'Dash cam', 'assets/images/accessories/dash_camera.jpg', '6.000'),
(110, 103, 'Dash cam', 'assets/images/accessories/dash_camera.jpg', '6.000'),
(111, 100, 'Backup Camera', 'assets/images/accessories/backup_camera.jpg', '11.500'),
(112, 101, 'Backup Camera', 'assets/images/accessories/backup_camera.jpg', '11.500'),
(113, 100, 'Entertainment System', 'assets/images/accessories/entertainment_system.jpg', '16.500'),
(114, 101, 'Entertainment System', 'assets/images/accessories/entertainment_system.jpg', '16.500'),
(115, 100, '\r\nInfant safety seat', 'assets/images/accessories/infant_safety_seat.jpg', '15.000'),
(116, 101, '\r\nInfant safety seat', 'assets/images/accessories/infant_safety_seat.jpg', '15.000');

-- --------------------------------------------------------

--
-- Stand-in structure for view `dbproj_car_detail`
-- (See below for the actual view)
--
CREATE TABLE `dbproj_car_detail` (
`car_id` int(10) unsigned
,`car_model_id` int(10) unsigned
,`color` varchar(50)
,`daily_rent_rate` decimal(10,3) unsigned
,`license_plate` varchar(50)
,`vehicle_identification_number` varchar(50)
,`status` enum('available','unavailable','servicing','repairing','sold','destroyed','stolen')
,`preview_image` varchar(255)
,`brand` varchar(50)
,`model` varchar(50)
,`year` year(4)
,`number_of_seats` int(11)
,`car_type_id` int(10) unsigned
,`type` varchar(50)
);

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
(1, 101, 'Ford', 'Explorer', 2016, 7),
(2, 103, 'Ford', 'Mustang', 2017, 4),
(3, 103, 'Ford', 'Mustang', 2020, 4),
(4, 103, 'Ford', 'Mustang', 2018, 4),
(5, 101, 'Ford', 'Explorer', 2017, 7),
(6, 103, 'Ford', 'Mustang', 2016, 4),
(7, 101, 'Ford', 'Explorer', 2019, 7),
(8, 103, 'Ford', 'Mustang', 2019, 4),
(9, 101, 'Ford', 'Explorer', 2020, 7),
(10, 101, 'Ford', 'Explorer', 2018, 7),
(11, 101, 'GMC', 'Yukon', 2018, 8),
(12, 101, 'GMC', 'Acadia', 2017, 7),
(13, 101, 'GMC', 'Acadia', 2020, 7),
(14, 101, 'GMC', 'Acadia', 2016, 7),
(15, 101, 'GMC', 'Yukon', 2020, 8),
(16, 101, 'GMC', 'Acadia', 2019, 7),
(17, 101, 'GMC', 'Yukon', 2017, 8),
(18, 101, 'GMC', 'Yukon', 2019, 8),
(19, 101, 'GMC', 'Acadia', 2018, 7),
(20, 101, 'GMC', 'Yukon', 2016, 8),
(21, 100, 'Honda', 'Accord', 2017, 5),
(22, 100, 'Honda', 'Accord', 2020, 5),
(23, 100, 'Honda', 'Accord', 2019, 5),
(24, 100, 'Honda', 'Accord', 2018, 5),
(25, 100, 'Honda', 'Accord', 2016, 5),
(26, 100, 'Hyundai', 'Elantra', 2018, 5),
(27, 100, 'Hyundai', 'Elantra', 2019, 5),
(28, 100, 'Hyundai', 'Accent', 2019, 5),
(29, 100, 'Hyundai', 'Accent', 2020, 5),
(30, 100, 'Hyundai', 'Sonata', 2018, 5),
(31, 100, 'Hyundai', 'Elantra', 2017, 5),
(32, 100, 'Hyundai', 'Sonata', 2017, 5),
(33, 100, 'Hyundai', 'Accent', 2018, 5),
(34, 100, 'Hyundai', 'Elantra', 2016, 5),
(35, 100, 'Hyundai', 'Sonata', 2020, 5),
(36, 100, 'Hyundai', 'Accent', 2017, 5),
(37, 100, 'Hyundai', 'Elantra', 2020, 5),
(38, 100, 'Hyundai', 'Sonata', 2019, 5),
(39, 100, 'Hyundai', 'Accent', 2016, 5),
(40, 100, 'Hyundai', 'Sonata', 2016, 5);

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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_user`
--

INSERT INTO `dbproj_user` (`user_id`, `user_type_id`, `first_name`, `last_name`, `email`, `password`, `cpr`, `nationality`, `gender`, `phone`, `created_at`, `updated_at`) VALUES
(1, 2, 'Ahmed', 'Naser', 'a7m3d699@gmail.com', '$2y$10$szCP.rJmBHYlN5KJ.dQR4uauAdbdTNReCI5tjoe7AkUcJ7dC5PhlC', '999999999', 'bahraini', 'male', NULL, '2021-05-19 20:14:09', '2021-06-14 06:01:15'),
(2, 1, 'Ali', 'Ahmed', 'admin@csr.local', '$2y$10$szCP.rJmBHYlN5KJ.dQR4uauAdbdTNReCI5tjoe7AkUcJ7dC5PhlC', '999999999', 'bahraini', 'male', '33333333', '2021-05-22 19:22:14', '2021-06-14 06:01:11');

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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_user_car_reservation`
--

CREATE TABLE `dbproj_user_car_reservation` (
  `user_car_reservation_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `car_id` int(10) UNSIGNED NOT NULL,
  `reservation_code` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pickup_date` date NOT NULL,
  `return_date` date NOT NULL,
  `status` enum('confirmed','unconfirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_amended` tinyint(1) NOT NULL DEFAULT 0,
  `sales_invoice_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Structure for view `dbproj_car_detail`
--
DROP TABLE IF EXISTS `dbproj_car_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u201700099`@`%` SQL SECURITY DEFINER VIEW `dbproj_car_detail`  AS  (select `c`.`car_id` AS `car_id`,`c`.`car_model_id` AS `car_model_id`,`c`.`color` AS `color`,`c`.`daily_rent_rate` AS `daily_rent_rate`,`c`.`license_plate` AS `license_plate`,`c`.`vehicle_identification_number` AS `vehicle_identification_number`,`c`.`status` AS `status`,`c`.`preview_image` AS `preview_image`,`cm`.`brand` AS `brand`,`cm`.`model` AS `model`,`cm`.`year` AS `year`,`cm`.`number_of_seats` AS `number_of_seats`,`ct`.`car_type_id` AS `car_type_id`,`ct`.`type` AS `type` from ((`dbproj_car` `c` join `dbproj_car_model` `cm` on(`c`.`car_model_id` = `cm`.`car_model_id`)) join `dbproj_car_type` `ct` on(`cm`.`car_type_id` = `ct`.`car_type_id`))) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbproj_car`
--
ALTER TABLE `dbproj_car`
  ADD PRIMARY KEY (`car_id`),
  ADD KEY `IXFK_car_car_model` (`car_model_id`);
ALTER TABLE `dbproj_car` ADD FULLTEXT KEY `color` (`color`,`license_plate`);

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
  MODIFY `car_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9975;

--
-- AUTO_INCREMENT for table `dbproj_car_accessory`
--
ALTER TABLE `dbproj_car_accessory`
  MODIFY `car_accessory_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `dbproj_car_model`
--
ALTER TABLE `dbproj_car_model`
  MODIFY `car_model_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9996;

--
-- AUTO_INCREMENT for table `dbproj_car_type`
--
ALTER TABLE `dbproj_car_type`
  MODIFY `car_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `dbproj_sales_invoice`
--
ALTER TABLE `dbproj_sales_invoice`
  MODIFY `sales_invoice_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `dbproj_sales_invoice_item`
--
ALTER TABLE `dbproj_sales_invoice_item`
  MODIFY `sales_invoice_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dbproj_transaction`
--
ALTER TABLE `dbproj_transaction`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dbproj_user`
--
ALTER TABLE `dbproj_user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dbproj_user_address`
--
ALTER TABLE `dbproj_user_address`
  MODIFY `user_address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  MODIFY `user_car_reservation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  ADD CONSTRAINT `FK_car_car_model` FOREIGN KEY (`car_model_id`) REFERENCES `dbproj_car_model` (`car_model_id`) ON UPDATE CASCADE;

--
-- Constraints for table `dbproj_car_accessory`
--
ALTER TABLE `dbproj_car_accessory`
  ADD CONSTRAINT `FK_car_accessory_car_type` FOREIGN KEY (`car_type_id`) REFERENCES `dbproj_car_type` (`car_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `dbproj_car_model`
--
ALTER TABLE `dbproj_car_model`
  ADD CONSTRAINT `FK_car_model_car_type` FOREIGN KEY (`car_type_id`) REFERENCES `dbproj_car_type` (`car_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `dbproj_car_reservation_accessory`
--
ALTER TABLE `dbproj_car_reservation_accessory`
  ADD CONSTRAINT `FK_car_reservation_accessory_car_accessory` FOREIGN KEY (`car_accessory_id`) REFERENCES `dbproj_car_accessory` (`car_accessory_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_car_reservation_accessory_user_car_reservation` FOREIGN KEY (`user_car_reservation_id`) REFERENCES `dbproj_user_car_reservation` (`user_car_reservation_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbproj_sales_invoice_item`
--
ALTER TABLE `dbproj_sales_invoice_item`
  ADD CONSTRAINT `FK_sales_invoice_item_sales_invoice` FOREIGN KEY (`sales_invoice_id`) REFERENCES `dbproj_sales_invoice` (`sales_invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbproj_transaction`
--
ALTER TABLE `dbproj_transaction`
  ADD CONSTRAINT `FK_transaction_sales_invoice` FOREIGN KEY (`sales_invoice_id`) REFERENCES `dbproj_sales_invoice` (`sales_invoice_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_transaction_user_address` FOREIGN KEY (`user_address_id`) REFERENCES `dbproj_user_address` (`user_address_id`) ON UPDATE CASCADE;

--
-- Constraints for table `dbproj_user`
--
ALTER TABLE `dbproj_user`
  ADD CONSTRAINT `FK_user_user_type` FOREIGN KEY (`user_type_id`) REFERENCES `dbproj_user_type` (`user_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `dbproj_user_address`
--
ALTER TABLE `dbproj_user_address`
  ADD CONSTRAINT `FK_user_address_user` FOREIGN KEY (`user_id`) REFERENCES `dbproj_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
