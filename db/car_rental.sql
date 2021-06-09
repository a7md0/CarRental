-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2021 at 05:13 AM
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
	DECLARE carReservationFee DECIMAL(10, 3);
    
	START TRANSACTION;
    
    SELECT @daysDifference := (UCR.`pickup_date` - CURRENT_DATE),
			@carId:=UCR.`car_id`,
            @isAmended:=UCR.`is_amended`,
            @salesInvoiceId:=UCR.`sales_invoice_id`,
            @dailyRentRate:=C.`daily_rent_rate`
            
		FROM `dbproj_user_car_reservation` AS UCR
		
		INNER JOIN `dbproj_car` AS C
			ON UCR.`car_id` = C.`car_id`
			
		WHERE `user_car_reservation_id` = userCarReservationId;
    
    IF @isAmended = true THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reservation have been already amended before';
    END IF;
    
    IF @daysDifference < 0 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reservation pickup date is already due';
	ELSEIF @daysDifference < 2 THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reservation pickup date is due in less than two days';
    END IF;
    
    IF is_car_reserved_except(@carId, userCarReservationId, pickupDate, returnDate) = true THEN
		SET @errMsg = CONCAT('Car is not available between ', pickupDate, ' and ', returnDate);
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @errMsg;
    END IF;
    
    SET @reservationPeriod = (returnDate - pickupDate) + 1;
    SET carReservationFee = @reservationPeriod * @dailyRentRate;
    
    UPDATE `dbproj_user_car_reservation` AS UCR
		INNER JOIN `dbproj_sales_invoice_item` AS SII
			ON UCR.`sales_invoice_id` = SII.`sales_invoice_id` AND SII.item = 'Car rent'
		SET UCR.`pickup_date` = pickupDate, UCR.`return_date` = returnDate, UCR.`is_amended` = true, SII.`price` = carReservationFee
		WHERE UCR.`user_car_reservation_id` = userCarReservationId;
        
	INSERT INTO `dbproj_sales_invoice_item`(`sales_invoice_id`, `item`, `price`) VALUES (@salesInvoiceId, 'Amendation fees', 12.6);
        
	COMMIT;
END$$

CREATE DEFINER=`u201700099`@`%` PROCEDURE `cancel_reservation` (IN `user_car_reservation_id` INT UNSIGNED)  BEGIN
	DECLARE sales_invoice_id INT UNSIGNED;
    
    START TRANSACTION;
    
    UPDATE `dbproj_user_car_reservation` AS UCR
    
	INNER JOIN `dbproj_sales_invoice` AS SI
		ON UCR.`sales_invoice_id` = SI.`sales_invoice_id`
	LEFT JOIN `dbproj_transaction` AS T
		ON UCR.`sales_invoice_id` = T.`sales_invoice_id`
		AND T.`status` = 'completed'
		
	SET UCR.`status` = 'cancelled', SI.`status` = 'cancelled', SI.paid_amount = 0.000, T.`status` = 'refunded'

	WHERE UCR.`user_car_reservation_id` = user_car_reservation_id;
    
    
    COMMIT;
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
(15, 1010, 'Black', '12.999', '410444', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_black.jpg'),
(16, 1010, 'White', '12.999', '646678', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_white.jpg'),
(17, 1010, 'Blue', '12.999', '2057', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_blue.jpg'),
(18, 1010, 'Gray', '12.999', '70250', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_gray.jpg'),
(19, 1010, 'Red', '12.999', '345081', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_red.jpg'),
(20, 1010, 'Silver', '12.999', '514655', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_silver.jpg'),
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
(100, 100, '\r\nToddler safety seat', 'assets/images/accessories/toddler_safety_seat.jpg', '9.999'),
(101, 101, '\r\nToddler safety seat', 'assets/images/accessories/toddler_safety_seat.jpg', '9.999'),
(102, 100, 'Navigation System', 'assets/images/accessories/navigational_system.jpg', '4.999'),
(103, 101, 'Navigation System', 'assets/images/accessories/navigational_system.jpg', '4.999'),
(104, 102, 'Navigation System', 'assets/images/accessories/navigational_system.jpg', '4.999'),
(105, 103, 'Navigation System', 'assets/images/accessories/navigational_system.jpg', '4.999'),
(107, 100, 'Dash cam', 'assets/images/accessories/dash_camera.jpg', '12.600'),
(108, 101, 'Dash cam', 'assets/images/accessories/dash_camera.jpg', '12.600'),
(109, 102, 'Dash cam', 'assets/images/accessories/dash_camera.jpg', '12.600'),
(110, 103, 'Dash cam', 'assets/images/accessories/dash_camera.jpg', '12.600'),
(111, 100, 'Backup Camera', 'assets/images/accessories/backup_camera.jpg', '7.300'),
(112, 101, 'Backup Camera', 'assets/images/accessories/backup_camera.jpg', '7.300'),
(113, 100, 'Entertainment System', 'assets/images/accessories/entertainment_system.jpg', '16.750'),
(114, 101, 'Entertainment System', 'assets/images/accessories/entertainment_system.jpg', '16.750'),
(115, 100, '\r\nInfant safety seat', 'assets/images/accessories/infant_safety_seat.jpg', '14.450'),
(116, 101, '\r\nInfant safety seat', 'assets/images/accessories/infant_safety_seat.jpg', '14.450');

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
,`preview_image` varchar(50)
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
(1000, 100, 'Hyundai', 'Accent', 2020, 5),
(1001, 100, 'Hyundai', 'Accent', 2019, 5),
(1002, 100, 'Hyundai', 'Accent', 2018, 5),
(1003, 100, 'Hyundai', 'Accent', 2017, 5),
(1004, 100, 'Hyundai', 'Accent', 2016, 5),
(1005, 100, 'Hyundai', 'Elantra', 2020, 5),
(1006, 100, 'Hyundai', 'Elantra', 2019, 5),
(1007, 100, 'Hyundai', 'Elantra', 2018, 5),
(1008, 100, 'Hyundai', 'Elantra', 2017, 5),
(1009, 100, 'Hyundai', 'Elantra', 2016, 5),
(1010, 100, 'Hyundai', 'Sonata', 2020, 5),
(1011, 100, 'Hyundai', 'Sonata', 2019, 5),
(1012, 100, 'Hyundai', 'Sonata', 2018, 5),
(1013, 100, 'Hyundai', 'Sonata', 2017, 5),
(1014, 100, 'Hyundai', 'Sonata', 2016, 5),
(1015, 101, 'GMC', 'Yukon', 2020, 8),
(1016, 101, 'GMC', 'Yukon', 2019, 8),
(1017, 101, 'GMC', 'Yukon', 2018, 8),
(1018, 101, 'GMC', 'Yukon', 2017, 8),
(1019, 101, 'GMC', 'Yukon', 2016, 8),
(1020, 101, 'GMC', 'Acadia', 2020, 7),
(1021, 101, 'GMC', 'Acadia', 2019, 7),
(1022, 101, 'GMC', 'Acadia', 2018, 7),
(1023, 101, 'GMC', 'Acadia', 2017, 7),
(1024, 101, 'GMC', 'Acadia', 2016, 7),
(1025, 101, 'Ford', 'Explorer', 2020, 7),
(1026, 101, 'Ford', 'Explorer', 2019, 7),
(1027, 101, 'Ford', 'Explorer', 2018, 7),
(1028, 101, 'Ford', 'Explorer', 2017, 7),
(1029, 101, 'Ford', 'Explorer', 2016, 7),
(1030, 102, 'Ford', 'Mustang', 2020, 4),
(1031, 102, 'Ford', 'Mustang', 2019, 4),
(1032, 102, 'Ford', 'Mustang', 2018, 4),
(1033, 102, 'Ford', 'Mustang', 2017, 4),
(1034, 102, 'Ford', 'Mustang', 2016, 4),
(1035, 100, 'Honda', 'Accord', 2020, 5),
(1036, 100, 'Honda', 'Accord', 2019, 5),
(1037, 100, 'Honda', 'Accord', 2018, 5),
(1038, 100, 'Honda', 'Accord', 2017, 5),
(1039, 100, 'Honda', 'Accord', 2016, 5);

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_car_reservation_accessory`
--

CREATE TABLE `dbproj_car_reservation_accessory` (
  `user_car_reservation_id` int(10) UNSIGNED NOT NULL,
  `car_accessory_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_car_reservation_accessory`
--

INSERT INTO `dbproj_car_reservation_accessory` (`user_car_reservation_id`, `car_accessory_id`) VALUES
(9, 100),
(9, 113),
(10, 104),
(10, 109),
(11, 100),
(11, 111),
(11, 115),
(12, 100),
(12, 107),
(13, 100),
(13, 107),
(14, 102),
(14, 113);

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

--
-- Dumping data for table `dbproj_sales_invoice`
--

INSERT INTO `dbproj_sales_invoice` (`sales_invoice_id`, `status`, `paid_amount`, `grand_total`, `remark`, `created_at`, `updated_at`) VALUES
(14, 'unpaid', '0.000', '62.746', NULL, '2021-05-27 15:02:47', '2021-05-27 15:02:47'),
(15, 'unpaid', '0.000', '29.598', NULL, '2021-05-28 12:52:21', '2021-05-28 12:52:21'),
(16, 'unpaid', '0.000', '43.748', NULL, '2021-05-31 00:33:39', '2021-05-31 00:33:39'),
(17, 'cancelled', '0.000', '418.566', NULL, '2021-06-07 03:10:57', '2021-06-07 03:10:57'),
(18, 'unpaid', '0.000', '35.598', NULL, '2021-06-08 02:01:18', '2021-06-08 02:01:18'),
(19, 'cancelled', '0.000', '16.998', NULL, '2021-06-08 22:01:26', '2021-06-08 22:01:26');

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

--
-- Dumping data for table `dbproj_sales_invoice_item`
--

INSERT INTO `dbproj_sales_invoice_item` (`sales_invoice_item_id`, `sales_invoice_id`, `item`, `price`) VALUES
(4, 14, 'Car rent', '35.997'),
(5, 14, '\r\nToddler safety seat', '9.999'),
(6, 14, 'Entertainment System', '16.750'),
(7, 15, 'Car rent', '11.999'),
(8, 15, 'Navigation System', '4.999'),
(9, 15, 'Dash cam', '12.600'),
(10, 16, 'Car rent', '11.999'),
(11, 16, '\r\nToddler safety seat', '9.999'),
(12, 16, 'Backup Camera', '7.300'),
(13, 16, '\r\nInfant safety seat', '14.450'),
(14, 17, 'Car rent', '395.967'),
(15, 17, '\r\nToddler safety seat', '9.999'),
(16, 17, 'Dash cam', '12.600'),
(17, 18, 'Car rent', '12.999'),
(18, 18, '\r\nToddler safety seat', '9.999'),
(19, 18, 'Dash cam', '12.600'),
(21, 19, 'Car rent', '11.999'),
(22, 19, 'Navigation System', '4.999');

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

--
-- Dumping data for table `dbproj_transaction`
--

INSERT INTO `dbproj_transaction` (`transaction_id`, `sales_invoice_id`, `user_address_id`, `amount`, `method`, `remark`, `status`, `created_at`, `updated_at`) VALUES
(2, 17, 1, '1.000', NULL, NULL, 'completed', '2021-06-07 23:50:14.8977', '2021-06-07 23:50:14.8977'),
(16, 19, 15, '0.000', 'Credit-card', NULL, 'refunded', '2021-06-08 23:53:40.9806', '2021-06-08 23:53:40.9806'),
(17, 19, 16, '0.000', 'Credit-card', NULL, 'refunded', '2021-06-08 23:53:55.0293', '2021-06-08 23:53:55.0293'),
(18, 19, 17, '0.000', 'Credit-card', NULL, 'refunded', '2021-06-08 23:54:08.5461', '2021-06-08 23:54:08.5461');

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

--
-- Dumping data for table `dbproj_user_address`
--

INSERT INTO `dbproj_user_address` (`user_address_id`, `user_id`, `type`, `address1`, `address2`, `country`, `city`, `zip_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'billing', NULL, NULL, NULL, NULL, NULL, '2021-06-07 23:50:10', '2021-06-07 23:50:10'),
(15, 1, 'billing', '3123', NULL, 'Belarus', NULL, NULL, '2021-06-08 23:53:40', '2021-06-08 23:53:40'),
(16, 1, 'billing', '3123', NULL, 'Belarus', NULL, NULL, '2021-06-08 23:53:54', '2021-06-08 23:53:54'),
(17, 1, 'billing', '3123', NULL, 'Belarus', NULL, NULL, '2021-06-08 23:54:08', '2021-06-08 23:54:08');

-- --------------------------------------------------------

--
-- Table structure for table `dbproj_user_car_reservation`
--

CREATE TABLE `dbproj_user_car_reservation` (
  `user_car_reservation_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `car_id` int(10) UNSIGNED NOT NULL,
  `reservation_code` int(11) NOT NULL,
  `pickup_date` date NOT NULL,
  `return_date` date NOT NULL,
  `status` enum('confirmed','unconfirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_amended` tinyint(1) NOT NULL DEFAULT 0,
  `sales_invoice_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_user_car_reservation`
--

INSERT INTO `dbproj_user_car_reservation` (`user_car_reservation_id`, `user_id`, `car_id`, `reservation_code`, `pickup_date`, `return_date`, `status`, `is_amended`, `sales_invoice_id`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 123, '2021-05-23', '2021-05-26', 'confirmed', 0, 14, '2021-05-19 20:15:34', '2021-05-19 20:15:34'),
(2, 1, 7, 1234, '2021-05-21', '2021-05-24', 'confirmed', 0, 14, '2021-05-19 20:15:35', '2021-05-19 20:15:35'),
(3, 1, 7, 12345, '2021-05-28', '2021-05-30', 'confirmed', 0, 14, '2021-05-19 20:15:36', '2021-05-19 20:15:36'),
(4, 1, 4, 123456, '2021-05-20', '2021-05-30', 'confirmed', 0, 14, '2021-05-19 20:15:37', '2021-05-19 20:15:37'),
(5, 1, 5, 1234567, '2021-05-20', '2021-05-22', 'confirmed', 0, 14, '2021-05-19 20:15:38', '2021-05-19 20:15:38'),
(6, 1, 5, 12345689, '2021-05-28', '2021-05-28', 'confirmed', 0, 14, '2021-05-19 20:15:45', '2021-05-19 20:15:45'),
(7, 1, 8, 98765, '2021-05-20', '2021-05-21', 'confirmed', 0, 14, '2021-05-19 20:37:34', '2021-05-19 20:37:34'),
(9, 1, 1, 987654, '2021-05-26', '2021-05-28', 'unconfirmed', 0, 14, '2021-05-27 15:02:48', '2021-05-27 15:02:48'),
(10, 1, 39, 9876543, '2021-05-28', '2021-05-28', 'unconfirmed', 0, 15, '2021-05-28 12:52:21', '2021-05-28 12:52:21'),
(11, 1, 1, 98765432, '2021-06-05', '2021-06-05', 'unconfirmed', 0, 16, '2021-05-31 00:33:39', '2021-05-31 00:33:39'),
(12, 1, 1, 98765321, '2021-06-09', '2021-07-10', 'cancelled', 0, 17, '2021-06-07 03:10:58', '2021-06-07 03:10:58'),
(13, 1, 17, 159, '2021-07-01', '2021-07-01', 'unconfirmed', 1, 18, '2021-06-08 02:01:18', '2021-06-08 02:01:18'),
(14, 1, 1, 35755856, '2021-06-08', '2021-06-08', 'cancelled', 0, 19, '2021-06-08 22:01:26', '2021-06-08 22:01:26');

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
  MODIFY `sales_invoice_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `dbproj_sales_invoice_item`
--
ALTER TABLE `dbproj_sales_invoice_item`
  MODIFY `sales_invoice_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `dbproj_transaction`
--
ALTER TABLE `dbproj_transaction`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `dbproj_user`
--
ALTER TABLE `dbproj_user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dbproj_user_address`
--
ALTER TABLE `dbproj_user_address`
  MODIFY `user_address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  MODIFY `user_car_reservation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
