-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 07:16 PM
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
-- Database: `art_drawing`
--

-- --------------------------------------------------------

--
-- Table structure for table `address_book`
--

CREATE TABLE `address_book` (
  `addressID` varchar(255) NOT NULL,
  `userID` varchar(255) NOT NULL,
  `recipientName` varchar(100) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `shippingAddress` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postalCode` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `isDefault` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cartId` varchar(255) NOT NULL,
  `userId` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` varchar(255) NOT NULL,
  `cartID` varchar(255) DEFAULT NULL,
  `productID` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` varchar(255) NOT NULL,
  `CategoryName` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `CategoryName`, `photo`) VALUES
('CAT_001', 'Figure Drawing', 'image/figurecart.jpg'),
('CAT_002', 'Portrait Drawing', 'image/portraitcart.jpg'),
('CAT_003', 'Cartoon Drawing', 'image/cartooncart.jpg'),
('CAT_004', 'Line Drawing', 'image/linecart.jpg'),
('CAT_005', 'Anime drawing', 'image/animecart.jpg'),
('CAT_006', 'Sketch & Pencil Drawing', 'image/pencilcart.jpg'),
('CAT_007', 'Canvas Art', 'image/canvascart.jpg'),
('CAT_008', 'Digital Illustration', 'image/digitcart.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderId` varchar(255) NOT NULL,
  `userId` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `order_status` enum('Pending','Paid','Completed','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `shipping_address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` varchar(255) NOT NULL,
  `orderId` varchar(255) DEFAULT NULL,
  `productId` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `paymentID` varchar(255) NOT NULL,
  `orderID` varchar(255) NOT NULL,
  `userID` varchar(255) NOT NULL,
  `total_amount` int(11) DEFAULT NULL,
  `card_number` varchar(4) DEFAULT NULL,
  `expiry_date` varchar(7) DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL,
  `status` enum('success','failed') NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ProductID` varchar(255) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Price` int(255) NOT NULL,
  `Stock` int(255) NOT NULL,
  `CategoryID` varchar(255) NOT NULL,
  `Photo` varchar(255) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductID`, `ProductName`, `Description`, `Price`, `Stock`, `CategoryID`, `Photo`, `created_at`) VALUES
('PRO_001', 'Figure1', 'Elevate your collection with detailed figure sketches that celebrate the human form. These hand-drawn artworks showcase dynamic poses and natural movement, ideal for art lovers, students, or collectors. Printed on textured paper to maintain a raw, authentic pencil-drawn look. A perfect gift or personal inspiration piece.', 12, 123, 'CAT_001', 'image/gestures.png', '2025-04-01 00:13:49.375628'),
('PRO_002', 'Figure2', 'Elevate your collection with detailed figure sketches that celebrate the human form. These hand-drawn artworks showcase dynamic poses and natural movement, ideal for art lovers, students, or collectors. Printed on textured paper to maintain a raw, authentic pencil-drawn look. A perfect gift or personal inspiration piece.', 123, 123, 'CAT_001', 'image/figure2.jpg', '2025-04-01 00:13:49.375628'),
('PRO_003', 'Figure3', 'Elevate your collection with detailed figure sketches that celebrate the human form. These hand-drawn artworks showcase dynamic poses and natural movement, ideal for art lovers, students, or collectors. Printed on textured paper to maintain a raw, authentic pencil-drawn look. A perfect gift or personal inspiration piece.', 123, 123, 'CAT_001', 'image/figure3.jpg', '2025-04-01 00:13:49.375628'),
('PRO_005', 'Figure5', 'Elevate your collection with detailed figure sketches that celebrate the human form. These hand-drawn artworks showcase dynamic poses and natural movement, ideal for art lovers, students, or collectors. Printed on textured paper to maintain a raw, authentic pencil-drawn look. A perfect gift or personal inspiration piece.', 123, 123, 'CAT_001', 'image/figure5.jpg', '2025-04-10 00:13:49.375628'),
('PRO_006', 'Portrait1', 'Turn memories into masterpieces with our custom portrait drawings. Sketched with precision and care, each portrait captures fine facial details and emotions in stunning realism. Choose from graphite, charcoal, or colored pencil styles. A meaningful gift for birthdays, anniversaries, or any special moment.', 123, 123, 'CAT_002', 'image/portrait-drawing.jpg', '2025-04-10 00:13:49.375628'),
('PRO_007', 'portrait2', 'Turn memories into masterpieces with our custom portrait drawings. Sketched with precision and care, each portrait captures fine facial details and emotions in stunning realism. Choose from graphite, charcoal, or colored pencil styles. A meaningful gift for birthdays, anniversaries, or any special moment.', 12, 123, 'CAT_002', 'image/portrait3.jpg', '2025-04-10 00:13:49.375628'),
('PRO_008', 'portrait3', 'Turn memories into masterpieces with our custom portrait drawings. Sketched with precision and care, each portrait captures fine facial details and emotions in stunning realism. Choose from graphite, charcoal, or colored pencil styles. A meaningful gift for birthdays, anniversaries, or any special moment.', 123, 123, 'CAT_002', 'image/portrait4.jpg', '2025-04-10 00:13:49.375628'),
('PRO_009', 'cartoon1', 'Bring smiles with our playful cartoon-style drawings! Whether it’s a caricature of yourself, a friend, or a pet, each illustration is full of charm and personality. Ideal for gifts, profile pictures, or decor. Created digitally with vibrant colors and clean lines, printed on high-quality paper or available as a digital download.', 12, 123, 'CAT_003', 'image/cartoon-drawing.jpg', '2025-04-15 00:13:49.375628'),
('PRO_010', 'cartoon2', 'Bring smiles with our playful cartoon-style drawings! Whether it’s a caricature of yourself, a friend, or a pet, each illustration is full of charm and personality. Ideal for gifts, profile pictures, or decor. Created digitally with vibrant colors and clean lines, printed on high-quality paper or available as a digital download.', 12, 123, 'CAT_003', 'image/cartoon3.jpg', '2025-04-15 00:13:49.375628'),
('PRO_011', 'cartoon', 'Bring smiles with our playful cartoon-style drawings! Whether it’s a caricature of yourself, a friend, or a pet, each illustration is full of charm and personality. Ideal for gifts, profile pictures, or decor. Created digitally with vibrant colors and clean lines, printed on high-quality paper or available as a digital download.', 12, 12, 'CAT_003', 'image/cartoon5.jpg', '2025-04-15 00:13:49.375628'),
('PRO_012', 'Line1', 'Discover beauty in simplicity with our minimalist line art prints. Each piece is crafted with clean, continuous strokes that evoke elegance and emotion. Perfect for modern interiors, bedrooms, studios, or offices—this art style brings calm and sophistication to any space. Printed on premium matte paper for a timeless finish.', 123, 123, 'CAT_004', 'image/Line2.jpg', '2025-04-20 00:13:49.375628'),
('PRO_013', 'Line2', 'Discover beauty in simplicity with our minimalist line art prints. Each piece is crafted with clean, continuous strokes that evoke elegance and emotion. Perfect for modern interiors, bedrooms, studios, or offices—this art style brings calm and sophistication to any space. Printed on premium matte paper for a timeless finish.', 123, 13, 'CAT_004', 'image/Line3.png', '2025-04-20 00:13:49.375628'),
('PRO_014', 'Line3', 'Discover beauty in simplicity with our minimalist line art prints. Each piece is crafted with clean, continuous strokes that evoke elegance and emotion. Perfect for modern interiors, bedrooms, studios, or offices—this art style brings calm and sophistication to any space. Printed on premium matte paper for a timeless finish.', 12, 10, 'CAT_004', 'image/Line-drawing.jpg', '2025-04-20 00:13:49.375628'),
('PRO_015', 'Line5', 'Discover beauty in simplicity with our minimalist line art prints. Each piece is crafted with clean, continuous strokes that evoke elegance and emotion. Perfect for modern interiors, bedrooms, studios, or offices—this art style brings calm and sophistication to any space. Printed on premium matte paper for a timeless finish.', 12, 12, 'CAT_004', 'image/Line4.jpg', '2025-04-20 00:13:49.375628'),
('PRO_016', 'Anime1', 'Experience the magic of anime through beautifully hand-drawn artwork that captures the essence of your favorite characters and stories. Our anime drawings are crafted by passionate artists who specialize in various anime styles—from classic manga aesthetics to vibrant chibi designs and semi-realistic illustrations. Whether you’re looking for a custom portrait, a dynamic action pose, or a heartfelt scene, each piece is designed with attention to detail and creativity. Ideal as a unique gift, personal collection piece, or decorative art, our anime drawings let you celebrate your love for anime in a truly artistic way.', 12, 12, 'CAT_005', 'image/anime1.jpg', '2025-04-22 00:13:49.375628'),
('PRO_017', 'Anime2', 'Experience the magic of anime through beautifully hand-drawn artwork that captures the essence of your favorite characters and stories. Our anime drawings are crafted by passionate artists who specialize in various anime styles—from classic manga aesthetics to vibrant chibi designs and semi-realistic illustrations. Whether you’re looking for a custom portrait, a dynamic action pose, or a heartfelt scene, each piece is designed with attention to detail and creativity. Ideal as a unique gift, personal collection piece, or decorative art, our anime drawings let you celebrate your love for anime in a truly artistic way.', 12, 123, 'CAT_005', 'image/anime2.jpg', '2025-04-22 00:13:49.375628'),
('PRO_018', 'Anime3', 'Experience the magic of anime through beautifully hand-drawn artwork that captures the essence of your favorite characters and stories. Our anime drawings are crafted by passionate artists who specialize in various anime styles—from classic manga aesthetics to vibrant chibi designs and semi-realistic illustrations. Whether you’re looking for a custom portrait, a dynamic action pose, or a heartfelt scene, each piece is designed with attention to detail and creativity. Ideal as a unique gift, personal collection piece, or decorative art, our anime drawings let you celebrate your love for anime in a truly artistic way.', 12, 121, 'CAT_005', 'image/anime3.jpg', '2025-04-22 00:13:49.375628'),
('PRO_019', 'pencil1', 'Dive into a futuristic world with this neon-soaked digital illustration of a bustling cyberpunk metropolis. Vibrant and atmospheric, this artwork is a hit among sci-fi lovers and digital art collectors.', 12, 12, 'CAT_006', 'image/pencil1.jpg', '2025-04-24 00:13:49.375628'),
('PRO_020', 'pencil2', 'Dive into a futuristic world with this neon-soaked digital illustration of a bustling cyberpunk metropolis. Vibrant and atmospheric, this artwork is a hit among sci-fi lovers and digital art collectors.', 12, 12, 'CAT_006', 'image/pencil2.jpg', '2025-04-24 00:13:49.375628'),
('PRO_021', 'canvas1', 'Lose yourself in the hues of dusk with this dreamy canvas art piece. “Mystic Forest Twilight” captures the peaceful essence of nature as nightfall descends over an enchanted woodland. Printed on premium cotton canvas with fade-resistant ink, perfect for home or office decor.', 12, 12, 'CAT_007', 'image/canvas1.webp', '2025-04-24 00:13:49.375628'),
('PRO_022', 'canvas2', 'Lose yourself in the hues of dusk with this dreamy canvas art piece. “Mystic Forest Twilight” captures the peaceful essence of nature as nightfall descends over an enchanted woodland. Printed on premium cotton canvas with fade-resistant ink, perfect for home or office decor.', 21, 123, 'CAT_007', 'image/canvas2.jpg', '2025-04-24 00:13:49.375628'),
('PRO_023', 'digit1', 'Dive into a futuristic world with this neon-soaked digital illustration of a bustling cyberpunk metropolis. Vibrant and atmospheric, this artwork is a hit among sci-fi lovers and digital art collectors.', 23, 12, 'CAT_008', 'image/digit11.jpg', '2025-04-24 00:13:49.375628'),
('PRO_024', 'digit2', 'Dive into a futuristic world with this neon-soaked digital illustration of a bustling cyberpunk metropolis. Vibrant and atmospheric, this artwork is a hit among sci-fi lovers and digital art collectors.', 45, 21, 'CAT_008', 'image/digit12.jpg', '2025-04-24 00:13:49.375628'),
('PRO_025', 'Figure4', 'Elevate your collection with detailed figure sketches that celebrate the human form. These hand-drawn artworks showcase dynamic poses and natural movement, ideal for art lovers, students, or collectors. Printed on textured paper to maintain a raw, authentic pencil-drawn look. A perfect gift or personal inspiration piece.', 123, 123, 'CAT_001', 'image/figure4.jpg', '2025-04-27 16:37:54.062280');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` varchar(100) NOT NULL,
  `userName` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `birthday` varchar(100) DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `profile_image` varchar(100) DEFAULT NULL,
  `role` enum('customer','staff','manager') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `userName`, `email`, `password`, `phone`, `birthday`, `gender`, `profile_image`, `role`) VALUES
('CUST_001', 'chin tung leongs', 'reams@bluevystore.com', '$2y$10$OdNVVvSu6.hwYqYzaDmLi..waOrtjumdl2925PbYKla3NtcHXF6O6', '01234567899', '2025-03-12', 'F', 'image/user.png', 'customer'),
('STFF_002', 'Tan peng rong', 'tpr@gmail.com', '$2y$10$H7tmbWAqZ/MJq2f7fzgi7usuKTEe0soeUjtpdv/k.eyuVj/rT/sve', '01115152095', '2025-04-01', 'F', 'image/user_img.png', 'manager'),
('STFF_003', 'HENG ZHENG TECK', 'heng123@exmaple.com', '$2y$10$dczPv6F69zn0Yk5gAp4WOOLvAfMoXBFcO7/Xi5Co7wt6R52Amgv6m', '01115152095', '2025-04-03', 'M', 'image/username.png', 'staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address_book`
--
ALTER TABLE `address_book`
  ADD PRIMARY KEY (`addressID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cartId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cartID` (`cartID`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `orderId` (`orderId`),
  ADD KEY `productId` (`productId`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`paymentID`),
  ADD KEY `orderID` (`orderID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `fk_products_category` (`CategoryID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address_book`
--
ALTER TABLE `address_book`
  ADD CONSTRAINT `address_book_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cartID`) REFERENCES `carts` (`cartId`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`ProductID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `products` (`ProductID`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderId`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
