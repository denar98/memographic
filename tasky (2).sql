-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 03, 2024 at 12:05 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tasky`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `attendance_id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `shift` varchar(50) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` varchar(255) NOT NULL,
  `client_name` varchar(50) NOT NULL,
  `client_phone` varchar(50) NOT NULL,
  `client_address` varchar(100) NOT NULL,
  `client_email` varchar(50) NOT NULL,
  `client_company` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `client_name`, `client_phone`, `client_address`, `client_email`, `client_company`) VALUES
('056586f7-d91a-49c0-9e29-fa54f714d9d9', 'Rankspade', '', '', '', ''),
('530e5d07-6250-4e20-80d0-372d1b15c2ce', 'Sandaus', '', '', '', ''),
('5ccca24c-b652-415e-9e74-736d74c861a9', 'Athbi89', '', '', '', ''),
('7a8fc6fc-be49-4fe9-8e4c-0304340a94f8', 'Luyingyeo', '', '', '', ''),
('bbceea9f-6eb2-4dc8-817a-a7462572df40', 'Yadul', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` varchar(255) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `employee_phone` varchar(255) NOT NULL,
  `employee_address` varchar(255) NOT NULL,
  `employee_job` varchar(255) NOT NULL,
  `employee_sallary` int(11) NOT NULL,
  `employee_level` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_name`, `employee_phone`, `employee_address`, `employee_job`, `employee_sallary`, `employee_level`) VALUES
('0c6479fe-c079-42ea-8531-0cd9df74e7cf', 'Ade', '-', '-', '-', 0, ''),
('2c5a88d3-ff62-4a4c-9aea-b52639d1ef9b', 'Rugun', '-', '-', '-', 0, ''),
('4d235cb9-a45e-458e-8a8b-880f98e1ffe3', 'Hafizh', '-', '-', '-', 0, ''),
('6f0ad0ea-74e6-4e6f-a38b-43986b06ecbf', 'Deni', '-', '-', '-', 0, ''),
('6f5bb207-4df8-4347-9751-bb114de8f04d', 'Alan', '-', '-', '-', 0, ''),
('96dbe4d5-229b-430f-bc2b-754e858bfcf9', 'Nashir', '-', '-', '-', 0, ''),
('a274d1e4-254e-4224-8699-cb0cb0574d58', 'Anggi', '-', '-', '-', 0, ''),
('a2aa762b-14f9-4f8e-9c71-2d3a038fb0b8', 'Ihza', '-', '-', '-', 0, ''),
('b4b7c944-03cb-4059-83fe-a679313b5c41', 'Wisnu', '-', '-', '-', 0, ''),
('dd298c27-15a0-4c27-90d2-1ab979eec5d7', 'Fauzan', '-', '-', '-', 0, ''),
('e9cda8eb-42d7-4da1-8ab5-51254113e4a5', 'Bagus', '-', '-', '-', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(255) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `order_nominal` double NOT NULL,
  `order_date` date NOT NULL,
  `order_deadline` datetime NOT NULL DEFAULT current_timestamp(),
  `client_id` varchar(255) NOT NULL,
  `service_id` varchar(255) NOT NULL,
  `service_package_id` varchar(255) NOT NULL,
  `brief` text DEFAULT NULL,
  `order_status` varchar(50) NOT NULL,
  `assign_to` varchar(50) DEFAULT NULL,
  `user_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_attachments`
--

CREATE TABLE `order_attachments` (
  `order_attachment_id` varchar(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `order_attachment_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `overtimes`
--

CREATE TABLE `overtimes` (
  `overtime_id` varchar(255) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `overtime_type` varchar(50) NOT NULL,
  `overtime_time` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` varchar(255) NOT NULL,
  `task_id` varchar(255) NOT NULL,
  `rating_date` datetime NOT NULL DEFAULT current_timestamp(),
  `quality` double NOT NULL,
  `brief_reading` double NOT NULL,
  `speed` double NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` varchar(255) NOT NULL,
  `service_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`) VALUES
('7ee3a83e-1e2b-41cc-a399-30363b310c3e', 'Illustration'),
('b50d8ba7-024e-4a0e-83f4-d696ed0d108b', 'Animation'),
('f00ed606-5747-4a5f-abec-284a1fd80a50', 'Infographic');

-- --------------------------------------------------------

--
-- Table structure for table `service_packages`
--

CREATE TABLE `service_packages` (
  `service_package_id` varchar(255) NOT NULL,
  `service_id` varchar(255) NOT NULL,
  `service_package_name` varchar(50) NOT NULL,
  `service_package_price` int(11) NOT NULL,
  `service_package_detail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_packages`
--

INSERT INTO `service_packages` (`service_package_id`, `service_id`, `service_package_name`, `service_package_price`, `service_package_detail`) VALUES
('062ad3b4-c189-41c2-876f-de5dbee61e32', 'f00ed606-5747-4a5f-abec-284a1fd80a50', 'Premium', 80, ''),
('3223afa0-dc6f-4ba0-94ec-57dc7bbeca7d', '7ee3a83e-1e2b-41cc-a399-30363b310c3e', 'Basic', 10, ''),
('3d346142-011c-43b7-8f9b-639575a6f4bc', 'f00ed606-5747-4a5f-abec-284a1fd80a50', 'Standard', 40, ''),
('5aa642cf-76d9-48aa-9be6-d3597aa6a2d2', 'b50d8ba7-024e-4a0e-83f4-d696ed0d108b', 'Basic', 25, ''),
('774f9b4a-fcac-4a7a-a2bc-8cbdc9437b8d', 'b50d8ba7-024e-4a0e-83f4-d696ed0d108b', 'Standard', 60, ''),
('a0324837-79a6-415a-a51e-de45767bb2af', 'b50d8ba7-024e-4a0e-83f4-d696ed0d108b', 'Premium', 125, ''),
('af98bf77-ce60-45c1-afc5-613869398dcc', '7ee3a83e-1e2b-41cc-a399-30363b310c3e', 'Standard', 30, ''),
('e9246597-7e23-400d-90c2-dcae144effda', 'f00ed606-5747-4a5f-abec-284a1fd80a50', 'Basic', 10, ''),
('fb6cd46a-9b99-4f5a-8fc8-f10e8f26d828', '7ee3a83e-1e2b-41cc-a399-30363b310c3e', 'Premium', 60, '');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` varchar(255) NOT NULL,
  `tag_name` varchar(59) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_name`) VALUES
('0ceb3220-1ff0-4914-a3dd-5a5e73e77baa', 'Normal'),
('947a5486-ca49-4a76-9956-68cc5a2b49a7', 'Urgent');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` varchar(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `task_type` varchar(50) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `task_name` varchar(100) DEFAULT NULL,
  `task_date` date NOT NULL,
  `task_estimation_hour` int(11) NOT NULL,
  `task_estimation_minute` int(11) NOT NULL,
  `task_start` datetime NOT NULL,
  `task_finish` datetime DEFAULT NULL,
  `task_brief` text DEFAULT NULL,
  `tag_id` varchar(255) NOT NULL,
  `task_status` varchar(50) NOT NULL,
  `task_new_count` int(11) NOT NULL,
  `task_revision_count` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_attachments`
--

CREATE TABLE `task_attachments` (
  `task_attachment_id` varchar(255) NOT NULL,
  `task_id` varchar(255) NOT NULL,
  `task_attachment_type` varchar(50) NOT NULL,
  `task_attachment_name` varchar(255) NOT NULL,
  `task_attachment_ext` varchar(50) DEFAULT NULL,
  `task_attachment_width` varchar(50) DEFAULT NULL,
  `task_attachment_height` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_deliveries`
--

CREATE TABLE `task_deliveries` (
  `task_delivery_id` varchar(255) NOT NULL,
  `task_id` varchar(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `task_delivery_date` datetime NOT NULL,
  `task_delivery_type` varchar(50) NOT NULL,
  `task_delivery_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_delivery_attachments`
--

CREATE TABLE `task_delivery_attachments` (
  `task_delivery_attachments_id` varchar(255) NOT NULL,
  `task_id` varchar(255) NOT NULL,
  `task_delivery_id` varchar(255) NOT NULL,
  `task_delivery_attachment_type` varchar(255) NOT NULL,
  `task_delivery_attachment_name` varchar(255) NOT NULL,
  `task_delivery_attachment_ext` varchar(50) NOT NULL,
  `task_delivery_attachment_width` varchar(50) DEFAULT NULL,
  `task_delivery_attachment_height` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(255) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `employee_id`, `username`, `password`, `role`, `last_login`) VALUES
('0a48a87f-d7ba-455e-a149-2e5778142ab4', '2c5a88d3-ff62-4a4c-9aea-b52639d1ef9b', 'rugun', '01a5f5db2d97bd6b389e7a20bd889708', 'Admin', NULL),
('23da7f49-cf1d-400f-8845-dfbe21834e6c', 'b4b7c944-03cb-4059-83fe-a679313b5c41', 'wisnu', '01a5f5db2d97bd6b389e7a20bd889708', 'Graphic Designer', NULL),
('4016777e-7eef-4756-b1e6-2f14cb406d1e', '96dbe4d5-229b-430f-bc2b-754e858bfcf9', 'nashir', '01a5f5db2d97bd6b389e7a20bd889708', 'Graphic Designer', NULL),
('5808647a-9783-44e6-897d-d2ecfedab26b', 'dd298c27-15a0-4c27-90d2-1ab979eec5d7', 'fauzan', '01a5f5db2d97bd6b389e7a20bd889708', 'Project Manager', '2024-01-31 07:52:54'),
('97cd31bf-08be-43e9-89fe-01a91675dfa0', 'e9cda8eb-42d7-4da1-8ab5-51254113e4a5', 'bagus', '01a5f5db2d97bd6b389e7a20bd889708', 'Head Designer', NULL),
('a2eee4d2-c7c1-4e8f-b089-e8a768afc684', '4d235cb9-a45e-458e-8a8b-880f98e1ffe3', 'hafizh', '01a5f5db2d97bd6b389e7a20bd889708', 'Project Manager', NULL),
('b4d0cbd6-b2d2-4fd3-aee0-e92525d22199', 'a274d1e4-254e-4224-8699-cb0cb0574d58', 'anggi', '01a5f5db2d97bd6b389e7a20bd889708', 'Project Manager', NULL),
('b56d4c54-c44b-4dd6-bbd5-651f3c7e64a1', '6f0ad0ea-74e6-4e6f-a38b-43986b06ecbf', 'deni', '01a5f5db2d97bd6b389e7a20bd889708', 'Admin', '2024-02-03 08:19:31'),
('b9a93636-f20d-4899-b57b-514083638d20', '6f5bb207-4df8-4347-9751-bb114de8f04d', 'alan', '01a5f5db2d97bd6b389e7a20bd889708', 'Graphic Designer', '2024-02-02 11:07:27'),
('c38515df-6d96-44e6-9ea7-53f8288deb7a', '0c6479fe-c079-42ea-8531-0cd9df74e7cf', 'ade', '01a5f5db2d97bd6b389e7a20bd889708', 'Graphic Designer', '2024-02-02 11:08:21'),
('c6b54d3b-751e-48cc-96fb-72455e06ae0d', 'a2aa762b-14f9-4f8e-9c71-2d3a038fb0b8', 'ihza', '01a5f5db2d97bd6b389e7a20bd889708', 'Head Designer', '2024-02-02 07:47:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_attachments`
--
ALTER TABLE `order_attachments`
  ADD PRIMARY KEY (`order_attachment_id`);

--
-- Indexes for table `overtimes`
--
ALTER TABLE `overtimes`
  ADD PRIMARY KEY (`overtime_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_packages`
--
ALTER TABLE `service_packages`
  ADD PRIMARY KEY (`service_package_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `task_attachments`
--
ALTER TABLE `task_attachments`
  ADD PRIMARY KEY (`task_attachment_id`);

--
-- Indexes for table `task_deliveries`
--
ALTER TABLE `task_deliveries`
  ADD PRIMARY KEY (`task_delivery_id`);

--
-- Indexes for table `task_delivery_attachments`
--
ALTER TABLE `task_delivery_attachments`
  ADD PRIMARY KEY (`task_delivery_attachments_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
