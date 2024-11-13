-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2024 at 01:06 PM
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
-- Database: `morya`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payment`
--

CREATE TABLE `tbl_payment` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `payment_date` datetime NOT NULL,
  `txnid` varchar(255) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `bank_transaction_info` text NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `payment_status` varchar(25) NOT NULL DEFAULT 'pending',
  `shipping_status` enum('pending','shipped','delivered') NOT NULL DEFAULT 'pending',
  `payment_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `p_id` int(11) NOT NULL,
  `p_name` varchar(255) NOT NULL,
  `p_slug` varchar(255) NOT NULL,
  `p_old_price` decimal(10,2) NOT NULL,
  `p_current_price` decimal(10,2) NOT NULL,
  `p_qty` int(10) NOT NULL DEFAULT 0,
  `p_featured_photo` varchar(255) NOT NULL,
  `p_description` text NOT NULL,
  `p_short_description` text NOT NULL,
  `p_total_view` bigint(20) NOT NULL DEFAULT 0,
  `p_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `tcat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`p_id`, `p_name`, `p_slug`, `p_old_price`, `p_current_price`, `p_qty`, `p_featured_photo`, `p_description`, `p_short_description`, `p_total_view`, `p_is_active`, `tcat_id`) VALUES
(1, 'Plain Partition Corrugated Box', 'plain-partition-corrugated-box.php', 100.00, 90.00, 10, 'plain-partition-corrugated-box.jpg', 'Description here', 'Short description here', 0, 1, 1),
(2, 'Corrugated Partition Box Rectangle', 'corrugated-partition-box-rectangle.php', 120.00, 110.00, 15, 'corrugated-partition-box-rectangle.jpg', 'Description here', 'Short description here', 0, 1, 1),
(3, 'Corrugated Partition Box Square', 'corrugated-partition-box-square.php', 120.00, 110.00, 15, 'corrugated-partition-box-square.jpg', 'Description here', 'Short description here', 0, 1, 1),
(4, 'Quarantine Bed', 'quarantine-bed.php', 200.00, 180.00, 5, 'quarantine-bed.jpg', 'Description here', 'Short description here', 0, 1, 2),
(5, 'Corrugated Punched Box', 'corrugated-punched-box.php', 150.00, 130.00, 20, 'corrugated-punched-box.jpg', 'Description here', 'Short description here', 0, 1, 2),
(6, 'Duplex Corrugated Box', 'duplex-corrugated-box.php', 160.00, 140.00, 10, 'duplex-corrugated-box.jpg', 'Description here', 'Short description here', 0, 1, 2),
(7, 'Plain Corrugated Boxes', 'plain-corrugated-boxes.php', 140.00, 120.00, 12, 'plain-corrugated-boxes.jpg', 'Description here', 'Short description here', 0, 1, 2),
(8, 'Flat Corrugated Boxes', 'flat-corrugated-boxes.php', 130.00, 115.00, 10, 'flat-corrugated-boxes.jpg', 'Description here', 'Short description here', 0, 1, 2),
(9, 'Fabric Corrugated Boxes', 'fabric-corrugated-boxes.php', 160.00, 145.00, 8, 'fabric-corrugated-boxes.jpg', 'Description here', 'Short description here', 0, 1, 2),
(10, 'Corrugated Carton Boxes', 'corrugated-carton-boxes.php', 170.00, 155.00, 6, 'corrugated-carton-boxes.jpg', 'Description here', 'Short description here', 0, 1, 2),
(11, 'Corrugated Box Partitions', 'corrugated-box-partitions.php', 130.00, 120.00, 20, 'corrugated-box-partitions.jpg', 'Description here', 'Short description here', 0, 1, 3),
(12, 'Plywood Boxes', 'plywood-boxes.php', 180.00, 170.00, 7, 'plywood-boxes.jpg', 'Description here', 'Short description here', 0, 1, 3),
(13, 'Wooden Packaging Boxes', 'wooden-packaging-boxes.php', 200.00, 180.00, 5, 'wooden-packaging-boxes.jpg', 'Description here', 'Short description here', 0, 1, 3),
(14, 'Industrial Wooden Boxes', 'industrial-wooden-boxes.php', 220.00, 210.00, 3, 'industrial-wooden-boxes.jpg', 'Description here', 'Short description here', 0, 1, 3),
(15, 'Heavy Duty Wooden Box', 'heavy-duty-wooden-box.php', 250.00, 230.00, 2, 'heavy-duty-wooden-box.jpg', 'Description here', 'Short description here', 0, 1, 3),
(16, 'Heavy Duty Wooden Packaging Box', 'heavy-duty-wooden-packaging-box.php', 240.00, 220.00, 4, 'heavy-duty-wooden-packaging-box.jpg', 'Description here', 'Short description here', 0, 1, 3),
(17, 'Plywood Pallets', 'plywood-pallets.php', 190.00, 175.00, 8, 'plywood-pallets.jpg', 'Description here', 'Short description here', 0, 1, 3),
(18, 'PineWood Pallets', 'pinewood-pallets.php', 180.00, 165.00, 10, 'pinewood-pallets.jpg', 'Description here', 'Short description here', 0, 1, 3),
(19, 'Corrugated Pallet Box', 'corrugated-pallet-box.php', 200.00, 185.00, 5, 'corrugated-pallet-box.jpg', 'Description here', 'Short description here', 0, 1, 3),
(20, 'Industrial Wooden Pallets', 'industrial-wooden-pallets.php', 210.00, 195.00, 4, 'industrial-wooden-pallets.jpg', 'Description here', 'Short description here', 0, 1, 3),
(21, 'Corrugated Packaging Boxes', 'corrugated-packaging-boxes.php', 150.00, 140.00, 15, 'corrugated-packaging-boxes.jpg', 'Description here', 'Short description here', 0, 1, 4),
(22, 'Corrugated Packaging Carton Box', 'corrugated-packaging-carton-box.php', 155.00, 145.00, 12, 'corrugated-packaging-carton-box.jpg', 'Description here', 'Short description here', 0, 1, 4),
(23, 'Laminated Packaging Box', 'laminated-packaging-box.php', 160.00, 150.00, 9, 'laminated-packaging-box.jpg', 'Description here', 'Short description here', 0, 1, 4),
(24, 'Handle Corrugated Box', 'handle-corrugated-box.php', 120.00, 110.00, 20, 'handle-corrugated-box.jpg', 'Description here', 'Short description here', 0, 1, 5),
(25, 'Handled Plain Corrugated Box', 'handled-plain-corrugated-box.php', 130.00, 115.00, 15, 'handled-plain-corrugated-box.jpg', 'Description here', 'Short description here', 0, 1, 5),
(26, 'Plastic Handle Corrugated Box', 'plastic-handle-corrugated-box.php', 135.00, 120.00, 18, 'plastic-handle-corrugated-box.jpg', 'Description here', 'Short description here', 0, 1, 5),
(27, 'Corrugated Packaging Sheet', 'corrugated-packaging-sheet.php', 100.00, 90.00, 25, 'corrugated-packaging-sheet.jpg', 'Description here', 'Short description here', 0, 1, 6),
(28, 'Heavy Duty Corrugated Box', 'heavy-duty-corrugated-box.php', 180.00, 170.00, 7, 'heavy-duty-corrugated-box.jpg', 'Description here', 'Short description here', 0, 1, 6),
(29, 'Heavy Duty Corrugated Packaging Box', 'heavy-duty-corrugated-packaging-box.php', 185.00, 175.00, 6, 'heavy-duty-corrugated-packaging-box.jpg', 'Description here', 'Short description here', 0, 1, 6),
(30, 'Laminated Duplex Box', 'laminated-duplex-box.php', 200.00, 190.00, 5, 'laminated-duplex-box.jpg', 'Description here', 'Short description here', 0, 1, 7),
(31, 'Heavy Duty Corrugated Pallet Box', 'heavy-duty-corrugated-pallet-box.php', 220.00, 200.00, 4, 'heavy-duty-corrugated-pallet-box.jpg', 'Description here', 'Short description here', 0, 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `favicon` varchar(255) NOT NULL,
  `contact_address` text NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `logo`, `favicon`, `contact_address`, `contact_email`, `contact_phone`) VALUES
(1, 'logo.png', 'favicon.png', 'France Cluster Q06\r\nDubai, United Arab Emirates', 'support@fashionys.com', '+971 50 202 0067');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_social`
--

CREATE TABLE `tbl_social` (
  `social_id` int(11) NOT NULL,
  `social_name` varchar(50) NOT NULL,
  `social_url` varchar(255) NOT NULL,
  `social_icon` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_social`
--

INSERT INTO `tbl_social` (`social_id`, `social_name`, `social_url`, `social_icon`) VALUES
(1, 'Facebook', 'https://www.facebook.com/', 'an an-facebook'),
(2, 'Twitter', 'https://www.twitter.com/', 'an an-twitter'),
(3, 'LinkedIn', '', 'an an-linkedin'),
(5, 'Pinterest', '', 'an an-pinterest-p'),
(6, 'YouTube', 'https://youtube.com/', 'an an-youtube'),
(7, 'Instagram', '', 'an an-instagram'),
(8, 'Tumblr', '', 'an an-tumblr'),
(11, 'Snapchat', '', 'an an-snapchat'),
(12, 'WhatsApp', '', 'an an-whatsapp'),
(15, 'Delicious', '', 'an an-delicious'),
(16, 'Digg', '', 'an an-digg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_top_category`
--

CREATE TABLE `tbl_top_category` (
  `tcat_id` int(11) NOT NULL,
  `tcat_name` varchar(255) NOT NULL,
  `show_on_menu` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_top_category`
--

INSERT INTO `tbl_top_category` (`tcat_id`, `tcat_name`, `show_on_menu`) VALUES
(1, 'Partitions Corrugated Boxes', 1),
(2, 'Corrugated Boxes', 1),
(3, 'Corrugated Dividers', 1),
(4, 'Wooden Boxes', 1),
(5, 'Partitions Corrugated Boxes', 1),
(6, 'Corrugated Boxes', 1),
(7, 'Corrugated Dividers', 1),
(8, 'Wooden Boxes', 1),
(9, 'Wooden Pallets', 1),
(10, 'Packaging Box', 1),
(11, 'Handle Corrugated Boxes', 1),
(12, 'Corrugated Sheets', 1),
(13, 'Heavy Duty Corrugated Boxes', 1),
(14, 'Laminated Duplex Box', 1),
(15, 'Heavy Duty Corrugated Pallet Box', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_payment`
--
ALTER TABLE `tbl_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_payment_method` (`payment_method`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `idx_tcat_id` (`tcat_id`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_contact_email` (`contact_email`);

--
-- Indexes for table `tbl_social`
--
ALTER TABLE `tbl_social`
  ADD PRIMARY KEY (`social_id`),
  ADD KEY `idx_social_name` (`social_name`);

--
-- Indexes for table `tbl_top_category`
--
ALTER TABLE `tbl_top_category`
  ADD PRIMARY KEY (`tcat_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_payment`
--
ALTER TABLE `tbl_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_social`
--
ALTER TABLE `tbl_social`
  MODIFY `social_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_top_category`
--
ALTER TABLE `tbl_top_category`
  MODIFY `tcat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD CONSTRAINT `fk_tcat_id` FOREIGN KEY (`tcat_id`) REFERENCES `tbl_top_category` (`tcat_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
