-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 04:30 PM
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
-- Database: `digital_lost_found`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `approved_items_view`
-- (See below for the actual view)
--
CREATE TABLE `approved_items_view` (
`id` int(11)
,`item_name` varchar(150)
,`description` text
,`photo` varchar(255)
,`reporter_name` varchar(100)
,`item_type` enum('lost','found')
,`date_reported` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `claimant_name` varchar(255) NOT NULL,
  `claimant_email` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `date_requested` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `item_id`, `claimant_name`, `claimant_email`, `status`, `date_requested`) VALUES
(1, 1, 'bob', 'bob@gmail.com', 'approved', '2025-10-14 20:50:19');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user`, `rating`, `comment`, `date_submitted`) VALUES
(1, 'bob', 5, 'thank you! my phone is found.', '2025-10-14 10:12:46');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `reporter_name` varchar(100) NOT NULL,
  `item_type` enum('lost','found') NOT NULL,
  `status` enum('pending','approved','rejected','claimed') DEFAULT 'pending',
  `claimed_by` varchar(100) DEFAULT NULL,
  `date_reported` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `description`, `photo`, `reporter_name`, `item_type`, `status`, `claimed_by`, `date_reported`) VALUES
(1, 'Black Wallet', 'Contains ID and some cash', '1760441735_black wallet.jpg', 'Karl', 'found', 'approved', 'bob', '2025-10-14 06:29:47'),
(3, 'Smartphone', 'iPhone 11', 'ip11.jpg', 'nicko', 'lost', 'approved', NULL, '2025-10-14 07:39:12'),
(4, 'SmartPhone', 'iPhone 11', 'ip11.jpg', 'bob', 'found', 'approved', NULL, '2025-10-14 07:41:28'),
(5, 'watch', 'smart watch 10', '1760443349_smart watch.jpg', 'eros', 'lost', 'pending', NULL, '2025-10-14 12:02:29');

--
-- Triggers `items`
--
DELIMITER $$
CREATE TRIGGER `after_item_approved` AFTER UPDATE ON `items` FOR EACH ROW BEGIN
    IF NEW.status = 'approved' THEN
        INSERT INTO notifications (recipient_email, subject, message, status)
        VALUES (
            (SELECT email FROM users WHERE name = NEW.reporter_name LIMIT 1),
            CONCAT('Your ', NEW.item_type, ' item report has been approved'),
            CONCAT('Hi ', NEW.reporter_name, ', your report for "', NEW.item_name, '" has been approved by admin.'),
            'pending'
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `recipient_email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('sent','pending') DEFAULT 'pending',
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `recipient_email`, `subject`, `message`, `status`, `date_sent`) VALUES
(1, 'bob@gmail.com', 'Your found item report has been approved', 'Hi bob, your report for \"SmartPhone\" has been approved by admin.', 'pending', '2025-10-14 07:42:04'),
(2, 'nicko@gmail.com', 'Your lost item report has been approved', 'Hi nicko, your report for \"Smartphone\" has been approved by admin.', 'pending', '2025-10-14 07:42:07'),
(3, 'aira@gmail.com', 'Your lost item report has been approved', 'Hi Aira, your report for \"School Bag\" has been approved by admin.', 'pending', '2025-10-14 07:42:11'),
(4, 'karl@gmail.com', 'Your found item report has been approved', 'Hi Karl, your report for \"Black Wallet\" has been approved by admin.', 'pending', '2025-10-14 07:42:13'),
(5, 'bob@gmail.com', 'Your found item report has been approved', 'Hi bob, your report for \"SmartPhone\" has been approved by admin.', 'pending', '2025-10-14 10:10:25'),
(6, 'karl@gmail.com', 'Your found item report has been approved', 'Hi Karl, your report for \"Black Wallet\" has been approved by admin.', 'pending', '2025-10-14 11:35:35'),
(7, 'karl@gmail.com', 'Your found item report has been approved', 'Hi Karl, your report for \"Black Wallet\" has been approved by admin.', 'pending', '2025-10-14 13:39:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','finder','owner') NOT NULL DEFAULT 'owner',
  `status` enum('pending','active') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`, `reset_token`, `reset_expiry`) VALUES
(1, 'Admin', 'admin@gmail.com', '0192023a7bbd73250516f069df18b500', 'admin', 'active', '2025-10-14 06:29:47', NULL, NULL),
(2, 'Karl', 'karl@gmail.com', 'ee6445d1bcce11bbb1b00098c6e660ed', 'finder', 'active', '2025-10-14 06:29:47', NULL, NULL),
(4, 'nicko', 'nicko@gmail.com', '7399377d1fc44304ff74e251e2c94dac', 'finder', 'active', '2025-10-14 07:32:23', NULL, NULL),
(5, 'bob', 'bob@gmail.com', '2acba7f51acfd4fd5102ad090fc612ee', 'owner', 'active', '2025-10-14 07:40:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure for view `approved_items_view`
--
DROP TABLE IF EXISTS `approved_items_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `approved_items_view`  AS SELECT `items`.`id` AS `id`, `items`.`item_name` AS `item_name`, `items`.`description` AS `description`, `items`.`photo` AS `photo`, `items`.`reporter_name` AS `reporter_name`, `items`.`item_type` AS `item_type`, `items`.`date_reported` AS `date_reported` FROM `items` WHERE `items`.`status` = 'approved' ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
