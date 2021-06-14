-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2021 at 02:30 AM
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
(4, 3040, 'Silver', '11.999', '919860', NULL, 'available', 'assets/images/cars/ford_explorer_2020_silver.jpg'),
(75, 8120, 'White', '11.999', '971682', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_white.jpg'),
(79, 4507, 'Silver', '15.999', '363356', '4', 'available', 'assets/images/cars/hyundai_sonata_2020_silver.jpg'),
(80, 4507, 'Red', '12.999', '889925', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_red.jpg'),
(107, 4507, 'Gray', '12.999', '28757', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_gray.jpg'),
(123, 8120, 'Gray', '11.999', '408106', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_gray.jpg'),
(152, 4507, 'Blue', '12.999', '84620', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_blue.jpg'),
(155, 7116, 'Black', '11.999', '147007', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_black.jpg'),
(182, 3040, 'Black', '11.999', '891242', NULL, 'available', 'assets/images/cars/ford_explorer_2020_black.jpg'),
(188, 1352, 'Blue', '11.999', '697346', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_blue.jpg'),
(189, 4653, 'Orange', '11.999', '587268', NULL, 'available', 'assets/images/cars/ford_mustang_2020_orange.jpg'),
(220, 3409, 'Red', '11.999', '308389', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_red.jpg'),
(230, 7116, 'Blue', '11.999', '783806', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_blue.jpg'),
(250, 1352, 'Black', '11.999', '464336', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_black.jpg'),
(260, 8120, 'Red', '11.999', '751576', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_red.jpg'),
(274, 4653, 'Red', '11.999', '668194', NULL, 'available', 'assets/images/cars/ford_mustang_2020_red.jpg'),
(328, 6706, 'White', '11.999', '70549', NULL, 'available', 'assets/images/cars/gmc_acadia_2020_white.jpg'),
(339, 7116, 'Silver', '11.999', '893846', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_silver.jpg'),
(422, 4688, 'Silver', '11.999', '701203', NULL, 'available', 'assets/images/cars/honda_accord_2020_silver.jpg'),
(490, 4507, 'Silver', '11.999', '443149', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_silver.jpg'),
(510, 3040, 'Blue', '11.999', '718717', NULL, 'available', 'assets/images/cars/ford_explorer_2020_blue.jpg'),
(517, 3409, 'Silver', '11.999', '299887', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_silver.jpg'),
(533, 4653, 'Blue', '11.999', '64327', NULL, 'available', 'assets/images/cars/ford_mustang_2020_blue.jpg'),
(536, 7116, 'Gray', '11.999', '984133', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_gray.jpg'),
(537, 3409, 'Beige', '11.999', '766646', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_beige.jpg'),
(539, 7116, 'White', '11.999', '644965', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_white.jpg'),
(551, 4507, 'White', '12.999', '464781', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_white.jpg'),
(558, 4653, 'Lime', '11.999', '756050', NULL, 'available', 'assets/images/cars/ford_mustang_2020_lime.jpg'),
(602, 8120, 'Beige', '11.999', '135606', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_beige.jpg'),
(614, 4688, 'White', '11.999', '125886', NULL, 'available', 'assets/images/cars/honda_accord_2020_white.jpg'),
(619, 4688, 'Red', '11.999', '347227', NULL, 'available', 'assets/images/cars/honda_accord_2020_red.jpg'),
(694, 3409, 'Blue', '11.999', '809458', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_blue.jpg'),
(703, 4653, 'Black', '11.999', '855195', NULL, 'available', 'assets/images/cars/ford_mustang_2020_black.jpg'),
(726, 4688, 'Black', '11.999', '761485', NULL, 'available', 'assets/images/cars/honda_accord_2020_black.jpg'),
(786, 8120, 'Blue', '11.999', '762985', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_blue.jpg'),
(800, 4653, 'White', '11.999', '579165', NULL, 'available', 'assets/images/cars/ford_mustang_2020_white.jpg'),
(817, 3409, 'Black', '11.999', '979880', 'XYZ', 'unavailable', 'assets/images/cars/hyundai_accent_2020_black.jpg'),
(860, 3409, 'Gray', '11.999', '747352', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_gray.jpg'),
(868, 4507, 'Black', '12.999', '413095', NULL, 'available', 'assets/images/cars/hyundai_sonata_2020_black.jpg'),
(892, 4688, 'Blue', '11.999', '344977', NULL, 'available', 'assets/images/cars/honda_accord_2020_blue.jpg'),
(925, 8120, 'Black', '11.999', '574268', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_black.jpg'),
(929, 8120, 'Silver', '11.999', '533565', NULL, 'available', 'assets/images/cars/hyundai_accent_2017_silver.jpg'),
(978, 1352, 'Red', '11.999', '832517', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_red.jpg'),
(988, 1352, 'White', '11.999', '218071', NULL, 'available', 'assets/images/cars/gmc_yukon_2020_white.jpg'),
(989, 7116, 'Red', '11.999', '569249', NULL, 'available', 'assets/images/cars/hyundai_elantra_2020_red.jpg'),
(998, 3409, 'White', '11.999', '341257', NULL, 'available', 'assets/images/cars/hyundai_accent_2020_white.jpg');

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
(446, 101, 'GMC', 'Acadia', 2018, 7),
(863, 100, 'Hyundai', 'Elantra', 2017, 5),
(1010, 100, 'Hyundai', 'Accent', 2016, 5),
(1200, 101, 'GMC', 'Yukon', 2019, 8),
(1352, 101, 'GMC', 'Yukon', 2020, 8),
(1514, 100, 'Hyundai', 'Sonata', 2019, 5),
(2425, 100, 'Hyundai', 'Sonata', 2017, 5),
(2631, 101, 'GMC', 'Acadia', 2016, 7),
(2710, 101, 'GMC', 'Yukon', 2017, 8),
(3040, 101, 'Ford', 'Explorer', 2020, 7),
(3102, 101, 'GMC', 'Acadia', 2017, 7),
(3200, 102, 'Ford', 'Mustang', 2018, 4),
(3409, 100, 'Hyundai', 'Accent', 2020, 5),
(3446, 100, 'Honda', 'Accord', 2018, 5),
(3666, 100, 'Hyundai', 'Sonata', 2018, 5),
(4120, 100, 'Hyundai', 'Accent', 2019, 5),
(4127, 101, 'Ford', 'Explorer', 2016, 7),
(4372, 100, 'Hyundai', 'Elantra', 2019, 5),
(4507, 100, 'Hyundai', 'Sonata', 2020, 5),
(4653, 102, 'Ford', 'Mustang', 2020, 4),
(4688, 100, 'Honda', 'Accord', 2020, 5),
(5313, 101, 'Ford', 'Explorer', 2019, 7),
(5390, 100, 'Hyundai', 'Elantra', 2018, 5),
(5930, 102, 'Ford', 'Mustang', 2017, 4),
(6135, 100, 'Hyundai', 'Sonata', 2016, 5),
(6364, 102, 'Ford', 'Mustang', 2019, 4),
(6620, 101, 'GMC', 'Yukon', 2016, 8),
(6706, 101, 'GMC', 'Acadia', 2020, 7),
(6939, 101, 'GMC', 'Yukon', 2018, 8),
(7005, 100, 'Honda', 'Accord', 2019, 5),
(7116, 100, 'Hyundai', 'Elantra', 2020, 5),
(7588, 102, 'Ford', 'Mustang', 2016, 4),
(7816, 100, 'Hyundai', 'Accent', 2018, 5),
(7826, 101, 'Ford', 'Explorer', 2017, 7),
(8063, 101, 'GMC', 'Acadia', 2019, 7),
(8120, 100, 'Hyundai', 'Accent', 2017, 5),
(8172, 101, 'Ford', 'Explorer', 2018, 7),
(8345, 100, 'Hyundai', 'Elantra', 2016, 5),
(9189, 100, 'Honda', 'Accord', 2016, 5),
(9523, 100, 'Honda', 'Accord', 2017, 5);

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
(14, 113),
(15, 104),
(15, 109),
(16, 101),
(18, 101),
(18, 103),
(18, 108),
(18, 112),
(18, 114),
(18, 116),
(23, 107),
(25, 100);

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

--
-- Dumping data for table `dbproj_sales_invoice`
--

INSERT INTO `dbproj_sales_invoice` (`sales_invoice_id`, `status`, `paid_amount`, `grand_total`, `remark`, `created_at`, `updated_at`) VALUES
(14, 'unpaid', '0.000', '62.746', NULL, '2021-05-27 15:02:47', '2021-05-27 15:02:47'),
(15, 'unpaid', '0.000', '29.598', NULL, '2021-05-28 12:52:21', '2021-05-28 12:52:21'),
(16, 'unpaid', '0.000', '43.748', NULL, '2021-05-31 23:59:59', '2021-05-31 00:33:39'),
(17, 'cancelled', '0.000', '418.566', NULL, '2021-06-07 03:10:57', '2021-06-07 03:10:57'),
(18, 'unpaid', '0.000', '35.598', NULL, '2021-06-08 02:01:18', '2021-06-08 02:01:18'),
(19, 'paid', '43.053', '43.053', NULL, '2021-06-08 22:01:26', '2021-06-08 22:01:26'),
(20, 'cancelled', '0.000', '82.625', NULL, '2021-06-11 08:11:44', '2021-06-11 08:11:44'),
(21, 'paid', '21.998', '21.998', NULL, '2021-06-12 14:48:26', '2021-06-12 14:48:26'),
(22, 'unpaid', '0.000', '111.792', NULL, '2021-06-13 04:15:43', '2021-06-13 08:06:29'),
(23, 'unpaid', '0.000', '12.999', NULL, '2021-06-13 04:18:24', '2021-06-13 04:18:24'),
(24, 'unpaid', '0.000', '12.999', NULL, '2021-06-13 04:19:08', '2021-06-13 04:19:08'),
(25, 'unpaid', '0.000', '12.999', NULL, '2021-06-13 04:24:55', '2021-06-13 04:24:55'),
(26, 'unpaid', '0.000', '103.992', NULL, '2021-06-13 04:25:31', '2021-06-13 08:09:30'),
(27, 'unpaid', '36.702', '40.372', NULL, '2021-06-13 05:13:33', '2021-06-13 11:40:11'),
(28, 'unpaid', '0.000', '79.294', NULL, '2021-06-13 10:08:28', '2021-06-13 10:08:44'),
(29, 'paid', '21.998', '21.998', NULL, '2021-06-13 14:04:12', '2021-06-13 14:04:52');

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
(21, 19, 'Car rent', '23.998'),
(22, 19, 'Navigation System', '4.999'),
(25, 19, 'Amendation fees', '14.056'),
(26, 20, 'Car rent', '23.998'),
(27, 20, 'Dash cam', '12.600'),
(28, 20, 'Navigation System', '4.999'),
(29, 20, 'Amendation fees', '2.960'),
(30, 20, 'Amendation fees', '5.656'),
(31, 20, 'Amendation fees', '6.221'),
(32, 20, 'Amendation fees', '5.643'),
(33, 20, 'Amendation fees', '6.208'),
(34, 20, 'Amendation fees', '6.829'),
(35, 20, 'Amendation fees', '7.511'),
(36, 21, 'Car rent', '11.999'),
(37, 21, '\r\nToddler safety seat', '9.999'),
(38, 22, 'Car rent', '11.999'),
(39, 22, '\r\nToddler safety seat', '9.999'),
(40, 22, 'Navigation System', '4.999'),
(41, 22, 'Dash cam', '12.600'),
(42, 22, 'Backup Camera', '7.300'),
(43, 22, 'Entertainment System', '16.750'),
(44, 22, '\r\nInfant safety seat', '14.450'),
(45, 23, 'Car rent', '12.999'),
(46, 24, 'Car rent', '12.999'),
(47, 25, 'Car rent', '12.999'),
(48, 26, 'Car rent', '103.992'),
(49, 27, 'Car rent', '11.999'),
(50, 27, 'Dash cam', '12.600'),
(51, 27, 'Amendation fees', '2.460'),
(52, 27, 'Amendation fees', '6.306'),
(53, 27, 'Amendation fees', '3.337'),
(82, 26, 'Amendation fees', '0.000'),
(83, 28, 'Car rent', '77.994'),
(84, 28, 'Amendation fees', '1.300'),
(85, 27, 'Amendation fees', '3.670'),
(86, 29, 'Car rent', '11.999'),
(87, 29, '\r\nToddler safety seat', '9.999');

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

--
-- Dumping data for table `dbproj_transaction`
--

INSERT INTO `dbproj_transaction` (`transaction_id`, `sales_invoice_id`, `user_address_id`, `amount`, `method`, `remark`, `status`, `created_at`, `updated_at`) VALUES
(2, 17, 1, '1.000', NULL, NULL, 'completed', '2021-06-07 23:50:14', '2021-06-07 23:50:14'),
(16, 19, 15, '0.000', 'Credit-card', NULL, 'refunded', '2021-06-08 23:53:40', '2021-06-08 23:53:40'),
(17, 19, 16, '0.000', 'Credit-card', NULL, 'refunded', '2021-06-08 23:53:55', '2021-06-08 23:53:55'),
(18, 19, 17, '0.000', 'Credit-card', NULL, 'refunded', '2021-06-08 23:54:08', '2021-06-08 23:54:08'),
(19, 20, 18, '29.598', 'Credit-card', NULL, 'refunded', '2021-06-11 08:12:23', '2021-06-11 08:12:23'),
(20, 21, 19, '21.998', 'Credit-card', NULL, 'completed', '2021-06-12 14:48:40', '2021-06-12 14:48:40'),
(21, 22, 20, '78.097', 'Credit-card', NULL, 'completed', '2021-06-13 04:16:03', '2021-06-13 04:16:03'),
(22, 22, 21, '0.000', 'Credit-card', NULL, 'completed', '2021-06-13 04:18:36', '2021-06-13 04:18:36'),
(23, 22, 22, '0.000', 'Credit-card', NULL, 'completed', '2021-06-13 04:21:19', '2021-06-13 04:21:19'),
(24, 22, 23, '0.000', 'Credit-card', NULL, 'completed', '2021-06-13 04:24:37', '2021-06-13 04:24:37'),
(25, 22, 24, '0.000', 'Credit-card', NULL, 'completed', '2021-06-13 04:24:47', '2021-06-13 04:24:47'),
(26, 26, 25, '12.999', 'Credit-card', NULL, 'completed', '2021-06-13 04:26:03', '2021-06-13 04:26:03'),
(27, 27, 26, '24.599', 'Credit-card', NULL, 'completed', '2021-06-13 05:24:57', '2021-06-13 05:24:57'),
(28, 27, 27, '38.457', 'Credit-card', NULL, 'completed', '2021-06-13 05:28:21', '2021-06-13 05:28:21'),
(29, 29, 28, '21.998', 'Credit-card', NULL, 'completed', '2021-06-13 14:04:52', '2021-06-13 14:04:52');

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
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbproj_user_address`
--

INSERT INTO `dbproj_user_address` (`user_address_id`, `user_id`, `type`, `address1`, `address2`, `country`, `city`, `zip_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'billing', NULL, NULL, NULL, NULL, NULL, '2021-06-07 23:50:10', '2021-06-07 23:50:10'),
(15, 1, 'billing', '3123', NULL, 'Belarus', NULL, NULL, '2021-06-08 23:53:40', '2021-06-08 23:53:40'),
(16, 1, 'billing', '3123', NULL, 'Belarus', NULL, NULL, '2021-06-08 23:53:54', '2021-06-08 23:53:54'),
(17, 1, 'billing', '3123', NULL, 'Belarus', NULL, NULL, '2021-06-08 23:54:08', '2021-06-08 23:54:08'),
(18, 1, 'billing', '11111', NULL, 'Bahrain', NULL, NULL, '2021-06-11 08:12:23', '2021-06-11 08:12:23'),
(19, 1, 'billing', '1234', NULL, 'Bahrain', NULL, NULL, '2021-06-12 14:48:40', '2021-06-12 14:48:40'),
(20, 1, 'billing', 'Aaa', NULL, 'Bahrain', NULL, NULL, '2021-06-13 04:16:03', '2021-06-13 04:16:03'),
(21, 1, 'billing', '1111114', NULL, 'Bahrain', NULL, NULL, '2021-06-13 04:18:36', '2021-06-13 04:18:36'),
(22, 1, 'billing', 'BBBBB', NULL, 'Bahrain', NULL, NULL, '2021-06-13 04:21:19', '2021-06-13 04:21:19'),
(23, 1, 'billing', 'BBBBB', NULL, 'Bahrain', NULL, NULL, '2021-06-13 04:24:37', '2021-06-13 04:24:37'),
(24, 1, 'billing', 'BBBBB', NULL, 'Bahrain', NULL, NULL, '2021-06-13 04:24:47', '2021-06-13 04:24:47'),
(25, 1, 'billing', '134', NULL, 'Bahrain', NULL, NULL, '2021-06-13 04:26:03', '2021-06-13 04:26:03'),
(26, 1, 'billing', '1111111111111111111', NULL, 'Bahrain', NULL, NULL, '2021-06-13 05:24:57', '2021-06-13 05:24:57'),
(27, 1, 'billing', 'aaaa', NULL, 'Bahrain', NULL, NULL, '2021-06-13 05:28:21', '2021-06-13 05:28:21'),
(28, 1, 'billing', 'AAAAA', '', 'Bahrain', '', '', '2021-06-13 14:04:52', '2021-06-13 14:04:52');

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

--
-- Dumping data for table `dbproj_user_car_reservation`
--

INSERT INTO `dbproj_user_car_reservation` (`user_car_reservation_id`, `user_id`, `car_id`, `reservation_code`, `pickup_date`, `return_date`, `status`, `is_amended`, `sales_invoice_id`, `created_at`, `updated_at`) VALUES
(1, 1, 220, '123', '2021-05-23', '2021-05-26', 'confirmed', 0, 14, '2021-05-19 20:15:34', '2021-05-19 20:15:34'),
(2, 1, 517, '1234', '2021-05-21', '2021-05-24', 'confirmed', 0, 14, '2021-05-19 20:15:35', '2021-05-19 20:15:35'),
(3, 1, 517, '12345', '2021-05-28', '2021-05-30', 'confirmed', 0, 14, '2021-05-19 20:15:36', '2021-05-19 20:15:36'),
(4, 1, 694, '123456', '2021-05-20', '2021-05-30', 'confirmed', 0, 14, '2021-05-19 20:15:37', '2021-05-19 20:15:37'),
(5, 1, 860, '1234567', '2021-05-20', '2021-05-22', 'confirmed', 0, 14, '2021-05-19 20:15:38', '2021-05-19 20:15:38'),
(6, 1, 860, '12345689', '2021-05-28', '2021-05-28', 'confirmed', 0, 14, '2021-05-19 20:15:45', '2021-05-19 20:15:45'),
(7, 1, 925, '98765', '2021-05-20', '2021-05-21', 'confirmed', 0, 14, '2021-05-19 20:37:34', '2021-05-19 20:37:34'),
(9, 1, 817, '987654', '2021-05-26', '2021-05-28', 'unconfirmed', 0, 14, '2021-05-27 15:02:48', '2021-05-27 15:02:48'),
(10, 1, 533, '9876543', '2021-05-28', '2021-05-28', 'unconfirmed', 0, 15, '2021-05-28 12:52:21', '2021-05-28 12:52:21'),
(11, 1, 817, '98765432', '2021-06-05', '2021-06-05', 'unconfirmed', 0, 16, '2021-05-31 00:33:39', '2021-05-31 00:33:39'),
(12, 1, 817, '98765321', '2021-06-09', '2021-07-10', 'cancelled', 0, 17, '2021-06-07 03:10:58', '2021-06-07 03:10:58'),
(13, 1, 152, '159', '2021-07-01', '2021-07-01', 'unconfirmed', 1, 18, '2021-06-08 02:01:18', '2021-06-08 02:01:18'),
(14, 1, 817, '35755856', '2021-06-11', '2021-06-12', 'confirmed', 1, 19, '2021-06-08 22:01:26', '2021-06-08 22:01:26'),
(15, 1, 189, '27918841', '2021-06-11', '2021-06-16', 'confirmed', 0, 20, '2021-06-11 08:11:44', '2021-06-11 08:11:44'),
(16, 1, 188, '63902707', '2021-06-15', '2021-06-15', 'confirmed', 0, 21, '2021-06-12 14:48:26', '2021-06-12 18:41:53'),
(17, 1, 188, '2485634', '2021-06-20', '2021-06-22', 'confirmed', 0, 14, '2021-06-12 18:42:17', '2021-06-12 18:42:26'),
(18, 1, 4, '60', '2021-06-13', '2021-06-13', 'confirmed', 0, 22, '2021-06-13 04:15:43', '2021-06-13 04:15:43'),
(22, 1, 80, '60c55e8bafc83', '2021-06-19', '2021-06-26', 'confirmed', 1, 26, '2021-06-13 04:25:31', '2021-06-13 08:09:30'),
(23, 1, 75, '60c569cd396c9', '0000-00-00', '0000-00-00', 'confirmed', 1, 27, '2021-06-13 05:13:33', '2021-06-13 11:40:10'),
(24, 1, 80, '60c5aeec2c649', '2021-07-15', '2021-07-20', 'unconfirmed', 1, 28, '2021-06-13 10:08:28', '2021-06-13 10:08:43'),
(25, 1, 75, '60c5e62c4e560', '2021-06-16', '2021-06-16', 'confirmed', 0, 29, '2021-06-13 14:04:12', '2021-06-13 14:06:39');

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
  MODIFY `car_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=999;

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
  MODIFY `sales_invoice_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `dbproj_sales_invoice_item`
--
ALTER TABLE `dbproj_sales_invoice_item`
  MODIFY `sales_invoice_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `dbproj_transaction`
--
ALTER TABLE `dbproj_transaction`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `dbproj_user`
--
ALTER TABLE `dbproj_user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dbproj_user_address`
--
ALTER TABLE `dbproj_user_address`
  MODIFY `user_address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `dbproj_user_car_reservation`
--
ALTER TABLE `dbproj_user_car_reservation`
  MODIFY `user_car_reservation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
