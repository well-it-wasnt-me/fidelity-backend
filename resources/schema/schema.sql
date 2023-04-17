CREATE TABLE `access_log` (
                            `access_id` int NOT NULL,
                            `user_id` int NOT NULL,
                            `ip` varchar(100) NOT NULL,
                            `browser` text NOT NULL,
                            `os` text NOT NULL,
                            `location` text NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `categories` (
  `cat_id` int NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) DEFAULT NULL,
  `cat_descr` text,
  `cat_picture` text,
  `is_active` int DEFAULT '1',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `offers` (
  `sale_id` int NOT NULL AUTO_INCREMENT,
  `start_from` datetime DEFAULT NULL,
  `end_on` datetime DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `type_discount` int DEFAULT '0' COMMENT '0 = % 1 = Money',
  `amount_discount` int DEFAULT NULL,
  `quantity_available` int DEFAULT NULL,
  PRIMARY KEY (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `points` (
  `point_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `amount_point` int DEFAULT NULL,
  `reason` text,
  `date_assignation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`point_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `prizes_categories` (
  `p_cat_id` int NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) DEFAULT NULL,
  `cat_descr` text,
  `cat_picture` text,
  `is_active` int DEFAULT '1',
  PRIMARY KEY (`p_cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `prizes_product` (
  `p_prod_id` int NOT NULL AUTO_INCREMENT,
  `prize_name` varchar(100) DEFAULT NULL,
  `prize_descr` text,
  `prize_picture` text,
  `prize_points` int DEFAULT NULL,
  `prize_quantity` int DEFAULT NULL,
  `prize_cat_id` int DEFAULT NULL,
  `is_active` int DEFAULT '1',
  PRIMARY KEY (`p_prod_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `products` (
  `prod_id` int NOT NULL AUTO_INCREMENT,
  `prod_name` varchar(100) DEFAULT NULL,
  `prod_descr` text,
  `prod_picture` text,
  `prod_price` int DEFAULT NULL,
  `cat_id` int DEFAULT NULL,
  `is_active` int DEFAULT '1',
  `quantity` int DEFAULT NULL,
  PRIMARY KEY (`prod_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `receipt_elements` (
  `rec_el_id` int NOT NULL AUTO_INCREMENT,
  `recipt_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  PRIMARY KEY (`rec_el_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `receipts` (
  `rec_id` int NOT NULL AUTO_INCREMENT,
  `recipt_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `recipt_status` varchar(100) DEFAULT NULL,
  `recipt_amount` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`rec_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `settings` (
  `sett_id` int NOT NULL AUTO_INCREMENT,
  `money_to_point` int DEFAULT NULL,
  `point_for_registration` int DEFAULT '1',
  `currency` varchar(10) DEFAULT '€',
  PRIMARY KEY (`sett_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `f_name` varchar(45) DEFAULT NULL,
  `l_name` varchar(75) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `account_status` int DEFAULT '0',
  `account_role` int DEFAULT '1',
  `creation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `locale` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO fidelity.users (user_id, f_name, l_name, email, password, account_status, account_role, creation_date, locale, full_addr, phone_number) VALUES (31, 'Admin', 'Admin', 'admin', '3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2', 1, 1, '2023-03-25 19:16:24', 'en_EN', null, null);
