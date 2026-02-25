-- =====================================================
-- Recommendation Service Database Migration
-- Hotel Nearby Recommendations Feature
-- =====================================================

-- Table: hotel_location
-- Stores the hotel's pinned location for recommendations
CREATE TABLE IF NOT EXISTS `hotel_location` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `latitude` DECIMAL(10, 8) NOT NULL,
  `longitude` DECIMAL(11, 8) NOT NULL,
  `label` VARCHAR(255) DEFAULT NULL,
  `updated_by` VARCHAR(100) DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default row (to be updated by admin)
INSERT INTO `hotel_location` (`id`, `latitude`, `longitude`, `label`, `updated_by`, `updated_at`) 
VALUES (1, 0.00000000, 0.00000000, NULL, NULL, NOW())
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Table: nationality_keywords
-- Maps nationalities to relevant cuisine/cultural keywords for recommendation boosting
CREATE TABLE IF NOT EXISTS `nationality_keywords` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nationality` VARCHAR(100) NOT NULL,
  `keywords` TEXT NOT NULL COMMENT 'JSON array of keywords',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nationality` (`nationality`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default nationality keyword mappings
INSERT INTO `nationality_keywords` (`nationality`, `keywords`) VALUES
('Lebanon', '["lebanese","shawarma","manakish","hummus","tabbouleh","kibbeh","falafel","mezze","baklava","fattoush"]'),
('Iraq', '["iraqi","mesopotamian","kebab","masgouf","dolma","biryani","kubba","samoon","kleicha"]'),
('Syria', '["syrian","damascus","kebab","shawarma","fattoush","muhammara","kibbeh","baklava","halawet el jibn"]'),
('Jordan', '["jordanian","mansaf","falafel","hummus","maqluba","knafeh","zarb"]'),
('Saudi Arabia', '["saudi","arabian","kabsa","mandi","shawarma","mutabbaq","saleeg","jareesh"]'),
('Kuwait', '["kuwaiti","machboos","harees","gabout","gers ogaily"]'),
('UAE', '["emirati","machboos","harees","luqaimat","balaleet","threed"]'),
('Egypt', '["egyptian","koshari","ful medames","molokhia","falafel","mahshi","shawarma","kunafa"]'),
('Iran', '["iranian","persian","kebab","tahdig","ghormeh sabzi","zereshk polo","fesenjan","ash reshteh"]'),
('Turkey', '["turkish","kebab","doner","pide","lahmacun","baklava","kofte","manti"]'),
('India', '["indian","curry","biryani","tandoori","naan","dal","samosa","masala","tikka"]'),
('Pakistan', '["pakistani","biryani","nihari","haleem","kebab","karahi","chapli"]'),
('Bangladesh', '["bangladeshi","bengali","biryani","hilsa","bhuna","korma","pitha"]'),
('Philippines', '["filipino","adobo","sinigang","lechon","lumpia","kare-kare","pancit"]'),
('Indonesia', '["indonesian","nasi goreng","satay","rendang","gado-gado","bakso","soto"]'),
('Malaysia', '["malaysian","nasi lemak","satay","laksa","rendang","roti canai","char kway teow"]'),
('China', '["chinese","dim sum","noodles","dumpling","kung pao","peking","wonton","szechuan"]'),
('Japan', '["japanese","sushi","ramen","tempura","udon","teriyaki","yakitori","tonkatsu"]'),
('Korea', '["korean","kimchi","bibimbap","bulgogi","samgyeopsal","japchae","tteokbokki"]'),
('Thailand', '["thai","pad thai","tom yum","green curry","som tam","massaman","satay"]'),
('Vietnam', '["vietnamese","pho","banh mi","spring roll","bun cha","com tam"]'),
('France', '["french","bistro","croissant","baguette","crepe","coq au vin","bouillabaisse"]'),
('Italy', '["italian","pizza","pasta","risotto","gelato","tiramisu","lasagna","ravioli"]'),
('Germany', '["german","bratwurst","schnitzel","pretzel","sauerkraut","currywurst"]'),
('USA', '["american","burger","bbq","steak","hot dog","pancakes","fried chicken"]'),
('UK', '["british","fish and chips","roast","pie","english breakfast","pudding"]'),
('Mexico', '["mexican","taco","burrito","enchilada","quesadilla","guacamole","nachos"]'),
('Brazil', '["brazilian","churrasco","feijoada","pao de queijo","coxinha","acai"]'),
('Russia', '["russian","borscht","pelmeni","blini","beef stroganoff","piroshki"]'),
('Afghanistan', '["afghan","kabuli palaw","mantu","ashak","chapli kebab","bolani"]')
ON DUPLICATE KEY UPDATE `keywords` = VALUES(`keywords`);

-- Table: recommendation_logs (optional - for analytics)
CREATE TABLE IF NOT EXISTS `recommendation_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `guest_id` INT(11) DEFAULT NULL,
  `category` VARCHAR(50) NOT NULL,
  `nationality_used` VARCHAR(100) DEFAULT NULL,
  `place_name` VARCHAR(255) DEFAULT NULL,
  `place_lat` DECIMAL(10, 8) DEFAULT NULL,
  `place_lng` DECIMAL(11, 8) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_guest_id` (`guest_id`),
  KEY `idx_category` (`category`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add index on nationality in real_guests table if not exists
-- ALTER TABLE `real_guests` ADD INDEX `idx_nationality` (`nationality`);
-- ALTER TABLE `real_guests` ADD INDEX `idx_passport_number` (`passport_number`);
-- ALTER TABLE `real_guests` ADD INDEX `idx_full_name` (`full_name`);
