-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql100.byetcluster.com
-- Generation Time: Nov 17, 2025 at 11:38 PM
-- Server version: 10.6.22-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40167632_digital_lost_found`
--

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `claimant_name` varchar(255) NOT NULL,
  `claimant_email` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `date_requested` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `item_id`, `claimant_name`, `claimant_email`, `status`, `date_requested`) VALUES
(8, 26, 'nicko', 'nicko@gmail.com', 'pending', '2025-11-16 19:39:22');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `rating` int(11) DEFAULT NULL
) ;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user`, `rating`, `comment`, `date_submitted`) VALUES
(1, 'bob', 5, 'thank you! my phone is found.', '2025-10-14 17:12:46'),
(2, 'nicko', 5, 'Fast Upload of the lost Item and Fast Approvals of the Admin. ', '2025-10-18 00:53:13'),
(3, 'K', 5, 'Niggrreeee', '2025-10-20 02:37:15'),
(4, 'bania', 1, 'hays, tagal....', '2025-10-20 02:43:19'),
(5, 'andrei vibar', 3, 'mid asf', '2025-10-22 03:03:40'),
(6, 'bob', 4, 'stfu', '2025-10-27 03:14:34');

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
  `reporter_email` varchar(100) DEFAULT NULL,
  `item_type` enum('lost','found') NOT NULL,
  `status` enum('pending','approved','rejected','claimed') DEFAULT 'pending',
  `claimed_by` varchar(100) DEFAULT NULL,
  `date_reported` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `description`, `photo`, `reporter_name`, `reporter_email`, `item_type`, `status`, `claimed_by`, `date_reported`) VALUES
(15, 'Infinix 30i', 'Smartphone', '1000002250.jpg', 'nicko', 'nicko@gmail.com', 'lost', 'approved', NULL, '2025-10-18 00:49:49'),
(16, 'Helment', 'Things', '1000002245.jpg', 'nicko', 'nicko@gmail.com', 'lost', 'approved', NULL, '2025-10-18 00:50:52'),
(17, 'Citizen Watch', 'Things', '1000001287.jpg', 'nicko', 'nicko@gmail.com', 'lost', 'approved', NULL, '2025-10-18 00:52:05'),
(26, 'sad', 'asdas', 'umbrella.png', 'bob', 'bob@gmail.com', 'lost', '', NULL, '2025-10-27 03:25:18');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_email` varchar(100) NOT NULL,
  `receiver_email` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_sent` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_email`, `receiver_email`, `message`, `image`, `date_sent`) VALUES
(1, 'bob@gmail.com', 'admin@example.com', 'HELLO', NULL, '2025-11-02 18:23:47'),
(2, 'bob@gmail.com', 'admin@example.com', 'HI', NULL, '2025-11-02 18:27:27'),
(3, 'bob@gmail.com', 'admin@example.com', 'A', NULL, '2025-11-02 18:32:44'),
(4, 'bob@gmail.com', 'admin@gmail.com', 'A', NULL, '2025-11-02 18:37:09'),
(5, 'admin@gmail.com', 'bob@gmail.com', 'A', NULL, '2025-11-02 18:37:16'),
(6, 'admin@gmail.com', 'bob@gmail.com', 'B', NULL, '2025-11-02 18:37:27'),
(7, 'bob@gmail.com', 'admin@gmail.com', 'B', NULL, '2025-11-02 18:37:33'),
(8, 'admin@gmail.com', 'bob@gmail.com', '', 'uploads/messages/1762137574_Screenshot 2025-09-20 183710.png', '2025-11-02 18:39:34'),
(9, 'admin@gmail.com', 'bob@gmail.com', 'what an i can help u', NULL, '2025-11-02 18:46:16'),
(10, 'admin@gmail.com', 'bob@gmail.com', 'can*', NULL, '2025-11-02 18:46:27'),
(11, 'bob@gmail.com', 'admin@gmail.com', 'CAN I CAN CLAIM THE LOST ITEM SMARTPHONE', NULL, '2025-11-02 19:21:11'),
(12, 'admin@gmail.com', 'bob@gmail.com', 'YES', NULL, '2025-11-02 19:21:39'),
(13, 'bob@gmail.com', 'admin@gmail.com', 'hello', NULL, '2025-11-04 18:45:34'),
(14, 'bob@gmail.com', 'admin@gmail.com', 'hi', NULL, '2025-11-04 18:45:53'),
(15, 'admin@gmail.com', 'nicko@gmail.com', 'Good evening mr.nicko!', NULL, '2025-11-07 06:00:37'),
(16, 'nicko@gmail.com', 'admin@gmail.com', 'Good eve too!', NULL, '2025-11-07 06:01:37'),
(17, 'admin@gmail.com', 'nicko@gmail.com', 'Can u send your id?', NULL, '2025-11-07 06:14:59'),
(18, 'nicko@gmail.com', 'admin@gmail.com', 'yes sir', NULL, '2025-11-07 06:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `recipient_email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `recipient_email`, `subject`, `message`, `status`, `date_sent`) VALUES
(1, 'bob@gmail.com', 'Your found item report has been approved', 'Hi bob, your report for \"SmartPhone\" has been approved by admin.', 'read', '2025-10-14 14:42:04'),
(2, 'nicko@gmail.com', 'Your lost item report has been approved', 'Hi nicko, your report for \"Smartphone\" has been approved by admin.', '', '2025-10-14 14:42:07'),
(3, 'aira@gmail.com', 'Your lost item report has been approved', 'Hi Aira, your report for \"School Bag\" has been approved by admin.', '', '2025-10-14 14:42:11'),
(4, 'karl@gmail.com', 'Your found item report has been approved', 'Hi Karl, your report for \"Black Wallet\" has been approved by admin.', '', '2025-10-14 14:42:13'),
(5, 'bob@gmail.com', 'Your found item report has been approved', 'Hi bob, your report for \"SmartPhone\" has been approved by admin.', 'read', '2025-10-14 17:10:25'),
(6, 'karl@gmail.com', 'Your found item report has been approved', 'Hi Karl, your report for \"Black Wallet\" has been approved by admin.', '', '2025-10-14 18:35:35'),
(7, 'karl@gmail.com', 'Your found item report has been approved', 'Hi Karl, your report for \"Black Wallet\" has been approved by admin.', '', '2025-10-14 20:39:10'),
(8, 'bob@gmail.com', 'Your lost item report has been approved', 'Hi bob, your report for \"MOUSE\" has been approved by admin.', 'read', '2025-10-17 19:35:03'),
(9, 'nicko@gmail.com', 'Item Approved', 'Hello nicko, your reported item \"Citizen Watch\" has been approved by the admin.', '', '2025-10-18 00:54:57'),
(10, 'nicko@gmail.com', 'Item Approved', 'Hello nicko, your reported item \"Helment\" has been approved by the admin.', '', '2025-10-18 00:55:02'),
(11, 'bania@gmail.com', 'Item Approved', 'Hello Bania, your reported item \"Doggy\" has been approved by the admin.', '', '2025-10-18 03:23:30'),
(12, 'bania@gmail.com', 'Item Approved', 'Hello Bania, your reported item \"Barbie\" has been approved by the admin.', '', '2025-10-18 03:23:34'),
(13, 'nicko@gmail.com', 'Item Approved', 'Hello nicko, your reported item \"Infinix 30i\" has been approved by the admin.', 'read', '2025-10-18 03:24:19'),
(14, 'bania@gmail.com', 'Item Approved', 'Hello Bania, your reported item \"tank\" has been approved by the admin.', '', '2025-10-20 02:29:20'),
(15, 'baniaa@gmail.com', 'Claim Approved', 'Hello bania, your claim for the item \'tank\' has been approved by the admin.', '', '2025-10-20 02:32:20'),
(16, 'andrei@gmail.com', 'Item Approved', 'Hello andrei vibar, your reported item \"Dealdough\" has been approved by the admin.', '', '2025-10-22 03:02:12'),
(17, 'andrei@gmail.com', 'Item Approved', 'Hello andrei vibar, your reported item \"Dealdough\" has been approved by the admin.', '', '2025-10-22 03:09:30'),
(18, 'bania@gmail.com', 'Item Approved', 'Hello Bania, your reported item \"mouse\" has been approved by the admin.', '', '2025-10-22 03:13:22'),
(19, 'andreivibar@gmail.com', 'Claim Approved', 'Hello andrei vibar, your claim for the item \'mouse\' has been approved by the admin.', '', '2025-10-22 03:14:40'),
(20, 'bania@gmail.com', 'Item Approved', 'Hello Bania, your reported item \"house and lot\" has been approved by the admin.', '', '2025-10-22 03:23:28'),
(21, 'andreivibar@gmail.com', 'Claim Approved', 'Hello andrei vibar, your claim for the item \'house and lot\' has been approved by the admin.', '', '2025-10-22 03:24:19'),
(22, 'bob@gmail.com', 'Item Approved', 'Hello bob, your reported item \"sad\" has been approved by the admin.', 'read', '2025-10-27 03:26:14');

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
(1, 'Admin', 'admin@gmail.com', '4acb4bc224acbbe3c2bfdcaa39a4324e', 'admin', 'active', '2025-10-14 13:29:47', NULL, NULL),
(2, 'Karl', 'karl@gmail.com', 'ee6445d1bcce11bbb1b00098c6e660ed', 'finder', 'active', '2025-10-14 13:29:47', NULL, NULL),
(4, 'nicko', 'nicko@gmail.com', '7399377d1fc44304ff74e251e2c94dac', 'finder', 'active', '2025-10-14 14:32:23', NULL, NULL),
(5, 'bob', 'bob@gmail.com', '2acba7f51acfd4fd5102ad090fc612ee', 'owner', 'active', '2025-10-14 14:40:02', NULL, NULL),
(6, 'karlnicko', 'karlnicko@gmail.com', '4d1e96dcb08de4f187d9f1571d187fe4', 'owner', 'active', '2025-10-18 03:09:51', NULL, NULL),
(7, 'Bania', 'bania@gmail.com', '25d55ad283aa400af464c76d713c07ad', 'owner', 'active', '2025-10-18 03:15:54', NULL, NULL),
(8, 'bania', 'baniaa@gmail.com', 'fcea920f7412b5da7be0cf42b8c93759', 'owner', 'active', '2025-10-20 02:31:33', NULL, NULL),
(9, 'K', 'k@gmail.con', 'f868619f18a04fc4b74abbc6ae228115', 'finder', 'active', '2025-10-20 02:36:57', NULL, NULL),
(10, 'Ken', 'kennydy@gmail.com', 'e807f1fcf82d132f9bb018ca6738a19f', 'finder', 'active', '2025-10-20 03:37:09', NULL, NULL),
(11, 'andrei vibar', 'andrei@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'finder', 'active', '2025-10-22 02:59:23', NULL, NULL),
(12, 'andrei vibar', 'andreivibar@gmail.com', '0192023a7bbd73250516f069df18b500', 'owner', 'active', '2025-10-22 03:13:45', NULL, NULL),
(13, 'ken', 'ken@gmail.com', 'd6172cee82aa0cd1e610aacab528f240', 'finder', 'active', '2025-10-22 03:16:07', NULL, NULL);

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
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
