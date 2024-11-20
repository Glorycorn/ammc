-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2024 at 06:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental_accom1`
--

-- --------------------------------------------------------

--
-- Table structure for table `accomodation_tbl`
--

CREATE TABLE `accomodation_tbl` (
  `accomodation_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `recommended` tinyint(1) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accomodation_tbl`
--

INSERT INTO `accomodation_tbl` (`accomodation_id`, `category_id`, `image`, `description`, `address`, `owner_id`, `price`, `status`, `comment_id`, `recommended`, `image_path`) VALUES
(1, NULL, NULL, 'nindot ni', 'dri samoa', 1, 104.00, 'available', NULL, 0, 'uploads/ownlogo.PNG'),
(2, NULL, NULL, 'dri na', 'balay namo', 1, 100.00, 'available', NULL, 0, 'uploads/464905356_964293762404916_3845292271289216028_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `admin_tbl`
--

CREATE TABLE `admin_tbl` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_tbl`
--

INSERT INTO `admin_tbl` (`admin_id`, `email`, `password`) VALUES
(2, 'admin@example.com', '$2y$10$........');

-- --------------------------------------------------------

--
-- Table structure for table `category_tbl`
--

CREATE TABLE `category_tbl` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments_tbl`
--

CREATE TABLE `comments_tbl` (
  `comment_id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `owners_tbl`
--

CREATE TABLE `owners_tbl` (
  `owner_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `contactnum` varchar(15) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owners_tbl`
--

INSERT INTO `owners_tbl` (`owner_id`, `email`, `password`, `contactnum`, `lastname`, `firstname`, `middlename`) VALUES
(1, 'ikawatako@gmail.com', '$2y$10$xsfL8p7ZA.8ji6ygmLq9ZOlOKUI4VBaIOI9K0vMVcZxAefqe49t0u', '09172771714', 'piedad', 'justine', 'OEIW');

-- --------------------------------------------------------

--
-- Table structure for table `payments_tbl`
--

CREATE TABLE `payments_tbl` (
  `payment_id` int(11) NOT NULL,
  `code_numb` varchar(50) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation_tbl`
--

CREATE TABLE `reservation_tbl` (
  `reservation_id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `accomodation_id` int(11) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants_tbl`
--

CREATE TABLE `tenants_tbl` (
  `tenant_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `contactnum` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants_tbl`
--

INSERT INTO `tenants_tbl` (`tenant_id`, `email`, `password`, `lastname`, `firstname`, `middlename`, `contactnum`) VALUES
(1, '$email', '$password', '$lastname', '$firstname', '$middlename', '$contactnum'),
(2, 'justinpiedad231@gmail.com', '$2y$10$ov/JZIF.Fq9NXMlvxW04OuqUNMoXRh6W2BheOOxVMEJhqBOX6tlXW', 'Piedad', 'Justine', 'Q', '09172771714'),
(3, 'akodot@gmail.com', '$2y$10$dv6FPbsspvMiXnADNRq1guZqYPF6Ddr9lmiAQRN9PJlwO6YLifemy', 'Ako', 'Ni', 'Q', '0981217712'),
(4, 'owner@gmail.com', '$2y$10$MOPLh9l40k7S0hahyTm9X.l7ZcBBa8T70iwZnwImM/PHtyU6zS1QO', 'ME', 'you', 'hi', '0972124131');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accomodation_tbl`
--
ALTER TABLE `accomodation_tbl`
  ADD PRIMARY KEY (`accomodation_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Indexes for table `admin_tbl`
--
ALTER TABLE `admin_tbl`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `category_tbl`
--
ALTER TABLE `category_tbl`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `comments_tbl`
--
ALTER TABLE `comments_tbl`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `owners_tbl`
--
ALTER TABLE `owners_tbl`
  ADD PRIMARY KEY (`owner_id`);

--
-- Indexes for table `payments_tbl`
--
ALTER TABLE `payments_tbl`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservation_tbl`
--
ALTER TABLE `reservation_tbl`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `accomodation_id` (`accomodation_id`);

--
-- Indexes for table `tenants_tbl`
--
ALTER TABLE `tenants_tbl`
  ADD PRIMARY KEY (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accomodation_tbl`
--
ALTER TABLE `accomodation_tbl`
  MODIFY `accomodation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_tbl`
--
ALTER TABLE `admin_tbl`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category_tbl`
--
ALTER TABLE `category_tbl`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments_tbl`
--
ALTER TABLE `comments_tbl`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `owners_tbl`
--
ALTER TABLE `owners_tbl`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments_tbl`
--
ALTER TABLE `payments_tbl`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservation_tbl`
--
ALTER TABLE `reservation_tbl`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants_tbl`
--
ALTER TABLE `tenants_tbl`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accomodation_tbl`
--
ALTER TABLE `accomodation_tbl`
  ADD CONSTRAINT `accomodation_tbl_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category_tbl` (`category_id`),
  ADD CONSTRAINT `accomodation_tbl_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `owners_tbl` (`owner_id`),
  ADD CONSTRAINT `accomodation_tbl_ibfk_3` FOREIGN KEY (`comment_id`) REFERENCES `comments_tbl` (`comment_id`);

--
-- Constraints for table `comments_tbl`
--
ALTER TABLE `comments_tbl`
  ADD CONSTRAINT `comments_tbl_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants_tbl` (`tenant_id`);

--
-- Constraints for table `payments_tbl`
--
ALTER TABLE `payments_tbl`
  ADD CONSTRAINT `payments_tbl_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservation_tbl` (`reservation_id`);

--
-- Constraints for table `reservation_tbl`
--
ALTER TABLE `reservation_tbl`
  ADD CONSTRAINT `reservation_tbl_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants_tbl` (`tenant_id`),
  ADD CONSTRAINT `reservation_tbl_ibfk_2` FOREIGN KEY (`accomodation_id`) REFERENCES `accomodation_tbl` (`accomodation_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
