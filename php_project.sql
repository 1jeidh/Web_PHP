-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 04:27 AM
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
-- Database: `php_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_cost` decimal(6,2) NOT NULL,
  `order_status` varchar(100) NOT NULL DEFAULT 'on_hold',
  `user_id` int(11) NOT NULL,
  `user_phone` int(11) NOT NULL,
  `user_city` varchar(255) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `user_id`, `user_phone`, `user_city`, `user_address`, `order_date`) VALUES
(9, 155.00, 'paid', 1, 1231251251, 'H', 'asdasdasda', '2025-10-05 13:41:11'),
(11, 250.00, 'shipped', 1, 1231251251, 'H', 'asdasdasda', '2025-10-05 14:01:47'),
(13, 52.00, 'paid', 1, 1231251251, 'H', 'asdasdasda', '2025-10-07 12:03:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `product_quantity`, `user_id`, `order_date`) VALUES
(8, 9, '1', 'White Shoes', 'fe1.jpg', 155.00, 1, 1, '2025-10-05 13:41:11'),
(10, 11, '3', 'Black Backpack', 'fe3.jpg', 250.00, 1, 1, '2025-10-05 14:01:47'),
(12, 13, '15', 'Blue Shoes', 'shoes3.jpg', 52.00, 1, 1, '2025-10-07 12:03:53');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `user_id`, `transaction_id`) VALUES
(1, 9, 1, '65840094VC832183L'),
(2, 11, 1, '3R223937DG225213X'),
(3, 13, 1, '9VT474609T3795911');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_category` varchar(100) NOT NULL,
  `product_description` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_image2` varchar(255) NOT NULL,
  `product_image3` varchar(255) NOT NULL,
  `product_image4` varchar(255) NOT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `product_special_offer` int(2) NOT NULL,
  `product_color` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_category`, `product_description`, `product_image`, `product_image2`, `product_image3`, `product_image4`, `product_price`, `product_special_offer`, `product_color`) VALUES
(1, 'White Shoes', 'bags', 'awesome white shoes', 'fe1.jpg', 'fe1.jpg', 'fe1.jpg', 'fe1.jpg', 160.00, 0, 'white'),
(2, 'Green Backpack', 'bags', 'awesome green backpack', 'fe2.jpg', 'fe2.jpg', 'fe2.jpg', 'fe2.jpg', 155.00, 0, 'green'),
(3, 'Black Backpack', 'bags', 'awesome black backpack', 'fe3.jpg', 'fe3.jpg', 'fe3.jpg', 'fe3.jpg', 250.00, 0, 'black'),
(4, 'Blue Backpack', 'bags', 'awesome blue backpack', 'fe4.jpg', 'fe4.jpg', 'fe4.jpg', 'fe4.jpg', 149.00, 0, 'blue'),
(5, 'Brown Coat', 'coats', 'Brown coat for men', 'clothes1.jpg', 'clothes1.jpg', 'clothes1.jpg', 'clothes1.jpg', 150.00, 0, 'brown'),
(6, 'Black Coat', 'coats', 'Black coat for men', 'clothes2.jpg', 'clothes2.jpg', 'clothes2.jpg', 'clothes2.jpg', 150.00, 0, 'black'),
(7, 'Blue Coat', 'coats', 'Blue coat for men', 'clothes3.jpg', 'clothes3.jpg', 'clothes3.jpg', 'clothes3.jpg', 150.00, 0, 'blue'),
(8, 'Light Blue Coat', 'coats', 'Light blue coat for men', 'clothes4.jpg', 'clothes4.jpg', 'clothes4.jpg', 'clothes4.jpg', 150.00, 0, 'blue'),
(9, 'Watch MK1', 'watches', 'Watch MK1 by ZXC', 'watch1.jpg', 'watch1.jpg', 'watch1.jpg', 'watch1.jpg', 150.00, 0, 'black'),
(10, 'Watch MK2', 'watches', 'Watch MK2 by ZXC', 'watch2.jpg', 'watch2.jpg', 'watch2.jpg', 'watch2.jpg', 250.00, 0, 'black'),
(11, 'Watch MK3', 'watches', 'Watch MK3 by ZXC', 'watch3.jpg', 'watch3.jpg', 'watch3.jpg', 'watch3.jpg', 252.00, 0, 'orange'),
(12, 'Watch MK4', 'watches', 'Watch MK4 by ZXC', 'watch4.jpg', 'watch4.jpg', 'watch4.jpg', 'watch4.jpg', 152.00, 0, 'white'),
(13, 'Black Shoes', 'shoes', 'Awesome black shoes', 'shoes1.jpg', 'shoes1.jpg', 'shoes1.jpg', 'shoes1.jpg', 50.00, 0, 'black'),
(14, 'Gray Shoes', 'shoes', 'Awesome gray shoes', 'shoes2.jpg', 'shoes2.jpg', 'shoes2.jpg', 'shoes2.jpg', 51.00, 0, 'gray'),
(15, 'Blue Shoes', 'shoes', 'Awesome blue shoes', 'shoes3.jpg', 'shoes3.jpg', 'shoes3.jpg', 'shoes3.jpg', 52.00, 0, 'blue'),
(16, 'Pink Shoes', 'shoes', 'Awesome pink shoes', 'shoes4.jpg', 'shoes.jpg', 'shoes4.jpg', 'shoes4.jpg', 53.00, 0, 'pink');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(100) NOT NULL,
  `user_role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role`) VALUES
(1, 'A', 'A@gmail.com', 'fcea920f7412b5da7be0cf42b8c93759', 'user'),
(2, 'Admin', 'ad@gmail.com', '823f4cfe556f95863e2df595c02b432f', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `UX_Constraint` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
