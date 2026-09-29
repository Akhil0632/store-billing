-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 06:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store_billing`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Rahul Sharma', 'rahul.sharma@example.com', '2026-09-29 00:41:37', '2026-09-29 00:41:37'),
(2, 'Priya Nair', 'priya.nair@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(3, 'Arjun Mehta', 'arjun.mehta@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(4, 'Sneha Reddy', 'sneha.reddy@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(5, 'Vikram Singh', 'vikram.singh@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(6, 'Ananya Iyer', 'ananya.iyer@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(7, 'Karthik Subramanian', 'karthik.subramanian@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(8, 'Meera Joshi', 'meera.joshi@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(9, 'Rohit Verma', 'rohit.verma@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(10, 'Divya Pillai', 'divya.pillai@example.com', '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(11, 'Akhil N A', 'akhil123@gmail.com', '2026-09-29 00:42:30', '2026-09-29 00:42:30'),
(12, 'User 1', 'user1@gmail.com', '2026-09-29 00:47:12', '2026-09-29 00:47:12'),
(13, 'Thomas', 'thomas@example.com', '2026-09-29 00:49:06', '2026-09-29 00:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_28_043725_create_products_table', 1),
(5, '2026_09_28_043854_create_customers_table', 1),
(6, '2026_09_28_043928_create_orders_table', 1),
(7, '2026_09_28_044113_create_order_items_table', 1),
(8, '2026_09_28_113956_create_personal_access_tokens_table', 1),
(9, '2026_09_29_041401_add_stock_check_constraint_to_products', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_total` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `amount_given` decimal(10,2) DEFAULT NULL,
  `change_returned` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `subtotal`, `tax_total`, `grand_total`, `amount_given`, `change_returned`, `created_at`, `updated_at`) VALUES
(1, 11, 805.00, 109.15, 914.15, 1000.00, 85.85, '2026-09-29 00:42:30', '2026-09-29 00:42:30'),
(2, 12, 838.00, 27.50, 865.50, 899.99, 34.49, '2026-09-29 00:47:12', '2026-09-29 00:47:12'),
(3, 13, 285.00, 51.30, 336.30, 400.00, 63.70, '2026-09-29 00:49:06', '2026-09-29 00:49:06'),
(4, 12, 475.00, 78.90, 553.90, 1000.00, 446.10, '2026-09-29 00:53:19', '2026-09-29 00:53:19'),
(5, 13, 285.00, 51.30, 336.30, 400.00, 63.70, '2026-09-29 00:54:53', '2026-09-29 00:54:53'),
(6, 13, 285.00, 51.30, 336.30, 500.00, 163.70, '2026-09-29 01:00:36', '2026-09-29 01:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `line_tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `tax_percentage`, `line_subtotal`, `line_tax`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 1, 275.00, 5.00, 275.00, 13.75, 275.00, '2026-09-29 00:42:30', '2026-09-29 00:42:30'),
(2, 1, 10, 2, 95.00, 18.00, 190.00, 34.20, 190.00, '2026-09-29 00:42:30', '2026-09-29 00:42:30'),
(3, 1, 17, 1, 340.00, 18.00, 340.00, 61.20, 340.00, '2026-09-29 00:42:30', '2026-09-29 00:42:30'),
(4, 2, 15, 2, 275.00, 5.00, 550.00, 27.50, 550.00, '2026-09-29 00:47:12', '2026-09-29 00:47:12'),
(5, 2, 12, 3, 68.00, 0.00, 204.00, 0.00, 204.00, '2026-09-29 00:47:12', '2026-09-29 00:47:12'),
(6, 2, 13, 1, 84.00, 0.00, 84.00, 0.00, 84.00, '2026-09-29 00:47:12', '2026-09-29 00:47:12'),
(7, 3, 1, 2, 55.00, 0.00, 0.00, 0.00, 129.80, '2026-09-29 00:49:06', '2026-09-29 00:49:06'),
(8, 3, 2, 5, 35.00, 0.00, 0.00, 0.00, 206.50, '2026-09-29 00:49:06', '2026-09-29 00:49:06'),
(9, 4, 20, 5, 20.00, 18.00, 100.00, 18.00, 100.00, '2026-09-29 00:53:19', '2026-09-29 00:53:19'),
(10, 4, 19, 1, 265.00, 18.00, 265.00, 47.70, 265.00, '2026-09-29 00:53:19', '2026-09-29 00:53:19'),
(11, 4, 18, 1, 110.00, 12.00, 110.00, 13.20, 110.00, '2026-09-29 00:53:19', '2026-09-29 00:53:19'),
(12, 5, 1, 2, 55.00, 0.00, 0.00, 0.00, 129.80, '2026-09-29 00:54:53', '2026-09-29 00:54:53'),
(13, 5, 2, 5, 35.00, 0.00, 0.00, 0.00, 206.50, '2026-09-29 00:54:53', '2026-09-29 00:54:53'),
(14, 6, 1, 2, 55.00, 0.00, 0.00, 0.00, 129.80, '2026-09-29 01:00:36', '2026-09-29 01:00:36'),
(15, 6, 2, 5, 35.00, 0.00, 0.00, 0.00, 206.50, '2026-09-29 01:00:36', '2026-09-29 01:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `code`, `price`, `tax_percentage`, `stock`, `created_at`, `updated_at`) VALUES
(1, 'Colgate Strong Teeth Toothpaste 100g', 'COL-100', 55.00, 18.00, 34, '2026-09-29 00:41:38', '2026-09-29 01:00:36'),
(2, 'Lifebuoy Soap 125g', 'LBW-125', 35.00, 18.00, 45, '2026-09-29 00:41:38', '2026-09-29 01:00:36'),
(3, 'Dove Shampoo 340ml', 'DOV-340', 299.00, 18.00, 15, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(4, 'Ponds Face Wash 100g', 'PND-100', 199.00, 18.00, 22, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(5, 'Nivea Body Lotion 400ml', 'NIV-400', 425.00, 18.00, 14, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(6, 'Parle-G Biscuit 250g', 'PAR-250', 25.00, 18.00, 120, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(7, 'Lays Classic Salted 52g', 'LAY-052', 20.00, 18.00, 3, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(8, 'Haldiram Aloo Bhujia 200g', 'HAL-200', 55.00, 18.00, 30, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(9, 'Dairy Milk Silk 60g', 'CDM-SLK', 90.00, 18.00, 50, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(10, 'Haldiram Soan Papdi 250g', 'HAL-SPN', 95.00, 18.00, 2, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(11, 'Modern Bread 400g', 'BRD-400', 45.00, 5.00, 4, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(12, 'Amul Milk 1L', 'AML-1L', 68.00, 0.00, 6, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(13, 'Eggs (Pack of 12)', 'EGG-012', 84.00, 0.00, 1, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(14, 'Tata Salt 1kg', 'TTL-1K', 28.00, 5.00, 80, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(15, 'Aashirvaad Atta 5kg', 'AAS-5K', 275.00, 5.00, 22, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(16, 'Tata Tea Gold 500g', 'TEA-500', 260.00, 5.00, 20, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(17, 'Nescafe Classic 100g', 'NES-100', 340.00, 18.00, 11, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(18, 'Real Mixed Fruit Juice 1L', 'REA-1L', 110.00, 12.00, 17, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(19, 'Horlicks Classic 500g', 'HOR-500', 265.00, 18.00, 15, '2026-09-29 00:41:38', '2026-09-29 00:41:38'),
(20, 'Bisleri Water 1L', 'BIS-1L', 20.00, 18.00, 95, '2026-09-29 00:41:38', '2026-09-29 00:41:38');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('mCpYupa07yp8hI8SatdaL3o82bZQEW6eqskEenvT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'eyJfdG9rZW4iOiJ4REp5bFdHcWg0OTlzSWRCcHRLNVJHd3NNb29WbFhxb2c5M1JuQ1duIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnMiLCJyb3V0ZSI6Im9yZGVycy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1790663126);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_code_unique` (`code`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
