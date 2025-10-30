-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 10:50 AM
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
-- Database: `grillbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `business_hours`
--

CREATE TABLE `business_hours` (
  `id` int(11) NOT NULL,
  `day_of_week` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `open_time` time NOT NULL,
  `close_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_hours`
--

INSERT INTO `business_hours` (`id`, `day_of_week`, `open_time`, `close_time`) VALUES
(1, 'Sunday', '17:00:00', '03:00:00'),
(2, 'Monday', '17:00:00', '02:00:00'),
(3, 'Tuesday', '17:00:00', '02:00:00'),
(4, 'Wednesday', '17:00:00', '02:00:00'),
(5, 'Thursday', '17:00:00', '02:00:00'),
(6, 'Friday', '19:00:00', '04:00:00'),
(7, 'Saturday', '19:00:00', '04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `deals`
--

CREATE TABLE `deals` (
  `deal_id` int(11) NOT NULL,
  `deal_name` varchar(60) NOT NULL,
  `deal_description` text NOT NULL,
  `deal_img_banner` varchar(255) NOT NULL,
  `deal_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `deal_type` enum('group_deals','promo_deals','','') NOT NULL,
  `deal_expiration` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deals`
--

INSERT INTO `deals` (`deal_id`, `deal_name`, `deal_description`, `deal_img_banner`, `deal_ids`, `deal_type`, `deal_expiration`) VALUES
(9, 'Food Platters', '', 'deals_6898a931cbe2a3.19899718.jpg', '[\"5\",\"9\",\"3\"]', 'group_deals', NULL),
(10, 'Barkada Promo', '', 'deals_6898a991cbd749.55023147.jpg', '[\"2\",\"5\"]', 'promo_deals', '2025-12-25'),
(11, 'Bar & Grill', '', 'deals_6898a9da07bbe1.86171904.jpg', '[\"8\",\"7\"]', 'group_deals', NULL),
(12, 'Ultimate Mixed Drinks', '', 'deals_6898a9f92f09c1.25099592.jpg', '[\"9\",\"8\",\"7\"]', 'group_deals', NULL),
(13, 'Christmass Sale', '', 'deals_6898ab32964117.30991192.jpg', '[\"8\",\"3\",\"2\"]', 'promo_deals', '2025-09-25'),
(14, 'esegf', 'sefsefse', 'deals_68e00a42800368.26974082.webp', '[\"7\",\"5\"]', 'promo_deals', '2025-10-17'),
(15, 'esrgfgdrg', 'dgdrgdr', 'deals_68e00a594bb9f0.28644501.jpg', '[\"8\"]', 'promo_deals', '2025-11-08'),
(16, 'efrsgdrgdr', 'i,jil', 'deals_68e00a6b558e38.86893669.jpg', '[\"8\",\"7\"]', 'promo_deals', '2025-11-08'),
(17, 'drgdrg', 'gyjgyj', 'deals_68e00a73cdef67.21999985.jpg', '[\"6\",\"5\"]', 'group_deals', NULL),
(18, 'fes', 'hdh', 'deals_68e00a79cbe240.98474318.webp', '[\"5\"]', 'group_deals', NULL),
(19, 'grdgh', 'tfhtf', 'deals_68e00a83a749a5.07734747.webp', '[\"6\",\"2\"]', 'group_deals', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `menu_name` varchar(60) NOT NULL,
  `menu_category` enum('dessert','appetizer','soup','salad','main course','side dish','beverages','') NOT NULL,
  `menu_description` text DEFAULT NULL,
  `menu_price` decimal(10,2) NOT NULL,
  `menu_image_banner` varchar(255) NOT NULL,
  `menu_status` int(11) NOT NULL DEFAULT 1 COMMENT '0=archived,1=active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `menu_name`, `menu_category`, `menu_description`, `menu_price`, `menu_image_banner`, `menu_status`) VALUES
(2, 'Fried Pork Belly Liempo', 'main course', 'Fried pork belly has always been a crowd favorite. It is simple, satisfying, and versatile. Whether you serve it as the main dish for lunch or dinner or bring it out as pulutan on a chill weekend, this crispy classic never gets old. Boiled first to keep it tender and then fried to golden perfection, every bite delivers a crunch that keeps you coming back for more. If you are feeding a family or just cooking for yourself, this dish fits right in. It is the kind of recipe that does not require complicated ingredients or steps but still gives you maximum satisfaction.', 200.00, 'menu_68940204c4b071.71364388.jpg', 1),
(3, 'Creamy Coconut Milk Fish Stew (Ginataang Isda with Eggplant ', 'main course', 'If you love ginataan dishes, this Creamy Coconut Milk Fish Stew is one recipe you’ll want to cook again and again. The combination of fried round scad simmered in coconut milk with eggplant and bok choy creates a satisfying dish that works beautifully for everyday meals. It is rich, savory, and naturally creamy, but still balanced thanks to the mild bitterness of the vegetables and a touch of vinegar. This is one of those dishes I used to enjoy back in the province. And now I still make it at home to relive those familiar flavors.', 150.00, 'menu_6894022b750d15.74182083.jpg', 1),
(4, 'Sarciado', 'main course', 'Sarciado is one of those comforting dishes that remind me of home. Whether made from leftover fried fish or cooked fresh, it delivers the kind of flavor that satisfies. I grew up enjoying this with hot rice and a splash of fish sauce on the side. The soft eggs mixed with tomatoes, onions, and garlic create a rich sauce that clings to the crispy fish. It is the kind of meal that proves you do not need much to make something truly delicious.', 20.00, 'menu_689402519a94a6.13433280.jpg', 0),
(5, 'Brazo de Mercedes', 'dessert', 'What I like most about Brazo de Mercedes is the sponge-like texture of the meringue that literally melts in my mouth. The light flavor of the meringue is balanced by the flavor of the rich custard filling. This is truly amazing!', 70.00, 'menu_6894054a55d6d5.49125553.jpg', 1),
(6, 'Ginisang Sitaw with Bell Pepper', 'main course', 'Ginisang sitaw with bell pepper is one of my go-to recipes when I want something hearty but simple. It brings me back to those everyday meals we often had growing up—where one good stir-fry, a bowl of rice, and maybe a fried egg made everything feel complete. The mix of tender pork, crisp vegetables, and that tasty sauce is just what you need on a busy day.', 250.00, 'menu_6895dc5c9c72e4.34679803.jpg', 1),
(7, 'Strawbery Shake', 'beverages', '', 150.00, 'menu_6898aa95390134.00193313.webp', 1),
(8, 'Manggo Shake', 'beverages', '', 200.00, 'menu_6898aaa8bba6e8.29857636.webp', 1),
(9, 'Green apple Shake', 'beverages', '', 180.00, 'menu_6898aac9d15837.68526775.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `reserve_user_id` int(11) NOT NULL,
  `reserve_unique_code` varchar(60) DEFAULT NULL,
  `table_code` varchar(10) NOT NULL,
  `seats` int(11) NOT NULL,
  `date_schedule` date NOT NULL,
  `time_schedule` time NOT NULL,
  `menu_total` decimal(10,2) DEFAULT 0.00,
  `promo_total` decimal(10,2) DEFAULT 0.00,
  `group_total` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `selected_menus` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_menus`)),
  `selected_promos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_promos`)),
  `selected_groups` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_groups`)),
  `termsFileSigned` varchar(255) NOT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','confirmed','cancelled','completed','request cancel','request new schedule') DEFAULT 'pending',
  `archived_by_admin` int(11) NOT NULL,
  `archived_by_customer` int(11) NOT NULL,
  `request_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `reserve_user_id`, `reserve_unique_code`, `table_code`, `seats`, `date_schedule`, `time_schedule`, `menu_total`, `promo_total`, `group_total`, `grand_total`, `selected_menus`, `selected_promos`, `selected_groups`, `termsFileSigned`, `proof_of_payment`, `created_at`, `updated_at`, `status`, `archived_by_admin`, `archived_by_customer`, `request_details`) VALUES
(113, 31, 'MAN3DV9C', 'G5', 6, '2025-10-30', '19:08:00', 0.00, 350.00, 0.00, 350.00, '[]', '[{\"id\":\"16\",\"name\":\"efrsgdrgdr\",\"price\":350,\"qty\":1,\"total\":350,\"type\":\"promo_deal\"}]', '[]', 'terms_69030f1b61a6d3.66546569.pdf', 'proof_69030f1b617138.70348974.webp', '2025-10-30 07:09:15', '2025-10-30 08:37:40', 'pending', 0, 0, NULL),
(114, 31, 'V7H3UFYX', 'E7', 4, '2025-10-30', '19:14:00', 830.00, 1220.00, 840.00, 2890.00, '[{\"id\":\"9\",\"name\":\"Green apple Shake\",\"price\":180,\"qty\":1,\"total\":180,\"type\":\"menu\"},{\"id\":\"8\",\"name\":\"Manggo Shake\",\"price\":200,\"qty\":1,\"total\":200,\"type\":\"menu\"},{\"id\":\"7\",\"name\":\"Strawbery Shake\",\"price\":150,\"qty\":3,\"total\":450,\"type\":\"menu\"}]', '[{\"id\":\"16\",\"name\":\"efrsgdrgdr\",\"price\":350,\"qty\":1,\"total\":350,\"type\":\"promo_deal\"},{\"id\":\"15\",\"name\":\"esrgfgdrg\",\"price\":200,\"qty\":3,\"total\":600,\"type\":\"promo_deal\"},{\"id\":\"10\",\"name\":\"Barkada Promo\",\"price\":270,\"qty\":1,\"total\":270,\"type\":\"promo_deal\"}]', '[{\"id\":\"19\",\"name\":\"grdgh\",\"price\":450,\"qty\":1,\"total\":450,\"type\":\"group_deal\"},{\"id\":\"18\",\"name\":\"fes\",\"price\":70,\"qty\":1,\"total\":70,\"type\":\"group_deal\"},{\"id\":\"17\",\"name\":\"drgdrg\",\"price\":320,\"qty\":1,\"total\":320,\"type\":\"group_deal\"}]', 'terms_69031073a88ab1.16988091.jpg', 'proof_69031073a86398.43199133.webp', '2025-10-30 07:14:59', '2025-10-30 07:16:08', 'completed', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_fname` varchar(60) NOT NULL,
  `user_lname` varchar(60) NOT NULL,
  `user_email` varchar(60) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_position` enum('admin','headstaff','customer','') NOT NULL,
  `user_status` int(11) NOT NULL DEFAULT 1 COMMENT '0=deleted,1=active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_fname`, `user_lname`, `user_email`, `user_password`, `user_position`, `user_status`) VALUES
(2, 'pedro', 'dela cruz', 'admin@gmail.com', '$2y$10$ELmrWp70f3PxsVYpNuadGOmW06fM4frvDri8//mEti1yrQsyI8/8K', 'admin', 1),
(4, 'john', 'doe', 'jdoe@gmail.com', '$2y$10$bE11O2FVvkuB8Qq2EFHjOOB3ZY5eftxoEXx9GUe2pphKGumQ2hx0q', 'customer', 1),
(29, 'john', 'doe', 'ardeleonpoultrysupplies@gmail.com', '$2y$10$e7.g0U2S48ty6aHBY8j6KuEqy4XZM.X03RyVRudcnuxt6E8XvlUJC', 'customer', 1),
(31, 'maria', 'doe', 'mypet02025@gmail.com', '$2y$10$zQaEEBkGL6Y5nqeopk8LBepGEK6.31jRkE3fdknZhX8p7Mzkc4tJC', 'customer', 1),
(32, 'Alice', 'Smith', 'alice.smith@gmail.com', '$2y$10$dummyhashedpassword1', 'customer', 1),
(33, 'Bob', 'Johnson', 'bob.johnson@gmail.com', '$2y$10$dummyhashedpassword2', 'customer', 1),
(34, 'Charlie', 'Brown', 'charlie.brown@gmail.com', '$2y$10$dummyhashedpassword3', 'customer', 1),
(35, 'Alice', 'Smith', 'alice.smith1@gmail.com', '$2y$10$dummyhashedpassword1', 'customer', 1),
(36, 'Bob', 'Johnson', 'bob.johnson1@gmail.com', '$2y$10$dummyhashedpassword2', 'customer', 1),
(37, 'Charlie', 'Brown', 'charlie.brown1@gmail.com', '$2y$10$dummyhashedpassword3', 'customer', 1),
(38, 'David', 'Wilson', 'david.wilson1@gmail.com', '$2y$10$dummyhashedpassword4', 'customer', 1),
(39, 'Eva', 'Davis', 'eva.davis1@gmail.com', '$2y$10$dummyhashedpassword5', 'customer', 1),
(40, 'Frank', 'Miller', 'frank.miller1@gmail.com', '$2y$10$dummyhashedpassword6', 'customer', 1),
(41, 'Grace', 'Taylor', 'grace.taylor1@gmail.com', '$2y$10$dummyhashedpassword7', 'customer', 1),
(42, 'Hannah', 'Anderson', 'hannah.anderson1@gmail.com', '$2y$10$dummyhashedpassword8', 'customer', 1),
(43, 'Ian', 'Thomas', 'ian.thomas1@gmail.com', '$2y$10$dummyhashedpassword9', 'customer', 1),
(44, 'Jane', 'Jackson', 'jane.jackson1@gmail.com', '$2y$10$dummyhashedpassword10', 'customer', 1),
(45, 'Kevin', 'White', 'kevin.white1@gmail.com', '$2y$10$dummyhashedpassword11', 'customer', 1),
(46, 'Laura', 'Harris', 'laura.harris1@gmail.com', '$2y$10$dummyhashedpassword12', 'customer', 1),
(47, 'Michael', 'Martin', 'michael.martin1@gmail.com', '$2y$10$dummyhashedpassword13', 'customer', 1),
(48, 'Nina', 'Thompson', 'nina.thompson1@gmail.com', '$2y$10$dummyhashedpassword14', 'customer', 1),
(49, 'Oscar', 'Garcia', 'oscar.garcia1@gmail.com', '$2y$10$dummyhashedpassword15', 'customer', 1),
(50, 'Paula', 'Martinez', 'paula.martinez1@gmail.com', '$2y$10$dummyhashedpassword16', 'customer', 1),
(51, 'Quinn', 'Robinson', 'quinn.robinson1@gmail.com', '$2y$10$dummyhashedpassword17', 'customer', 1),
(52, 'Rachel', 'Clark', 'rachel.clark1@gmail.com', '$2y$10$dummyhashedpassword18', 'customer', 1),
(53, 'Steve', 'Rodriguez', 'steve.rodriguez1@gmail.com', '$2y$10$dummyhashedpassword19', 'customer', 1),
(54, 'Tina', 'Lewis', 'tina.lewis1@gmail.com', '$2y$10$dummyhashedpassword20', 'customer', 1),
(55, 'Uma', 'Lee', 'uma.lee1@gmail.com', '$2y$10$dummyhashedpassword21', 'customer', 1),
(56, 'Victor', 'Walker', 'victor.walker1@gmail.com', '$2y$10$dummyhashedpassword22', 'customer', 1),
(57, 'Wendy', 'Hall', 'wendy.hall1@gmail.com', '$2y$10$dummyhashedpassword23', 'customer', 1),
(58, 'Xander', 'Allen', 'xander.allen1@gmail.com', '$2y$10$dummyhashedpassword24', 'customer', 1),
(59, 'Yara', 'Young', 'yara.young1@gmail.com', '$2y$10$dummyhashedpassword25', 'customer', 1),
(60, 'Zane', 'Hernandez', 'zane.hernandez1@gmail.com', '$2y$10$dummyhashedpassword26', 'customer', 1),
(61, 'Alicia', 'King', 'alicia.king1@gmail.com', '$2y$10$dummyhashedpassword27', 'customer', 1),
(62, 'Brian', 'Wright', 'brian.wright1@gmail.com', '$2y$10$dummyhashedpassword28', 'customer', 1),
(63, 'Clara', 'Lopez', 'clara.lopez1@gmail.com', '$2y$10$dummyhashedpassword29', 'customer', 1),
(64, 'Derek', 'Hill', 'derek.hill1@gmail.com', '$2y$10$dummyhashedpassword30', 'customer', 1);

-- --------------------------------------------------------

--
-- Table structure for table `walkin_tables`
--

CREATE TABLE `walkin_tables` (
  `walkin_id` int(11) NOT NULL,
  `walkin_table_code` varchar(50) NOT NULL,
  `walkin_status` enum('available','unavailable') DEFAULT 'available',
  `walkin_created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `walkin_updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `walkin_tables`
--

INSERT INTO `walkin_tables` (`walkin_id`, `walkin_table_code`, `walkin_status`, `walkin_created_at`, `walkin_updated_at`) VALUES
(3, 'DJ', 'unavailable', '2025-10-30 09:06:12', '2025-10-30 09:06:12'),
(4, 'RESERV.', 'available', '2025-10-30 09:11:27', '2025-10-30 09:47:21'),
(5, 'D1', 'unavailable', '2025-10-30 09:16:59', '2025-10-30 09:16:59'),
(8, 'E5', 'unavailable', '2025-10-30 09:47:26', '2025-10-30 09:48:23'),
(13, 'B1', 'unavailable', '2025-10-30 09:49:28', '2025-10-30 09:49:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_hours`
--
ALTER TABLE `business_hours`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deals`
--
ALTER TABLE `deals`
  ADD PRIMARY KEY (`deal_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reserve_user_id` (`reserve_user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `walkin_tables`
--
ALTER TABLE `walkin_tables`
  ADD PRIMARY KEY (`walkin_id`),
  ADD UNIQUE KEY `table_code` (`walkin_table_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_hours`
--
ALTER TABLE `business_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `deals`
--
ALTER TABLE `deals`
  MODIFY `deal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `walkin_tables`
--
ALTER TABLE `walkin_tables`
  MODIFY `walkin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`reserve_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
