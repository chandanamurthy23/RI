-- SQL Database Dump for Rithamaya E-Commerce Website
-- Database: `rithamaya_db`

CREATE DATABASE IF NOT EXISTS `rithamaya_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rithamaya_db`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `short_description` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `weight` VARCHAR(50) DEFAULT '500g',
  `badge` VARCHAR(50) DEFAULT 'Organic',
  `stock` INT DEFAULT 100,
  `image` VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Cash on Delivery',
  `payment_status` ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',
  `order_status` ENUM('Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Processing',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `order_items`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `contact_messages`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(200) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Seed Data: Admin User
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `role`) VALUES
(1, 'Admin Manager', 'admin@rithamaya.com', '$2y$10$kn6TvKPYeWMpnBJXvQWw2.QoUavZwUPR8wlWw8vo9pz6uxVZyweR.', '+91 98765 43210', 'admin')
ON DUPLICATE KEY UPDATE `role` = VALUES(`role`);

-- --------------------------------------------------------
-- Seed Data: Categories
-- --------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`) VALUES
(1, 'Health & Multigrain Mixes', 'health-mixes', 'Nutritious multigrain health mix powders for energy and holistic wellness.', 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop'),
(2, 'Masala & Spice Powders', 'masala-powders', 'Authentic Karnataka-style homemade masalas and pure single spice powders.', 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=600&auto=format&fit=crop'),
(3, 'Baby & Infant Food', 'baby-food', 'Gentle, 100% natural baby ragi sari and nutrient-dense weaning foods.', 'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?w=600&auto=format&fit=crop'),
(4, 'Traditional Sweets & Laddus', 'sweets-laddus', 'Wholesome homemade sweets, dry fruit laddus, and guilt-free snacks.', 'https://images.unsplash.com/photo-1605888967806-4455001078f1?w=600&auto=format&fit=crop')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- --------------------------------------------------------
-- Seed Data: Products
-- --------------------------------------------------------
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `weight`, `badge`, `stock`, `image`, `is_featured`) VALUES
(1, 1, '35+ Multigrain Health Mix Powder', '35-multigrain-health-mix-powder', '35+ natural grains, millets, nuts, and natural herbs for daily family nourishment.', 'RM Sampoorna 35+ Multigrain Health Mix is expertly crafted using 35 nutrient-rich natural ingredients including finger millet (ragi), bajra, jowar, almonds, walnuts, green gram, cardamom, and lotus seeds. Free from preservatives, artificial flavors, and added sugars.', 349.00, '500g', 'Bestseller', 120, 'assets/images/products/multigrain-health-mix.png', 1),
(2, 3, 'Baby Ragi Sari Powder', 'baby-ragi-sari-powder', 'Traditional sprouted ragi baby cereal for healthy digestion and bone growth.', 'Handcrafted sprouted Ragi Sari specially prepared for infants and toddler nutrition. Rich in natural calcium, iron, and dietary fiber, promoting easy digestion and natural weight gain.', 399.00, '500g', 'Organic', 85, 'assets/images/products/baby ragi sari powder.jpg', 1),
(3, 2, 'Bisibele Bath Powder', 'bisibele-bath-powder', 'Authentic aroma & flavor for traditional Karnataka-style Bisibele Bath.', 'Made from roasted coriander, chana dal, urad dal, dried red chillies, cinnamon, cloves, and kapok buds. Delivers restaurant-style authentic taste to your homemade rice dishes.', 140.00, '250g', 'Traditional', 95, 'assets/images/products/bisibele-bath-powder.png', 1),
(4, 2, 'Dhaniya Powder (Coriander Powder)', 'dhaniya-powder', 'Freshly ground premium coriander seeds with intense natural fragrance.', '100% pure sun-dried coriander seeds slow-ground to preserve volatile essential oils. Elevates every curry, gravy, and dal with fresh aromatic warmth.', 95.00, '250g', 'Pure', 150, 'assets/images/products/dhaniya-powder.png', 0),
(5, 2, 'Red Chilli Powder', 'red-chilli-powder', 'Rich vibrant red color with mild authentic heat from select chillies.', 'Blended from handpicked Byadgi and Guntur chillies giving vibrant red color without synthetic colors or chemicals.', 120.00, '250g', 'Spicy', 110, 'assets/images/products/red-chilli-powder.png', 0),
(6, 2, 'Authentic Sambar Powder', 'sambar-powder', 'Classic South Indian aromatic spice mix for comforting homemade sambar.', 'A balanced blend of roasted spices, roasted lentils, fenugreek, cumin, and asafoetida. Perfect for daily vegetable sambars.', 130.00, '250g', 'Karnataka Special', 140, 'assets/images/products/sambar-powder.png', 1),
(7, 2, 'Mutton Sambar Powder', 'mutton-sambar-powder', 'Special secret blend for rich, spicy non-vegetarian sambars & gravies.', 'Traditional homemade Mutton Sambar Powder expertly blended with exotic spices to bring out the authentic taste of Karnataka non-veg cuisine.', 160.00, '250g', 'Authentic', 75, 'assets/images/products/mutton-sambar-powder.png', 1),
(8, 2, 'Homemade Puliyogare Powder', 'puliyogare-powder', 'Tangy tamarind rice masala powder prepared from heritage family recipe.', 'Prepared with dry roasted sesame seeds, black pepper, peanuts, coriander, mustard, and tamarind spice blend for instant authentic tamarind rice.', 145.00, '250g', 'Heritage Recipe', 90, 'assets/images/products/puliyogare-powder.png', 0),
(9, 4, 'Nutritious Dry Fruits Laddu', 'dry-fruits-laddu', 'No added sugar laddu packed with dates, figs, almonds, cashews & ghee.', 'Healthy guilt-free dessert crafted with premium Medjool dates, dried figs, roasted almonds, pistachios, cashews, seeds, and pure desi cow ghee.', 420.00, '400g', 'Sugar-Free', 60, 'assets/images/products/dry-fruits-laddu.jpg', 1),
(10, 1, 'Korle Millet Powder', 'korle-millet-powder', 'Pure organic blend of Browntop (Korle) millet, pumpkin seeds & brown rice.', 'High-fiber diabetic-friendly millet powder rich in minerals and slow-releasing complex carbohydrates. Great for kanji, porridge, or roti flour mix.', 280.00, '500g', 'Superfood', 80, 'assets/images/products/korle-millet-powder.png', 0)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
