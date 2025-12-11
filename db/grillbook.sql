-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 05:36 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `emergency_closures`
--

CREATE TABLE `emergency_closures` (
  `id` int(11) NOT NULL,
  `closure_date` date NOT NULL,
  `reason` text NOT NULL,
  `closure_type` enum('emergency','holiday','maintenance') DEFAULT 'emergency',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `restored_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `emergency_closures`
--

INSERT INTO `emergency_closures` (`id`, `closure_date`, `reason`, `closure_type`, `status`, `created_at`, `updated_at`, `restored_at`) VALUES
(1, '2025-12-06', 'Typhoon', 'emergency', 'inactive', '2025-12-06 11:26:32', '2025-12-06 11:26:43', NULL),
(2, '2025-12-06', 'Typhoon', 'emergency', 'inactive', '2025-12-06 11:26:43', '2025-12-08 12:53:11', NULL),
(3, '2025-12-08', 'Testing', 'emergency', 'inactive', '2025-12-08 12:53:11', '2025-12-08 12:55:18', NULL),
(7, '2025-12-08', 'TEST 3', 'emergency', 'inactive', '2025-12-09 02:55:24', '2025-12-09 09:47:05', '2025-12-09 09:47:05'),
(8, '2025-12-09', 'SAMPLE', 'emergency', 'inactive', '2025-12-09 09:47:34', '2025-12-09 09:47:54', '2025-12-09 09:47:54'),
(9, '2025-12-10', 'Testing', 'emergency', 'inactive', '2025-12-11 04:47:03', '2025-12-11 04:48:30', '2025-12-11 04:48:30'),
(10, '2025-12-11', 'Test', 'emergency', 'inactive', '2025-12-11 12:21:05', '2025-12-11 12:21:15', '2025-12-11 12:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `group_deals`
--

CREATE TABLE `group_deals` (
  `deal_id` int(11) NOT NULL,
  `deal_name` varchar(255) NOT NULL,
  `deal_price` decimal(10,2) NOT NULL,
  `deal_description` text DEFAULT NULL,
  `deal_img_banner` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_deal_items`
--

CREATE TABLE `group_deal_items` (
  `id` int(11) NOT NULL,
  `deal_id` int(11) NOT NULL,
  `item_type` enum('menu','drink') NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holiday_schedules`
--

CREATE TABLE `holiday_schedules` (
  `id` int(11) NOT NULL,
  `holiday_date` date NOT NULL,
  `holiday_name` varchar(255) NOT NULL,
  `holiday_type` enum('closure','late_opening') DEFAULT 'closure',
  `late_opening_time` time DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `holiday_schedules`
--

INSERT INTO `holiday_schedules` (`id`, `holiday_date`, `holiday_name`, `holiday_type`, `late_opening_time`, `status`, `created_at`, `updated_at`) VALUES
(1, '2025-12-09', 'TESTING 1', 'closure', NULL, 'inactive', '2025-12-09 02:53:30', '2025-12-09 02:54:18'),
(2, '2025-12-09', 'TESTING 2', 'closure', NULL, 'active', '2025-12-09 02:54:18', '2025-12-09 02:54:18'),
(3, '2025-12-12', 'Test', '', NULL, 'active', '2025-12-11 12:28:09', '2025-12-11 12:28:09'),
(4, '2025-12-12', 'Test', '', NULL, 'active', '2025-12-11 12:28:15', '2025-12-11 12:28:15');

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
(9, 'Green apple Shake', 'beverages', '', 180.00, 'menu_6898aac9d15837.68526775.webp', 1),
(10, 'Kropek', 'appetizer', 'Kropek is a crispy, bite-sized snack known for its crunchy texture and bold, savory flavor.', 145.00, 'menu_690dfffd33dec1.96157422.jpg', 1),
(11, 'Sinigang Liempo', 'main course', 'Sinigang Liempo is a hearty Filipino main course featuring tender pork belly simmered in a tangy tamarind broth with assorted vegetables.', 485.00, 'menu_690e00c1236fb3.89641101.jpg', 1),
(12, 'Chicken Salpicao', 'main course', 'Chicken Salpicao is a savory Filipino dish made of tender chicken pieces sautéed in garlic, butter, and soy sauce for a rich, flavorful taste.', 345.00, 'menu_690e013eeb6c51.96274714.jpg', 1),
(13, 'Sizzling Bangus', 'main course', 'Sizzling Bangus is a flavorful Filipino dish featuring marinated milkfish served on a hot plate with a savory sauce and aromatic seasonings.', 385.00, 'menu_690e01aeaa89f8.39492400.jpg', 1),
(14, 'Espesyal Crispy Talong', 'main course', 'Espesyal Crispy Talong is a delicious Filipino dish made of eggplant coated in a crispy batter and fried to golden perfection, served with a savory dipping sauce.', 195.00, 'menu_690e025ba00d12.40573993.jpg', 1),
(15, 'Tinapa Rice Platter', 'main course', 'Tinapa Rice Platter is a fragrant Filipino rice dish mixed with smoked fish flakes, garlic, and vegetables, offering a savory and smoky flavor in every bite.', 235.00, 'menu_690e02d5d34246.68927541.jpg', 1),
(16, 'Wendell\'s Wings (10\'s)', 'appetizer', 'Wendell’s Wings (10’s) features ten pieces of crispy, juicy chicken wings coated in your choice of flavorful sauce for a perfect blend of crunch and taste.', 385.00, 'menu_690e038f147792.50996492.jpg', 1),
(17, 'Liempo Kare-Kare', 'main course', 'Liempo Kare-Kare is a rich Filipino main dish made with tender grilled pork belly served in a creamy peanut sauce with vegetables and bagoong on the side.', 585.00, 'menu_690e03efe7fad2.60350495.jpg', 1),
(18, 'Sizzling Hotdog', 'appetizer', 'Sizzling Hotdog is a tasty dish featuring juicy hotdogs served on a sizzling plate with onions, sauce, and a hint of smokiness for extra flavor.', 195.00, 'menu_690e04941cbc63.95026551.jpg', 1),
(19, 'Salted Egg Fries', 'appetizer', 'Salted Egg Fries are crispy golden fries drizzled with a rich, savory salted egg sauce and often topped with cheese or herbs for extra flavor.', 215.00, 'menu_690e04dba21972.28455106.jpg', 1),
(20, 'Espesyal Bulaklak', 'appetizer', 'Espesyal Bulaklak is a Filipino dish featuring deep-fried pork mesenteries seasoned to crispy perfection and served with a savory dipping sauce.', 265.00, 'menu_690e058b1438b4.05174006.jpg', 1),
(21, 'Crispy Pata', 'main course', 'Crispy Pata is a Filipino delicacy of deep-fried pork knuckle, perfectly tender inside with a golden, crunchy exterior, typically served with a savory soy-vinegar dipping sauce.', 875.00, 'menu_690e060ba6dd70.65824407.jpg', 1),
(22, 'Sizzling Mushroom', 'side dish', 'Sizzling Mushroom is a savory dish of tender mushrooms cooked on a hot plate with garlic, butter, and soy-based sauce, served sizzling for extra flavor and aroma.', 195.00, 'menu_690e06b8497613.09116295.jpg', 1),
(23, 'Calamares', 'appetizer', 'Calamares is a popular appetizer of lightly battered and deep-fried squid rings, served crispy with a tangy dipping sauce.', 315.00, 'menu_690e0726e44a01.98324180.jpg', 1),
(24, 'Tuna Belly', 'main course', 'Tuna Belly is a rich and tender cut of tuna, often grilled or seared to perfection, offering a buttery texture and savory flavor.', 495.00, 'menu_690e07a629e2e4.80963426.jpg', 1),
(25, 'Crispy Liempo (Half)', 'main course', 'Crispy Liempo (Half) is a succulent portion of pork belly, seasoned and deep-fried to achieve a golden, crunchy exterior while remaining tender and juicy inside.', 365.00, 'menu_690e081a9a5c48.31323999.jpg', 1),
(26, 'Chicken Skin', 'side dish', 'Chicken Skin is a crispy, savory snack made from fried chicken skin, golden-brown and crunchy with a rich, flavorful taste.', 195.00, 'menu_690e0886abade7.70261972.jpg', 1),
(27, 'Twister Isaw', 'side dish', 'Twister Isaw is a popular Filipino street food of marinated chicken or pork intestines, skewered, twisted, and grilled to a savory, smoky perfection.', 185.00, 'menu_690e08d8c429a1.06344501.jpg', 1),
(28, 'Dynamite (4pcs)', 'side dish', 'Dynamite is a Filipino appetizer of chili peppers stuffed with cheese and sometimes meat, then battered and deep-fried until golden and crispy.', 215.00, 'menu_6939fddb410933.21368252.jpg', 1),
(30, 'Sapporo', 'beverages', 'Light Drink for Adults', 100.00, 'menu_6939fd66a32419.29350546.jpg', 0),
(51, 'San Miguel Pale Pilsen', 'beverages', 'Classic Filipino beer with smooth taste (5% alcohol, 330ml)', 90.00, 'drink_sanmiguel.jpg', 1),
(52, 'San Miguel Light', 'beverages', 'Light beer with fewer calories (5% alcohol, 330ml)', 95.00, 'drink_smlight.webp', 1),
(53, 'Red Horse Beer', 'beverages', 'Strong beer with extra kick (6.9% alcohol, 330ml)', 100.00, 'redhorse.jpg', 1),
(54, 'Corona Extra', 'beverages', 'Premium Mexican beer with lime (4.5% alcohol, 330ml)', 120.00, 'Corona.webp', 1),
(55, 'Heineken', 'beverages', 'Dutch lager with crisp taste (5% alcohol, 330ml)', 130.00, 'Heineken.webp', 1),
(56, 'House Red Wine', 'beverages', 'House blend red wine, full-bodied (13% alcohol, 175ml)', 250.00, 'redwine.jpg', 1),
(57, 'House White Wine', 'beverages', 'Crisp white wine, perfect with seafood (12% alcohol, 175ml)', 240.00, 'whitewine.jpg', 1),
(58, 'Mojito', 'beverages', 'Rum cocktail with mint, lime, and soda (15% alcohol, 300ml)', 180.00, 'mojito.jpg', 1),
(59, 'Margarita', 'beverages', 'Tequila cocktail with lime and salt rim (18% alcohol, 300ml)', 190.00, 'margarita.jpg', 1),
(60, 'Long Island Iced Tea', 'beverages', 'Strong cocktail with multiple spirits (22% alcohol, 350ml)', 220.00, 'longisland.webp', 1),
(61, 'Jack Daniels', 'beverages', 'Tennessee whiskey, smooth and smoky (40% alcohol, 30ml)', 180.00, 'JD.webp', 1),
(62, 'Johnnie Walker Black', 'beverages', 'Premium blended Scotch whisky (40% alcohol, 30ml)', 220.00, 'JW.jpg', 1),
(63, 'Absolut Vodka', 'beverages', 'Swedish vodka, clean and smooth (40% alcohol, 30ml)', 160.00, 'Absolut.webp', 1),
(64, 'Bombay Sapphire', 'beverages', 'Premium London dry gin (40% alcohol, 30ml)', 170.00, 'bombay.webp', 1),
(65, 'Bacardi White Rum', 'beverages', 'Light and smooth Caribbean rum (40% alcohol, 30ml)', 150.00, 'bacardi.webp', 1),
(66, 'Tequila Jose Cuervo', 'beverages', 'Traditional Mexican tequila (40% alcohol, 30ml)', 170.00, 'JC.webp', 1),
(67, 'Bucket of 6 Beers', 'beverages', '6 bottles of San Miguel Pale Pilsen (5% alcohol, 6x330ml)', 500.00, 'bucket.jpg', 1),
(68, 'Tower Draft Beer', 'beverages', '5-liter tower of draft beer (5% alcohol, 5000ml)', 600.00, 'tower.jpg', 1),
(69, 'Margarita Pitcher', 'beverages', '1.5-liter pitcher of margarita (15% alcohol, 1500ml)', 450.00, 'margaritapitcher.jpg', 1),
(70, 'Sangria Pitcher', 'beverages', 'Red wine cocktail with fruits (12% alcohol, 1500ml)', 400.00, 'Sangriapitcher.jpg', 1),
(71, 'Sample', 'soup', 'Sample', 1.00, 'menu_693a2d8471e983.43619611.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('reservation','payment','system','alert') DEFAULT 'system',
  `is_read` tinyint(1) DEFAULT 0,
  `reservation_id` int(11) DEFAULT NULL,
  `table_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `type`, `is_read`, `reservation_id`, `table_code`, `created_at`, `updated_at`) VALUES
(1, 'Holiday Closure Notice', 'Sent holiday closure notice for 2025-11-28: test', 'system', 1, NULL, NULL, '2025-11-27 04:21:07', '2025-12-05 21:33:07'),
(2, 'Holiday Closure Notice', 'Sent holiday closure notice for 2025-11-28: test', 'system', 1, NULL, NULL, '2025-11-27 04:21:07', '2025-12-05 21:33:07'),
(3, 'Holiday Closure Notice', 'Sent holiday closure notice for 2025-11-28: test', 'system', 1, NULL, NULL, '2025-11-27 04:21:08', '2025-12-05 21:33:06'),
(4, 'Holiday Closure Notice', 'Sent holiday closure notice for 2025-11-28: Today, As we embark the amazing day given by use we are pleasing to tell you that we are close to this day. thank you and Godbless.', 'system', 1, NULL, NULL, '2025-11-27 04:22:42', '2025-12-05 21:09:00'),
(5, 'Emergency Closure', 'Sent emergency closure notice for 2025-11-27: Heayv typonhe', 'alert', 1, NULL, NULL, '2025-11-27 04:23:04', '2025-12-05 21:33:03'),
(6, 'New Reservation', 'Table G6 has a new pending reservation from Guest User', 'reservation', 1, 6, 'G6', '2025-11-27 21:20:53', '2025-12-05 21:33:05'),
(7, 'Reservation Updated', 'Reservation for table G6 changed from request_reschedule to confirmed', 'reservation', 1, 6, 'G6', '2025-12-05 22:27:42', '2025-12-05 22:43:44'),
(8, 'Reservation Updated', 'Reservation for table G6 changed from confirmed to request_reschedule', 'reservation', 1, 6, 'G6', '2025-12-05 22:49:05', '2025-12-06 03:05:13'),
(9, 'Reservation Updated', 'Reservation for table G6 changed from request_reschedule to confirmed', 'reservation', 1, 6, 'G6', '2025-12-05 23:11:45', '2025-12-05 23:42:17'),
(10, 'Reservation Updated', 'Reservation for table G6 changed from confirmed to cancelled', 'reservation', 1, 6, 'G6', '2025-12-05 23:50:18', '2025-12-06 03:05:13'),
(11, 'Reservation Updated', 'Reservation for table G6 changed from cancelled to confirmed', 'reservation', 1, 6, 'G6', '2025-12-05 23:50:26', '2025-12-06 03:05:13'),
(12, 'Reservation Updated', 'Reservation for table G6 changed from confirmed to request_cancel', 'reservation', 1, 6, 'G6', '2025-12-05 23:50:33', '2025-12-06 00:21:23'),
(13, 'Reservation Updated', 'Reservation for table G6 changed from request_cancel to confirmed', 'reservation', 1, 6, 'G6', '2025-12-06 00:37:56', '2025-12-06 03:05:13'),
(14, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-06: 2', 'alert', 1, NULL, NULL, '2025-12-06 01:57:41', '2025-12-06 03:05:13'),
(15, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-06: 2', 'alert', 1, NULL, NULL, '2025-12-06 01:57:42', '2025-12-06 03:05:13'),
(16, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-06: 2', 'alert', 1, NULL, NULL, '2025-12-06 01:57:52', '2025-12-06 03:05:13'),
(17, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-06: 2', 'alert', 1, NULL, NULL, '2025-12-06 01:57:52', '2025-12-06 03:05:13'),
(18, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-06: 2', 'alert', 1, NULL, NULL, '2025-12-06 01:57:52', '2025-12-06 03:05:13'),
(19, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-06: 2', 'alert', 1, NULL, NULL, '2025-12-06 01:57:53', '2025-12-06 03:05:13'),
(20, 'Holiday Closure Notice', 'Sent holiday closure notice for 2025-12-07: 1', 'system', 1, NULL, NULL, '2025-12-06 01:57:53', '2025-12-06 03:05:13'),
(21, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-06: Typhoon', 'alert', 1, NULL, NULL, '2025-12-06 03:26:52', '2025-12-06 06:14:16'),
(22, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-06: Typhoon', 'alert', 1, NULL, NULL, '2025-12-06 03:26:53', '2025-12-06 06:14:16'),
(23, 'Holiday Closure Notice', 'Sent holiday closure notice for 2025-12-07: Samples', 'system', 1, NULL, NULL, '2025-12-06 03:27:24', '2025-12-06 06:14:16'),
(24, 'Reservation Updated', 'Reservation for table G6 changed from confirmed to request_reschedule', 'reservation', 1, 6, 'G6', '2025-12-06 04:08:16', '2025-12-06 06:14:16'),
(25, 'Reservation Updated', 'Reservation for table G6 changed from request_reschedule to request_cancel', 'reservation', 1, 6, 'G6', '2025-12-06 04:08:18', '2025-12-06 06:14:16'),
(26, 'Reservation Updated', 'Reservation for table G6 changed from request_cancel to confirmed', 'reservation', 1, 6, 'G6', '2025-12-06 04:09:23', '2025-12-06 06:14:16'),
(27, 'Reservation Updated', 'Reservation for table G6 changed from confirmed to cancelled', 'reservation', 1, 6, 'G6', '2025-12-06 06:13:59', '2025-12-06 06:14:16'),
(28, 'Reservation Updated', 'Reservation for table G6 changed from cancelled to confirmed', 'reservation', 1, 6, 'G6', '2025-12-06 06:57:56', '2025-12-06 06:59:49'),
(29, 'Reservation Updated', 'Reservation for table G6 changed from confirmed to cancelled', 'reservation', 1, 6, 'G6', '2025-12-06 06:58:18', '2025-12-06 06:59:49'),
(30, 'Reservation Updated', 'Reservation for table G6 changed from cancelled to request_cancel', 'reservation', 1, 6, 'G6', '2025-12-06 06:58:38', '2025-12-06 06:59:49'),
(31, 'Reservation Updated', 'Reservation for table G6 changed from request_cancel to cancelled', 'reservation', 1, 6, 'G6', '2025-12-06 06:59:08', '2025-12-06 06:59:49'),
(32, 'Reservation Updated', 'Reservation for table G6 changed from cancelled to request_reschedule', 'reservation', 1, 6, 'G6', '2025-12-06 06:59:12', '2025-12-06 06:59:49'),
(33, 'New Reservation', 'New pending reservation for table A5 on 2025-12-11', 'reservation', 1, 7, 'A5', '2025-12-06 08:13:21', '2025-12-08 17:17:13'),
(34, 'New Reservation', 'New pending reservation for table B1 on 2025-12-26', 'reservation', 1, 8, 'B1', '2025-12-06 08:29:04', '2025-12-08 17:17:13'),
(35, 'New Reservation', 'Table B1 has a new pending reservation from Guest User', 'reservation', 1, 8, 'B1', '2025-12-06 08:29:08', '2025-12-08 17:17:13'),
(36, 'Reservation Updated', 'Reservation for table A5 changed from pending to confirmed', 'reservation', 1, 7, 'A5', '2025-12-06 15:52:33', '2025-12-08 17:17:13'),
(37, 'New Reservation', 'New pending reservation for table E1 on 2025-12-11', 'reservation', 1, 9, 'E1', '2025-12-06 18:55:19', '2025-12-08 17:17:13'),
(38, 'New Reservation', 'Table E1 has a new pending reservation from Guest User', 'reservation', 1, 9, 'E1', '2025-12-06 18:55:22', '2025-12-08 17:17:13'),
(39, 'Reservation Updated', 'Reservation for table E1 changed from pending to cancelled', 'reservation', 1, 9, 'E1', '2025-12-06 18:56:58', '2025-12-08 17:17:13'),
(40, 'Reservation Updated', 'Reservation for table E1 changed from cancelled to request_reschedule', 'reservation', 1, 9, 'E1', '2025-12-08 02:49:17', '2025-12-08 17:17:13'),
(41, 'Reservation Updated', 'Reservation for table B1 changed from pending to request_cancel', 'reservation', 1, 8, 'B1', '2025-12-08 02:49:34', '2025-12-08 17:17:13'),
(42, 'Reservation Updated', 'Reservation for table A5 changed from confirmed to request_cancel', 'reservation', 1, 7, 'A5', '2025-12-08 02:50:21', '2025-12-08 17:17:13'),
(43, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-08: Testing', 'alert', 1, NULL, '', '2025-12-08 04:53:23', '2025-12-08 17:17:13'),
(44, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-08: Testing', 'alert', 1, NULL, '', '2025-12-08 06:35:36', '2025-12-08 17:17:13'),
(45, 'Emergency Closure', 'Sent emergency closure notice for 2025-12-08: TESING', 'alert', 1, NULL, '', '2025-12-08 06:36:11', '2025-12-08 17:17:13'),
(46, 'New Reservation', 'New pending reservation for table G6 on 2025-12-09', 'reservation', 1, 10, 'G6', '2025-12-08 15:52:41', '2025-12-08 17:17:13'),
(47, 'New Reservation', 'Table G6 has a new pending reservation from Guest User', 'reservation', 1, 10, 'G6', '2025-12-08 15:52:45', '2025-12-08 17:17:13'),
(48, 'New Reservation', 'New pending reservation for table G5 on 2025-12-09', 'reservation', 1, 11, 'G5', '2025-12-08 15:53:35', '2025-12-08 17:17:13'),
(49, 'New Reservation', 'Table G5 has a new pending reservation from Guest User', 'reservation', 1, 11, 'G5', '2025-12-08 15:53:39', '2025-12-08 17:17:13'),
(50, 'Reservation Updated', 'Reservation for table G5 changed from pending to confirmed', 'reservation', 1, 11, 'G5', '2025-12-08 15:54:22', '2025-12-08 17:17:13'),
(51, 'Reservation Updated', 'Reservation for table G5 changed from confirmed to request_reschedule', 'reservation', 1, 11, 'G5', '2025-12-08 16:48:16', '2025-12-08 17:17:13'),
(52, 'Reservation Updated', 'Reservation for table G6 changed from pending to request_reschedule', 'reservation', 1, 10, 'G6', '2025-12-08 17:02:42', '2025-12-08 17:17:13'),
(53, 'Reservation Updated', 'Reservation for table G5 changed from request_reschedule to request_cancel', 'reservation', 1, 11, 'G5', '2025-12-08 17:17:35', '2025-12-08 22:42:49'),
(54, 'Reservation Updated', 'Reservation for table G5 changed from request_cancel to confirmed', 'reservation', 1, 11, 'G5', '2025-12-08 17:40:02', '2025-12-08 22:42:49'),
(55, 'New Reservation', 'New pending reservation for table G3 on 2025-12-10', 'reservation', 1, 12, 'G3', '2025-12-08 17:45:09', '2025-12-08 22:42:49'),
(56, '📋 New Reservation', 'Table G3 has a new pending reservation from Guest User for Wednesday, December 10, 2025 - 1:00 AM. Total: ₱236.5', 'reservation', 1, 12, 'G3', '2025-12-08 17:45:13', '2025-12-08 22:42:49'),
(57, 'New Reservation', 'New pending reservation for table B5 on 2025-12-10', 'reservation', 1, 13, 'B5', '2025-12-08 17:45:58', '2025-12-08 22:42:49'),
(58, '📋 New Reservation', 'Table B5 has a new pending reservation from Guest User for Wednesday, December 10, 2025 - 12:00 AM. Total: ₱436.5', 'reservation', 1, 13, 'B5', '2025-12-08 17:46:01', '2025-12-08 22:42:49'),
(59, 'Reservation Updated', 'Reservation for table G5 changed from confirmed to cancelled', 'reservation', 1, 11, 'G5', '2025-12-08 17:55:46', '2025-12-08 22:42:49'),
(60, 'Reservation Updated', 'Reservation for table B5 changed from pending to request_reschedule', 'reservation', 1, 13, 'B5', '2025-12-08 17:56:29', '2025-12-08 22:42:49'),
(61, 'Reschedule Request', 'Customer Guest User requested to reschedule reservation #13 for table B5. Reason: I would like to reschede my schesulde edas asd asd asda da', 'reservation', 1, 13, 'B5', '2025-12-08 17:56:29', '2025-12-08 22:42:49'),
(62, 'Reservation Updated', 'Reservation for table G3 changed from pending to request_cancel', 'reservation', 1, 12, 'G3', '2025-12-08 17:57:24', '2025-12-08 22:42:49'),
(63, 'Cancellation Request', 'Customer Guest User requested to cancel reservation #12 for table G3. Reason: ............................', 'reservation', 1, 12, 'G3', '2025-12-08 17:57:24', '2025-12-08 22:42:49'),
(64, 'Reservation Updated', 'Reservation for table B5 changed from request_reschedule to cancelled', 'reservation', 1, 13, 'B5', '2025-12-08 17:58:11', '2025-12-08 22:42:49'),
(65, 'Reservation Updated', 'Reservation for table G3 changed from request_cancel to cancelled', 'reservation', 1, 12, 'G3', '2025-12-08 17:58:16', '2025-12-08 22:42:49'),
(66, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:33:32', '2025-12-08 22:42:49'),
(67, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:34:02', '2025-12-08 22:42:49'),
(68, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:34:32', '2025-12-08 22:42:49'),
(69, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:35:02', '2025-12-08 22:42:49'),
(70, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:35:31', '2025-12-08 22:42:49'),
(71, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:36:02', '2025-12-08 22:42:49'),
(72, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:36:32', '2025-12-08 22:42:49'),
(73, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:37:02', '2025-12-08 22:42:49'),
(74, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:37:42', '2025-12-08 22:42:49'),
(75, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:38:42', '2025-12-08 22:42:49'),
(76, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:39:42', '2025-12-08 22:42:49'),
(77, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:40:42', '2025-12-08 22:42:49'),
(78, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:41:42', '2025-12-08 22:42:49'),
(79, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:42:42', '2025-12-08 22:42:49'),
(80, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:43:42', '2025-12-08 22:42:49'),
(81, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:44:02', '2025-12-08 22:42:49'),
(82, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:44:32', '2025-12-08 22:42:49'),
(83, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:45:42', '2025-12-08 22:42:49'),
(84, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:46:42', '2025-12-08 22:42:49'),
(85, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:47:08', '2025-12-08 22:42:49'),
(86, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:47:31', '2025-12-08 22:42:49'),
(87, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:48:02', '2025-12-08 22:42:49'),
(88, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:48:31', '2025-12-08 22:42:49'),
(89, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:49:01', '2025-12-08 22:42:49'),
(90, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:49:36', '2025-12-08 22:42:49'),
(91, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:50:02', '2025-12-08 22:42:49'),
(92, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:50:31', '2025-12-08 22:42:49'),
(93, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:51:04', '2025-12-08 22:42:49'),
(94, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:51:31', '2025-12-08 22:42:49'),
(95, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:52:02', '2025-12-08 22:42:49'),
(96, '✅ SYSTEM RESTORED ✅', 'System access has been restored successfully! The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-08 18:52:31', '2025-12-08 22:42:49'),
(97, '🏖️ Holiday Notice', 'Holiday scheduled for 2025-12-09: TESTING 1', 'system', 1, NULL, NULL, '2025-12-08 18:53:30', '2025-12-08 22:42:49'),
(98, '🏖️ Holiday Notice', 'Holiday scheduled for 2025-12-09: TESTING 2', 'system', 1, NULL, NULL, '2025-12-08 18:54:18', '2025-12-08 22:42:49'),
(99, '🚨 SYSTEM EMERGENCY CLOSURE 🚨', 'Emergency closure activated for 2025-12-08. Reason: TEST 3. System is now in emergency shutdown mode.', 'alert', 1, NULL, NULL, '2025-12-08 18:55:35', '2025-12-09 01:48:23'),
(100, '🔐 User Login', 'User tanjicommissions@gmail.com (customer) logged into the system', 'system', 1, NULL, NULL, '2025-12-08 19:55:11', '2025-12-09 01:48:22'),
(101, 'Reservation Updated', 'Reservation for table B5 changed from cancelled to confirmed', 'reservation', 1, 13, 'B5', '2025-12-08 19:59:41', '2025-12-08 22:42:49'),
(102, 'Reservation Updated', 'Reservation for table B5 changed from confirmed to request_reschedule', 'reservation', 1, 13, 'B5', '2025-12-08 20:04:32', '2025-12-08 22:42:49'),
(103, 'Reschedule Request', 'Customer Guest User requested to reschedule reservation #13 for table B5. Reason: I need to reschule my reservation', 'reservation', 1, 13, 'B5', '2025-12-08 20:04:32', '2025-12-08 22:42:49'),
(104, '🔐 User Login', 'User admin@gmail.com (admin) logged into the system', 'system', 1, NULL, NULL, '2025-12-08 22:43:03', '2025-12-09 03:10:07'),
(105, '✅ SYSTEM RESTORED ✅', 'Emergency system shutdown has been lifted. The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-09 01:47:05', '2025-12-09 03:10:07'),
(106, 'Emergency Closure', 'Emergency closure on 2025-12-09: SAMPLE. All reservations cancelled.', 'alert', 1, NULL, NULL, '2025-12-09 01:47:34', '2025-12-09 01:48:29'),
(107, '✅ SYSTEM RESTORED ✅', 'Emergency system shutdown has been lifted. The system is now active and ready for reservations.', 'system', 1, NULL, NULL, '2025-12-09 01:47:54', '2025-12-09 01:48:28'),
(108, 'Reservation Updated', 'Reservation for table B5 changed from request_reschedule to confirmed', 'reservation', 1, 13, 'B5', '2025-12-09 01:57:13', '2025-12-09 03:10:07'),
(109, 'Reservation Updated', 'Reservation for table B5 changed from confirmed to request_reschedule', 'reservation', 1, 13, 'B5', '2025-12-09 01:57:38', '2025-12-09 01:57:56'),
(110, 'Reschedule Request', 'Customer Guest User requested to reschedule reservation #13 for table B5. Reason: I need to do some important matters', 'reservation', 1, 13, 'B5', '2025-12-09 01:57:38', '2025-12-09 01:57:59'),
(111, '🎉 New Deal Created', 'New deal created: Sample 1 (group_deals)', 'system', 1, NULL, NULL, '2025-12-09 02:40:10', '2025-12-09 03:10:07'),
(112, 'New Reservation', 'New walkin reservation for table E4 on 2025-12-09', 'reservation', 1, 14, 'E4', '2025-12-09 03:04:39', '2025-12-09 03:10:07'),
(113, 'New Reservation', 'New unavailable reservation for table E3 on 2025-12-09', 'reservation', 1, 15, 'E3', '2025-12-09 03:04:49', '2025-12-09 03:10:07'),
(114, 'New Reservation', 'New unavailable reservation for table E3 on 2025-12-09', 'reservation', 0, 16, 'E3', '2025-12-09 03:28:27', '2025-12-09 03:28:27'),
(115, 'Reservation Updated', 'Reservation for table E3 changed from unavailable to confirmed', 'reservation', 0, 15, 'E3', '2025-12-09 03:28:42', '2025-12-09 03:28:42'),
(116, 'Reservation Updated', 'Reservation for table E4 changed from walkin to request_reschedule', 'reservation', 0, 14, 'E4', '2025-12-09 03:29:00', '2025-12-09 03:29:00'),
(117, 'New Reservation', 'New walkin reservation for table E8 on 2025-12-09', 'reservation', 0, 17, 'E8', '2025-12-09 03:29:28', '2025-12-09 03:29:28'),
(118, 'Reservation Updated', 'Reservation for table E8 changed from walkin to confirmed', 'reservation', 0, 17, 'E8', '2025-12-09 03:30:03', '2025-12-09 03:30:03'),
(119, 'Reservation Updated', 'Reservation for table E3 changed from  to request_reschedule', 'reservation', 0, 16, 'E3', '2025-12-09 03:36:46', '2025-12-09 03:36:46'),
(120, '🔐 User Login', 'User tanjicommissions@gmail.com (customer) logged into the system', 'system', 0, NULL, NULL, '2025-12-09 05:31:30', '2025-12-09 05:31:30'),
(121, '🔐 User Login', 'User admin@gmail.com (admin) logged into the system', 'system', 0, NULL, NULL, '2025-12-09 13:52:36', '2025-12-09 13:52:36'),
(122, '🔐 User Login', 'User tanjicommissions@gmail.com (customer) logged into the system', 'system', 0, NULL, NULL, '2025-12-10 17:37:27', '2025-12-10 17:37:27'),
(123, '🔐 User Login', 'User tanjicommissions@gmail.com (customer) logged into the system', 'system', 0, NULL, NULL, '2025-12-10 17:37:56', '2025-12-10 17:37:56'),
(124, '🔐 User Login', 'User tanjicommissions@gmail.com (customer) logged into the system', 'system', 0, NULL, NULL, '2025-12-10 17:55:05', '2025-12-10 17:55:05'),
(125, '🔐 User Login', 'User admin@gmail.com (admin) logged into the system', 'system', 0, NULL, NULL, '2025-12-10 20:46:12', '2025-12-10 20:46:12'),
(126, 'Emergency Closure', 'Emergency closure on 2025-12-10: Testing. All reservations cancelled.', 'alert', 0, NULL, NULL, '2025-12-10 20:47:03', '2025-12-10 20:47:03'),
(127, '✅ SYSTEM RESTORED ✅', 'Emergency system shutdown has been lifted. The system is now active and ready for reservations.', 'system', 0, NULL, NULL, '2025-12-10 20:48:30', '2025-12-10 20:48:30'),
(129, 'Reservation Updated', 'Reservation for table E8 changed from confirmed to completed', 'reservation', 0, 17, 'E8', '2025-12-10 21:45:38', '2025-12-10 21:45:38'),
(130, 'Reservation Updated', 'Reservation for table E8 changed from completed to confirmed', 'reservation', 0, 17, 'E8', '2025-12-10 22:00:42', '2025-12-10 22:00:42'),
(131, 'New Reservation', 'New  reservation for table E4 on 2025-12-11', 'reservation', 0, 18, 'E4', '2025-12-10 22:11:49', '2025-12-10 22:11:49'),
(132, 'New Reservation', 'New  reservation for table E4 on 2025-12-11', 'reservation', 0, 19, 'E4', '2025-12-10 22:11:52', '2025-12-10 22:11:52'),
(133, 'New Reservation', 'New  reservation for table E4 on 2025-12-11', 'reservation', 0, 20, 'E4', '2025-12-10 22:12:12', '2025-12-10 22:12:12'),
(134, 'Reservation Updated', 'Reservation for table E4 changed from  to confirmed', 'reservation', 0, 18, 'E4', '2025-12-10 22:13:25', '2025-12-10 22:13:25'),
(136, 'Reservation Updated', 'Reservation for table E8 changed from confirmed to completed', 'reservation', 0, 17, 'E8', '2025-12-10 22:14:40', '2025-12-10 22:14:40'),
(137, '🔐 User Login', 'User tanjicommissions@gmail.com (customer) logged into the system', 'system', 0, NULL, NULL, '2025-12-11 00:00:40', '2025-12-11 00:00:40'),
(139, 'New Reservation', 'New pending reservation for table E7 on 2025-12-12', 'reservation', 0, 21, 'E7', '2025-12-11 00:43:40', '2025-12-11 00:43:40'),
(140, 'Reservation Updated', 'Reservation for table E7 changed from pending to request_reschedule', 'reservation', 0, 21, 'E7', '2025-12-11 00:44:13', '2025-12-11 00:44:13'),
(141, 'Reservation Updated', 'Reservation for table B5 changed from request_reschedule to confirmed', 'reservation', 0, 13, 'B5', '2025-12-11 01:53:54', '2025-12-11 01:53:54'),
(142, '🍽️ New Menu Added', 'New menu item added: Sample (soup) - ₱1', 'system', 0, NULL, NULL, '2025-12-11 02:33:40', '2025-12-11 02:33:40'),
(143, 'Reservation Updated', 'Reservation for table E7 changed from request_reschedule to confirmed', 'reservation', 0, 21, 'E7', '2025-12-11 03:28:58', '2025-12-11 03:28:58'),
(144, '🗑️ Menu Item Removed', 'Menu item #71 has been removed from the system', 'system', 0, NULL, NULL, '2025-12-11 04:00:57', '2025-12-11 04:00:57'),
(145, 'Reservation Updated', 'Reservation for table E4 changed from confirmed to cancelled', 'reservation', 0, 18, 'E4', '2025-12-11 04:21:05', '2025-12-11 04:21:05'),
(146, 'Emergency Closure', 'Emergency closure on 2025-12-11: Test. All reservations cancelled.', 'alert', 0, NULL, NULL, '2025-12-11 04:21:05', '2025-12-11 04:21:05'),
(147, '✅ SYSTEM RESTORED ✅', 'Emergency system shutdown has been lifted. The system is now active and ready for reservations.', 'system', 0, NULL, NULL, '2025-12-11 04:21:15', '2025-12-11 04:21:15'),
(148, 'Holiday Notice', 'Holiday on 2025-12-12: Test', 'alert', 0, NULL, NULL, '2025-12-11 04:28:09', '2025-12-11 04:28:09'),
(149, 'Holiday Notice', 'Holiday on 2025-12-12: Test', 'alert', 0, NULL, NULL, '2025-12-11 04:28:15', '2025-12-11 04:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `promo_deals`
--

CREATE TABLE `promo_deals` (
  `promo_id` int(11) NOT NULL,
  `promo_name` varchar(255) NOT NULL,
  `promo_description` text DEFAULT NULL,
  `promo_image` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','upcoming') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promo_deals`
--

INSERT INTO `promo_deals` (`promo_id`, `promo_name`, `promo_description`, `promo_image`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Samples', 'Sample', '', '2025-12-05', '2025-12-25', 'active', '2025-12-05 10:05:20', '2025-12-05 10:05:20');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `table_code` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `seats` int(11) NOT NULL,
  `date_schedule` date NOT NULL,
  `time_schedule` time NOT NULL,
  `menu_total` decimal(10,2) DEFAULT 0.00,
  `drink_total` decimal(10,2) DEFAULT 0.00,
  `promo_total` decimal(10,2) DEFAULT 0.00,
  `group_total` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `selected_menus` text DEFAULT NULL,
  `selected_drinks` text DEFAULT NULL,
  `selected_promos` text DEFAULT NULL,
  `selected_groups` text DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `amount_to_pay` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','request_cancel','request_reschedule','completed') NOT NULL DEFAULT 'pending',
  `cancellation_reason` text DEFAULT NULL,
  `reschedule_reason` text DEFAULT NULL,
  `approval_reason` text DEFAULT NULL,
  `corkage_fee` decimal(10,2) DEFAULT 0.00,
  `service_charge` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `table_code`, `customer_name`, `customer_email`, `seats`, `date_schedule`, `time_schedule`, `menu_total`, `drink_total`, `promo_total`, `group_total`, `grand_total`, `selected_menus`, `selected_drinks`, `selected_promos`, `selected_groups`, `payment_type`, `amount_to_pay`, `payment_method`, `payment_proof`, `status`, `cancellation_reason`, `reschedule_reason`, `approval_reason`, `corkage_fee`, `service_charge`, `created_at`, `updated_at`, `customer_phone`) VALUES
(10, 'G6', 'Guest User', 'tanjicommissions@gmail.com', 1, '2025-12-09', '22:00:00', 215.00, 0.00, 0.00, 0.00, 436.50, '{\"28\":{\"id\":28,\"name\":\"Dynamite (4pcs)\",\"price\":215,\"quantity\":1,\"total\":215}}', NULL, '{}', '{}', 'full', 436.50, 'cash', NULL, 'request_reschedule', NULL, NULL, NULL, 0.00, 0.00, '2025-12-08 15:52:41', '2025-12-08 17:02:42', '09933570557'),
(11, 'G5', 'Guest User', 'tanjicommissions@gmail.com', 3, '2025-12-09', '22:30:00', 185.00, 0.00, 0.00, 0.00, 703.50, '{\"27\":{\"id\":27,\"name\":\"Twister Isaw\",\"price\":185,\"quantity\":1,\"total\":185}}', NULL, '{}', '{}', 'full', 703.50, 'cash', NULL, 'cancelled', NULL, NULL, NULL, 0.00, 0.00, '2025-12-08 15:53:35', '2025-12-08 17:55:46', '09933570557'),
(12, 'G3', 'Guest User', 'tanjicommissions@gmail.com', 2, '2025-12-10', '01:00:00', 215.00, 0.00, 0.00, 0.00, 236.50, '{\"28\":{\"id\":28,\"name\":\"Dynamite (4pcs)\",\"price\":215,\"quantity\":1,\"total\":215}}', NULL, '{}', '{}', 'full', 236.50, 'cash', NULL, 'cancelled', NULL, NULL, NULL, 0.00, 0.00, '2025-12-08 17:45:09', '2025-12-08 17:58:16', '09933570557'),
(13, 'B5', 'Guest User', 'tanjicommissions@gmail.com', 3, '2025-12-10', '00:00:00', 215.00, 0.00, 0.00, 0.00, 436.50, '{\"28\":{\"id\":28,\"name\":\"Dynamite (4pcs)\",\"price\":215,\"quantity\":1,\"total\":215}}', NULL, '{}', '{}', 'full', 436.50, 'cash', NULL, 'confirmed', NULL, NULL, NULL, 0.00, 0.00, '2025-12-08 17:45:58', '2025-12-11 01:53:54', '09933570557'),
(14, 'E4', 'Walk-in Customer', 'walkin@example.com', 2, '2025-12-09', '11:04:39', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'request_reschedule', NULL, NULL, NULL, 0.00, 0.00, '2025-12-09 03:04:39', '2025-12-09 03:29:00', '0000000000'),
(15, 'E3', 'Unavailable', 'unavailable@example.com', 0, '2025-12-09', '11:04:49', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'confirmed', NULL, NULL, NULL, 0.00, 0.00, '2025-12-09 03:04:49', '2025-12-09 03:28:42', '0000000000'),
(16, 'E3', 'Unavailable', 'unavailable@example.com', 0, '2025-12-09', '11:28:27', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'request_reschedule', NULL, NULL, NULL, 0.00, 0.00, '2025-12-09 03:28:27', '2025-12-09 03:36:46', '0000000000'),
(17, 'E8', 'Walk-in Customer', 'walkin@example.com', 2, '2025-12-09', '11:29:28', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', NULL, NULL, NULL, 0.00, 0.00, '2025-12-09 03:29:28', '2025-12-10 22:14:40', '0000000000'),
(18, 'E4', 'Unavailable', 'unavailable@example.com', 0, '2025-12-11', '06:11:49', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cancelled', 'Emergency Closure: Test', NULL, NULL, 0.00, 0.00, '2025-12-10 22:11:49', '2025-12-11 04:21:05', '0000000000'),
(19, 'E4', 'Unavailable', 'unavailable@example.com', 0, '2025-12-11', '06:11:52', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, 0.00, 0.00, '2025-12-10 22:11:52', '2025-12-10 22:11:52', '0000000000'),
(20, 'E4', 'Unavailable', 'unavailable@example.com', 0, '2025-12-11', '06:12:12', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, 0.00, 0.00, '2025-12-10 22:12:12', '2025-12-10 22:12:12', '0000000000'),
(21, 'E7', 'Guest User', 'tanjicommissions@gmail.com', 3, '2025-12-12', '00:30:00', 0.00, 0.00, 0.00, 0.00, 1000.00, '[]', '[]', '[]', '{\"29\":{\"id\":29,\"name\":\"Sample 1\",\"price\":0,\"quantity\":1,\"total\":0}}', 'full', 0.00, 'cash', '', 'confirmed', NULL, NULL, NULL, 0.00, 0.00, '2025-12-11 00:43:40', '2025-12-11 03:28:58', '09933570557');

--
-- Triggers `reservations`
--
DELIMITER $$
CREATE TRIGGER `after_reservation_insert` AFTER INSERT ON `reservations` FOR EACH ROW BEGIN
    INSERT INTO notifications (title, message, type, table_code, reservation_id, created_at)
    VALUES (
        'New Reservation',
        CONCAT('New ', NEW.status, ' reservation for table ', NEW.table_code, ' on ', NEW.date_schedule),
        'reservation',
        NEW.table_code,
        NEW.id,
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_reservation_update` AFTER UPDATE ON `reservations` FOR EACH ROW BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO notifications (title, message, type, table_code, reservation_id, created_at)
        VALUES (
            'Reservation Updated',
            CONCAT('Reservation for table ', NEW.table_code, ' changed from ', OLD.status, ' to ', NEW.status),
            'reservation',
            NEW.table_code,
            NEW.id,
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reservation_requests`
--

CREATE TABLE `reservation_requests` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` enum('reschedule','cancel') NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_requests`
--

INSERT INTO `reservation_requests` (`id`, `reservation_id`, `user_id`, `request_type`, `reason`, `status`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 11, 76, 'reschedule', 'SAMPLE sdasda sdasdasdasdasd asd asd asd asd asda', 'pending', NULL, '2025-12-08 16:48:16', '2025-12-08 16:48:16'),
(2, 10, 76, 'reschedule', 'sadasdas asd asd asd asda sd asdasd', 'pending', NULL, '2025-12-08 17:02:42', '2025-12-08 17:02:42');

-- --------------------------------------------------------

--
-- Table structure for table `system_status`
--

CREATE TABLE `system_status` (
  `id` int(11) NOT NULL,
  `status_key` varchar(100) NOT NULL,
  `status_value` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_status`
--

INSERT INTO `system_status` (`id`, `status_key`, `status_value`, `created_at`, `updated_at`) VALUES
(1, 'emergency_mode', 'false', '2025-12-08 12:53:22', '2025-12-11 12:21:15');

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
  `user_status` int(11) NOT NULL DEFAULT 1 COMMENT '0=deleted,1=active',
  `verification_token` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_fname`, `user_lname`, `user_email`, `user_password`, `user_position`, `user_status`, `verification_token`, `is_verified`, `token_expires`) VALUES
(2, 'Abegail', 'Salem', 'admin@gmail.com', '$2y$10$ELmrWp70f3PxsVYpNuadGOmW06fM4frvDri8//mEti1yrQsyI8/8K', 'admin', 1, NULL, 1, NULL),
(32, 'Alice', 'Smith', 'headstaff@gmail.com', '$2y$10$u2Ccdb8we85KJB8O2IHsu.XXFQnZnOdNP3.6fXMLD0uD27deFGqnO', 'headstaff', 1, NULL, 1, NULL),
(76, 'Tanjicommissions', 'Customer', 'tanjicommissions@gmail.com', '$2y$10$5xB09Wagba7V1k87FCIIl.uC1XHkhEdlH9vBd83xjRLz0DpvgxTDW', 'customer', 1, NULL, 1, NULL);

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
(4, 'RESERV.', 'available', '2025-10-30 09:11:27', '2025-10-30 09:47:21'),
(5, 'D1', 'unavailable', '2025-10-30 09:16:59', '2025-10-30 09:16:59'),
(8, 'E5', 'unavailable', '2025-10-30 09:47:26', '2025-10-30 09:48:23'),
(13, 'B1', 'unavailable', '2025-10-30 09:49:28', '2025-10-30 09:49:28'),
(14, 'COMPLI', 'unavailable', '2025-10-30 10:15:15', '2025-10-30 10:15:15'),
(16, 'G5', 'unavailable', '2025-10-30 11:07:08', '2025-10-30 11:07:08'),
(18, 'G2', 'unavailable', '2025-10-30 11:08:45', '2025-10-30 11:08:45'),
(19, 'G4', 'unavailable', '2025-11-04 12:49:47', '2025-11-04 12:49:47'),
(21, 'C6', 'unavailable', '2025-11-07 15:20:36', '2025-11-07 15:20:36'),
(22, 'C4', 'unavailable', '2025-11-07 15:20:38', '2025-11-07 15:20:38'),
(23, 'C3', 'unavailable', '2025-11-07 15:20:40', '2025-11-07 15:20:40'),
(24, 'C2', 'unavailable', '2025-11-07 15:20:42', '2025-11-07 15:20:42'),
(25, 'C1', 'unavailable', '2025-11-07 15:20:44', '2025-11-07 15:20:44'),
(26, 'VIP 1', 'unavailable', '2025-11-07 15:26:23', '2025-11-07 15:26:23'),
(27, 'BILLIARDS', 'unavailable', '2025-11-07 15:26:25', '2025-11-07 15:26:25'),
(28, 'VIP 3', 'unavailable', '2025-11-07 15:26:28', '2025-11-07 15:26:28'),
(29, 'VIP 2', 'unavailable', '2025-11-07 15:26:29', '2025-11-07 15:26:29'),
(31, 'ACOUSTIC', 'unavailable', '2025-11-07 15:26:37', '2025-11-07 15:26:37'),
(32, 'SOUNDECT', 'unavailable', '2025-11-07 15:26:39', '2025-11-07 15:26:39'),
(34, 'DJ', 'unavailable', '2025-11-07 15:26:45', '2025-11-07 15:26:45'),
(35, 'F1', 'unavailable', '2025-11-07 15:26:50', '2025-11-07 15:26:50'),
(36, 'F2', 'unavailable', '2025-11-07 15:26:53', '2025-11-07 15:26:53'),
(37, 'F3', 'unavailable', '2025-11-07 15:26:55', '2025-11-07 15:26:55'),
(38, 'F4', 'unavailable', '2025-11-07 15:26:58', '2025-11-07 15:26:58'),
(40, 'E8', 'available', '2025-11-24 02:20:13', '2025-12-10 22:14:41');

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
-- Indexes for table `emergency_closures`
--
ALTER TABLE `emergency_closures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`closure_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `group_deals`
--
ALTER TABLE `group_deals`
  ADD PRIMARY KEY (`deal_id`);

--
-- Indexes for table `group_deal_items`
--
ALTER TABLE `group_deal_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deal_id` (`deal_id`);

--
-- Indexes for table `holiday_schedules`
--
ALTER TABLE `holiday_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`holiday_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `promo_deals`
--
ALTER TABLE `promo_deals`
  ADD PRIMARY KEY (`promo_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservation_requests`
--
ALTER TABLE `reservation_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservation_requests_status` (`status`),
  ADD KEY `idx_reservation_requests_reservation_id` (`reservation_id`);

--
-- Indexes for table `system_status`
--
ALTER TABLE `system_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_key` (`status_key`),
  ADD KEY `idx_status_key` (`status_key`);

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
  MODIFY `deal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `emergency_closures`
--
ALTER TABLE `emergency_closures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `group_deals`
--
ALTER TABLE `group_deals`
  MODIFY `deal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_deal_items`
--
ALTER TABLE `group_deal_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holiday_schedules`
--
ALTER TABLE `holiday_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `promo_deals`
--
ALTER TABLE `promo_deals`
  MODIFY `promo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reservation_requests`
--
ALTER TABLE `reservation_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_status`
--
ALTER TABLE `system_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `walkin_tables`
--
ALTER TABLE `walkin_tables`
  MODIFY `walkin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `group_deal_items`
--
ALTER TABLE `group_deal_items`
  ADD CONSTRAINT `group_deal_items_ibfk_1` FOREIGN KEY (`deal_id`) REFERENCES `group_deals` (`deal_id`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_requests`
--
ALTER TABLE `reservation_requests`
  ADD CONSTRAINT `reservation_requests_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
