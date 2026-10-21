-- Glovia database (demo data)
-- Demo logins:  demo@glovia.com / demo1234   |   admin@glovia.com / admin1234 (admin)
--
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `glovia`
--

CREATE DATABASE IF NOT EXISTS `glovia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `glovia`;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `liked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--


-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `skin_type` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `num_reviews` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `product_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `trending` tinyint(1) DEFAULT 0,
  `best_seller` tinyint(1) DEFAULT 0,
  `picks` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `skin_type`, `price`, `rating`, `num_reviews`, `image_path`, `product_url`, `description`, `trending`, `best_seller`, `picks`) VALUES
(1, 'Uriage Hyseac Cleansing Gel', 'Normal', 86.25, 5, 682, 'assets/products/uriage.jpg', 'https://www.nahdionline.com/en-sa/uriage-hyseac-cleansing-gel-for-oily-skin-150-ml/pdp/100792295', '\nHigh rinsability, light foaming action in\ncontact with water, and a delicate fragrance characteristic of the entire Hyséac\nrange make this product extremely pleasant to use every day. The skin is\nclearer, fresher, and healthier', 0, 0, 1),
(2, 'Dr. Althea 345 Intensive Repair Cream', 'Scarring', 105.00, 4, 444, 'assets/products/345.webp', 'https://www.nahdionline.com/en-sa/dr-althea-resveratrol-345-intensive-repair-cream-50ml/pdp/103479448', 'Lightweight, nutrient-rich post-acne cream that soothes, hydrates, and nourishes. With 10,000ppm Niacinamide, Panthenol, and Opuntia Extract, it fades dark spots, calms redness, and restores moisture. Vegan, fragrance-free, and gentle for sensitive skin.', 0, 0, 1),
(3, 'Skin1004 Centella Toning Toner', 'Normal', 138.00, 5, 1023, 'assets/products/toner_c.png', 'https://www.nahdionline.com/en-sa/skin1004-centella-toning-toner-210ml/pdp/103231047', 'A gentle exfoliating toner designed for daily use to refine and enhance your skin texture.\n\nInfused with Centella Asiatica Extract and PHA, it effectively removes dead skin cells and excess sebum while calming the skin.', 0, 0, 1),
(4, 'Beauty of Joseon Centella Asiatica', 'Redness', 70.00, NULL, NULL, 'assets/products/calming.jpg', 'https://www.nahdionline.com/en-sa/beauty-of-joseon-calming-serum-green-tea-panthenol/pdp/102852301?srsltid=AfmBOor3b6IDAjmeonr_FSAi-zjum9eGXbsSR1TAfasq58Uey3qWHN_2', 'Soothing & Calming: Mugwort and Green Tea soothe sensitive skin with a gentle, relaxing touch. Hydration Boost: Green Tea, Mugwort, and Panthenol deeply hydrate for plump, refreshed skin. Radiant Glow: Revitalizes and energizes for a naturally luminous complexion.', 0, 0, 0),
(5, 'KAYZON Centella Asiatica Face Serum', 'Redness', 109.25, NULL, NULL, 'assets/products/rec2.jpg', 'https://www.nahdionline.com/en-sa/skin1004-centella-ampoule-55ml/pdp/103231291', 'This Centella serum deeply hydrates, soothes irritation, balances oil and moisture, and improves skin texture. Enriched with Centella Asiatica, it calms sensitive skin, reduces redness, fades dark spots, controls acne, and restores a healthy, radiant glow.', 0, 0, 0),
(6, 'Eucerin Atopicontrol Cream', 'Redness', 104.00, NULL, NULL, 'assets/products/rec4.webp', 'https://www.nahdionline.com/en-sa/eucerin-atopicontrol-acute-care-cream-40-ml/pdp/101152151', 'Intensively nourishes the skin while soothing itching and irritation. Helps to reduce redness, providing comfort and relief for sensitive or distressed skin, leaving it calm and hydrated.', 0, 0, 0),
(7, 'Bioderma Cicabio Arnica+', 'Redness', 95.45, NULL, NULL, 'assets/products/rec25.jpg', 'https://www.nahdionline.com/en-sa/bioderma-cicabio-arnica-40-ml/pdp/100975868?srsltid=AfmBOor623QD5Iw8KrqbAFtyi43zwSS2I3qXQ2cqG8nOkQ0Dwgc6iflu', 'Cicabio Arnica+ by BIODERMA soothes damaged skin with arnica extract, ANTALGICINE™, apigenin, and zinc to ease discomfort. Its creamy, dermatologist-tested formula absorbs easily and supports skin health.', 0, 0, 0),
(8, 'Cetaphil Facial Moisturizer', 'Dryness', 132.25, NULL, NULL, 'assets/products/rec5.jpg', 'https://www.nahdionline.com/en-sa/cetaphil-pro-daily-facial-moisturizer-spf-30-for-redness-prone-skin-50-ml/pdp/101463899', 'SPF 30 moisturizer for dry skin, offering long-lasting hydration and sun protection.\n                                Suitable for men and women, with ingredients like Ceramide, Shea Butter, Aloe Vera,\n                                and Niacinamide. Non-greasy, ideal for daily use and cold weather.', 0, 0, 0),
(9, 'La Roche-Posay Eczema Cream', 'Dryness', 53.00, NULL, NULL, 'assets/products/rec6.jpg', 'https://www.nahdionline.com/en-sa/la-roche-posay-lipikar-baume-apm-75-ml/pdp/101660753', 'Lipikar Eczema Cream soothes itching and irritation from eczema, deeply hydrates,\n                                and restores the skin barrier. Enriched with colloidal oatmeal, shea butter, and\n                                niacinamide. Safe for daily use on face and body.', 0, 0, 0),
(10, 'Cetaphil Deep Hydration Serum', 'Dryness', 129.25, NULL, NULL, 'assets/products/rec7.jpg', 'https://www.nahdionline.com/en-ae/cetaphil-optimal-hydration-48h-activation-serum-30ml/pdp/102985672?srsltid=AfmBOorXRkM_OuYb41-AGQqBkJ35VULkA2WMfFi_Iw2pLJ_FZHEEMmmR', 'Cetaphil Deep Hydration Serum delivers 48-hour moisture using HydroSensitiv Complex,\n                                hyaluronic acid, sunflower seed oil, and vitamins B5 & E. It soothes sensitive skin,\n                                absorbs instantly, and layers well under other products—ideal for dry, dehydrated,\n                                and easily irritated skin.', 0, 0, 0),
(11, 'Bionnex The Nordea Hyaluronic Acid 2% + B5 Serum', 'Dryness', 88.55, NULL, NULL, 'assets/products/rec27.png', 'https://www.nahdionline.com/en-sa/bionnex-the-nordea-hyaluronic-acid-2-b5-serum-30ml/pdp/103056744', '                       A lightweight serum with 3 types of hyaluronic acid, arctic algae, and Vitamin B5 to deeply hydrate, plump, and protect the skin. It improves elasticity, soothes, evens skin tone, and helps reduce fine lines. Suitable for all skin types.', 0, 0, 0),
(12, 'Vichy Mineral 89 Hyaluronic Acid', 'Dryness', 135.33, NULL, NULL, 'assets/products/rec28.jpg', 'https://www.nahdionline.com/en-sa/vichy-mineral-89-hyaluronic-acid-hydrating-plumping-serum-for-all-skin-types-50-ml/pdp/101208089', '                                Lightweight hyaluronic acid serum that hydrates, plumps, and strengthens the skin barrier. Fast-absorbing, fragrance-free, and made with just 11 ingredients for a healthy, radiant glow.', 0, 0, 0),
(13, 'Anua Niacinamide Spot Serum', 'Scarring', 126.50, NULL, NULL, 'assets/products/rec8.jpg', 'https://www.nahdionline.com/en-sa/anua-niacinamide-10-percent-plus-txa-4-percent-dark-spot-correcting-serum-30ml/pdp/103226846?srsltid=AfmBOoqAmt9XD_gTvGZJGz0RA8BDV5lDosesibmckufAZT8J-MxEQb_k', 'Brightens skin, fades dark spots, and hydrates with 10% Niacinamide, 4% TXA, Ceramides, and Hyaluronic Acid. Lightweight and gentle for sensitive skin.', 0, 0, 0),
(15, 'La Roche-Posay Anthelios Sunscreen', 'Scarring', 151.00, NULL, NULL, 'assets/products/rec3.jpg', 'https://www.nahdionline.com/en-sa/la-roche-posay-anthelios-uvmune-400-invisible-sunscreen-spf-50-50-ml/pdp/102068031?srsltid=AfmBOoq6FIne03uIbippwKSGlt9an1Yz21c9-vWRgiIf8eAynZnBSSpm', 'This award-winning facial sunscreen offers SPF 60 broad-spectrum protection with a lightweight, matte finish. Free of Oxybenzone and Octinoxate, it absorbs quickly, helps prevent sun damage and premature aging, and works well under makeup—ideal for normal to combination skin.', 0, 0, 0),
(16, 'The Ordinary Azelaic Acid', 'Scarring', 113.57, NULL, NULL, 'assets/products/rec10.png', 'https://www.nahdionline.com/en-sa/the-ordinary-azelaic-acid-suspension-10-30ml/pdp/102765741?srsltid=AfmBOook2TuM-CzY-61wSSVgsBPEyQDd13T_sbff-rBfBkkcYmrys3Rg', 'Lightweight, nutrient-rich post-acne cream that soothes, hydrates, and nourishes. With 10,000ppm Niacinamide, Panthenol, and Opuntia Extract, it fades dark spots, calms redness, and restores moisture. Vegan, fragrance-free, and gentle for sensitive skin.', 0, 0, 0),
(17, 'Avene Cleanance Comedomed', 'Scarring', 173.65, NULL, NULL, 'assets/products/rec23.jpg', 'https://www.nahdionline.com/en-sa/avene-cleanance-comedomed-anti-blemishes-concentrate-gel-30-ml/pdp/101677715', 'Plant-based serum with Avène Thermal Spring Water. Reduces oil, pimples, and blackheads in 7 days. Fast-absorbing and gentle for acne-prone skin.', 0, 0, 0),
(18, 'Eucerin Even Pigment Perfector', 'Scarring', 310.50, NULL, NULL, 'assets/products/rec29.jpg', 'https://www.nahdionline.com/en-sa/eucerin-even-pigment-perfector-dual-serum-30-ml/pdp/101485473', 'A dual-action serum with Thiamidol and Hyaluronic Acid to reduce dark spots and brighten skin. Visibly improves tone in 2 weeks and prevents spots from reappearing. Now in simplified mono-chamber packaging.', 0, 0, 0),
(19, 'Cosrx Hyaluronic Acid Essence', 'Aging', 119.86, NULL, NULL, 'assets/products/rec11.jpg', 'https://www.nahdionline.com/en-sa/102709820/pdp/102709820', 'This lightweight gel essence, enriched with pure hyaluronic acid, delivers deep hydration to dry, rough skin. It locks in moisture, smooths fine lines, and leaves your skin plump, dewy, and refreshed.', 1, 0, 0),
(20, 'Garnier Vitamin C Serum', 'Aging', 49.35, NULL, NULL, 'assets/products/rec12.jpg', 'https://www.nahdionline.com/en-sa/garnier-skin-active-fast-bright-vitamin-c-serum-30-ml/pdp/101795623', 'This powerful serum, with 30x Pure Vitamin C and Niacinamide, brightens skin, evens tone, and visibly fades dark spots in just 3 days. Rich in antioxidants, it revives dull, tired skin for a radiant, spotless glow.', 1, 0, 0),
(21, 'La Roche-Posay B3 Retinol Anti Aging Serum', 'Aging', 217.99, NULL, NULL, 'assets/products/rec30.jpg', 'https://www.nahdionline.com/en-sa/la-roche-posay-b3-retinol-anti-aging-serum-for-fine-lines-and-wrinkles-30ml/pdp/101660788', 'This anti-aging serum with pure retinol and vitamin B3 smooths, hydrates, and reduces the look of wrinkles and sun damage—gentle enough for sensitive skin.', 0, 0, 0),
(22, 'Nip + Fab Vitamin C Fix Serum', 'Aging', 96.69, NULL, NULL, 'assets/products/rec31.jpg', 'https://www.nahdionline.com/en-sa/nip-fab-vitamin-c-fix-serum-50-ml/pdp/101570731', 'This Vitamin C Serum boosts radiance and glow with antioxidant-rich Vitamin C, Carrot Oil, and Acai Berry. It brightens, smooths fine lines, and soothes with anti-inflammatory benefits.', 0, 0, 0),
(23, 'Beauty Of Joseon Ginseng Cleansing Oil', 'Normal', 77.00, NULL, NULL, 'assets/products/oil.jpg', 'https://www.nahdionline.com/en-sa/beauty-of-joseon-ginseng-cleansing-oil/pdp/102852183', 'At Beauty of Joseon, we value cleansing as the most important step in skincare. If neglected, skin impurities and sebum can build up in your pores, potentially causing skin inflammation. Our cleansing oil gently emulsifies and removes these impurities, ensuring a clean and refreshed complexion.', 1, 0, 0),
(24, 'Anua Birch Moisture Boosting Cream', 'Normal', 141.44, NULL, NULL, 'assets/products/70.jpg', 'https://www.nahdionline.com/en-sa/anua-birch-moisture-boosting-cream-50ml/pdp/102967141', 'Effective in eliminating makeup residue, blackheads and sebum, whilst preventing pore congestion. Essential step for double cleansing, with glowy glass skin results.', 1, 0, 0),
(25, 'Uriage Hyseac SPF 50+ Fluid For Combination To Oily Skin', 'Normal', 148.35, NULL, NULL, 'assets/products/sspf.jpg', 'https://www.nahdionline.com/en-sa/uriage-hyseac-spf-50-fluid-for-combination-to-oily-skin-50-ml/pdp/100788384', 'HYSÉAC Fluid SPF50+ combines effective sun protection and\nproperties specifically tailored to combination to oily skin. It offers very\nhigh-performance UVA-UVB protection thanks to a new highly effective\noctocrylene-free filtering complex. Its texture is non-sticky and non-greasy.', 1, 0, 0),
(26, 'Beauty Of Joseon Hanbang Serum Discovery Kit', 'Normal', 100.00, NULL, NULL, 'assets/products/pack.webp', 'https://www.nahdionline.com/en-sa/beauty-of-joseon-hanbang-serum-discovery-kit/pdp/102852204', '\nThis is a 10ml mini-size set with 4 types of serums so that you can choose to use or layer them according to your skin type or skin issues. Experience a wonderful journey to find the right product for you before taking the plunge on a full-size bottle!', 1, 0, 0),
(27, 'Bioderma Sebium Hydra Cream', 'Normal', 119.60, NULL, NULL, 'assets/products/rec26.jpg', 'https://www.nahdionline.com/en-sa/bioderma-sebium-hydra-moisturising-cream-40-ml/pdp/100999270', 'Sébium Hydra is specifically formulated to biologically counteract temporary side effects of dermatological treatments', 1, 1, 0),
(28, 'Neutrogena Hydro Boost Moisturizer', 'Normal', 51.70, NULL, NULL, 'assets/products/rec15.jpg', 'https://www.nahdionline.com/en-sa/neutrogena-hydro-boost-moisturizer-water-gel-normal-to-combination-skin-50-ml/pdp/101081081', 'Neutrogena Hydro Boost Water Gel quenches dry skin and keeps it looking smooth and so supple that your skin bounces back. The award-winning and best-selling water gel formula absorbs quickly and instantly delivers intense hydration.', 1, 1, 0),
(29, 'Anua Peach 70 Niacin Serum', 'Normal', 106.41, NULL, NULL, 'assets/products/anuaPeach.jpg', 'https://www.nahdionline.com/en-sa/anua-peach-70-niacin-serum-30ml/pdp/102970665', 'With its non-comedogenic formula and lightweight texture, this soothing powerhouse provides nourishing benefits and calming relief to redness, irritation and acne. Fast-absorbing and with no sticky texture, this light, gel ampoule also contains Panthenol, Betaine and Hyaluronic Acid for long-lasting, 3 layer hydration helping to achieve a balanced, dewy glow.', 0, 1, 0),
(48, 'Cerave Foaming Cleanser', 'Combo', 55.30, NULL, NULL, 'assets/products/rec13.jpg', 'https://www.nahdionline.com/en-sa/cerave-foaming-cleanser-for-normal-to-oily-skin-with-hyaluronic-acid-236-ml/pdp/101892677', 'A gel-to-foam wash that removes oil, dirt, and makeup without drying. Hydrates for 24 hours with 3 ceramides. Fragrance-free, dermatologist-developed, and ideal for normal to oily skin.', 0, 0, 0),
(49, 'La Roche-Posay Moisturizer', 'Combo', 166.90, NULL, NULL, 'assets/products/rec14.png', 'https://www.nahdionline.com/en-sa/la-roche-posay-tol-dermallergo-fluid-40ml/pdp/102719198?srsltid=AfmBOor3Dmx7xBZy6LR-jLK14c-hnIzyfI8VS7VCwKR2iGmL_MPf9OfG', 'Hydrates and soothes sensitive, allergy-prone, and oily skin for 48H. With prebiotic Sphingobioma and Neurosensine, it helps reduce irritation, itching, and redness while repairing the skin barrier. Hypoallergenic and suitable for daily use.', 0, 0, 0),
(50, 'Neutrogena Hydro Boost Moisturizer', 'Combo', 51.70, NULL, NULL, 'assets/products/rec15.jpg', 'https://www.nahdionline.com/en-sa/neutrogena-hydro-boost-moisturizer-water-gel-normal-to-combination-skin-50-ml/pdp/101081081?srsltid=AfmBOoqd9o8fQ7Vq-Ko1zAKehC-ZmF9BaKTVPphjSUXaXV1BZ-0qMfJb', 'Instantly hydrates and smooths skin with hyaluronic acid, keeping it supple and refreshed all day. Strengthens the moisture barrier with glycerin and olive extract. Oil-free, non-comedogenic, and safe for sensitive skin.', 0, 0, 0),
(51, 'Cosrx Aha/Bha Clarifying Toner', 'Combo', 151.90, NULL, NULL, 'assets/products/rec16.jpg', 'https://www.nahdionline.com/en-sa/103065721/pdp/103065721', 'Exfoliates, smooths, and softens skin while clearing congested pores. Balances oil production and boosts skin vitality with purifying botanical ingredients.', 0, 0, 0),
(52, 'Beauty Of Joseon Green Plum Cleanser', 'Combo', 64.00, NULL, NULL, 'assets/products/rec17.jpg', 'https://www.nahdionline.com/en-ae/beauty-of-joseon-green-plum-refreshing-cleanser/pdp/102852212', 'A hypoallergenic, slightly acidic gel that cleanses without drying. Infused with plum and mung bean extracts to refresh and hydrate sensitive skin.', 0, 0, 0),
(58, 'Axis-Y Mugwort Pore Clarifying Wash', 'Oily', 102.35, NULL, NULL, 'assets/products/rec18.png', 'https://www.nahdionline.com/en-sa/axis-y-mugwort-pore-clarifying-wash-off-pack-100ml/pdp/103174652', 'With 61% mugwort extract and kaolin, this cleanser clears pores, soothes irritation, and gently exfoliates. Hyaluronic acid hydrates without drying. Suitable for all skin types.', 0, 0, 0),
(59, 'Cosrx Salicylic Acid Cleanser', 'Oily', 71.92, NULL, NULL, 'assets/products/rec20.jpg', 'https://www.nahdionline.com/en-sa/102709846/pdp/102709846?srsltid=AfmBOop740D8zyxMAt8JHIh-QDLvQcri7vHSqbeecV7Wut1jaLupa5zw', 'Daily wash with 0.5% salicylic acid, tea tree oil, and willow bark. Clears oil and breakouts without stripping, leaving skin soft and clear.', 0, 0, 0),
(60, 'Beauty Of Joseon Ginseng Essence Water', 'Oily', 75.00, NULL, NULL, 'assets/products/rec21.jpg', 'https://www.nahdionline.com/en-sa/beauty-of-joseon-ginseng-essence-water/pdp/102852319', 'Made with 80% ginseng water, this essence deeply hydrates, nourishes, and revitalizes skin for lasting moisture and a healthy glow.', 0, 0, 0),
(61, 'Bioderma Sebium Hydra Cream', 'Oily', 119.60, NULL, NULL, 'assets/products/rec26.jpg', 'https://www.nahdionline.com/en-sa/bioderma-sebium-hydra-moisturising-cream-40-ml/pdp/100999270', 'Soothing moisturizer with glycerin, ceramides, and anti-irritants to hydrate, calm redness, and reduce tightness. Fluidactiv™ prevents clogged pores.', 0, 0, 0),
(62, 'Isntree Green Tea Fresh Toner', 'Oily', 79.98, NULL, NULL, 'assets/products/rec32.jpg', 'https://www.nahdionline.com/en-sa/isntree-green-tea-fresh-toner-200ml/pdp/102965575', 'Controls oil, purifies pores, and hydrates with green tea and Antisebum P, while Centella soothes the skin.', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `rating`, `review`, `created_at`, `product_name`) VALUES
(6, 'zainab', 5, 'I love how personalized the experience is on Glovia’s website. It helped me find the perfect cleanser and moisturizer for my sensitive skin.', '2025-04-23 16:46:58', 'Uriage Hyseac Cleansing Gel'),
(7, 'natali', 5, 'Thanks to Glovia, I stopped wasting money on products that don’t suit me. Their selections are thoughtful and spot-on for my needs.”', '2025-04-23 16:48:12', 'Dr. Althea 345 Intensive Repair Cream'),
(8, 'hashim', 4, 'Navigating Glovia was a breeze, and within minutes I found a serum that’s already improving my skin texture. Super impressed!', '2025-04-23 16:49:51', 'Garnier Vitamin C Serum');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT 0,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `is_verified`, `created_at`, `password`, `banned`, `is_admin`) VALUES
(1, 'demo@glovia.com', 1, '2025-06-01 12:00:00', '$2y$10$2RYql6Se07lrWYGVBIrXhO6hAnzBg8NPOlefXUpxziif8Che0O3Ne', 0, 0),
(2, 'admin@glovia.com', 1, '2025-06-01 12:00:00', '$2y$10$CHNv5tkcH64.wdD9Gv/J6utGndDWBtTjjS0aT50oTfKQeTfkK1AiO', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
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
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
--
-- Table structure for table `login_attempts` (brute-force protection)
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email_time` (`email`, `attempted_at`),
  KEY `idx_ip_time` (`ip`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
