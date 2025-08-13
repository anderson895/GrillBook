-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 04:14 AM
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
(13, 'Christmass Sale', '', 'deals_6898ab32964117.30991192.jpg', '[\"8\",\"3\",\"2\"]', 'promo_deals', '2025-09-25');

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
(4, 'Sarciado', 'main course', 'Sarciado is one of those comforting dishes that remind me of home. Whether made from leftover fried fish or cooked fresh, it delivers the kind of flavor that satisfies. I grew up enjoying this with hot rice and a splash of fish sauce on the side. The soft eggs mixed with tomatoes, onions, and garlic create a rich sauce that clings to the crispy fish. It is the kind of meal that proves you do not need much to make something truly delicious.', 20.00, 'menu_689402519a94a6.13433280.jpg', 1),
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
  `status` enum('pending','confirmed','cancelled','completed','archived') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `reserve_user_id`, `reserve_unique_code`, `table_code`, `seats`, `date_schedule`, `time_schedule`, `menu_total`, `promo_total`, `group_total`, `grand_total`, `selected_menus`, `selected_promos`, `selected_groups`, `termsFileSigned`, `proof_of_payment`, `created_at`, `updated_at`, `status`) VALUES
(10, 3, '8M5FYBP7', 'DJ', 5, '2025-08-13', '09:30:00', 0.00, 270.00, 350.00, 620.00, '[]', '[{\"id\":\"10\",\"name\":\"Barkada Promo\",\"price\":270,\"type\":\"promo_deal\"}]', '[{\"id\":\"11\",\"name\":\"Bar & Grill\",\"price\":350,\"type\":\"group_deal\"}]', 'terms_689bebc0824356.93856830.pdf', 'proof_689bebc0820eb3.72815322.png', '2025-08-13 01:34:56', '2025-08-13 02:04:04', 'archived'),
(11, 3, '39IRHFCO', 'C5', 8, '2025-08-13', '09:50:00', 20.00, 270.00, 530.00, 820.00, '[{\"id\":\"4\",\"name\":\"Sarciado\",\"price\":20,\"type\":\"menu\"}]', '[{\"id\":\"10\",\"name\":\"Barkada Promo\",\"price\":270,\"type\":\"promo_deal\"}]', '[{\"id\":\"12\",\"name\":\"Ultimate Mixed Drinks\",\"price\":530,\"type\":\"group_deal\"}]', 'terms_689bef90f04ae9.48170934.png', 'proof_689bef90ef7690.19069440.png', '2025-08-13 01:51:12', '2025-08-13 01:51:12', 'pending');

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
(2, 'juan', 'dela cruz', 'admin@gmail.com', '$2y$10$365uaOv8W44iLlOqJYyIgukb4WKci2BpbHEj9272Y0CdrQLd.0sdq', 'admin', 1),
(3, 'Joshua Anderson', 'Padilla', 'andersonandy046@gmail.com', '$2y$10$wT2P4z2/HuwbW1Dcgb/zleHRj62t2f0XXPiiUrbB2s/xhJj8p0I.W', 'customer', 1),
(4, 'john', 'doe', 'jdoe@gmail.com', '$2y$10$bE11O2FVvkuB8Qq2EFHjOOB3ZY5eftxoEXx9GUe2pphKGumQ2hx0q', 'customer', 1);

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `deals`
--
ALTER TABLE `deals`
  MODIFY `deal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
