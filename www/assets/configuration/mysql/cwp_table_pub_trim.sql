DROP TABLE IF EXISTS pub_trim;
CREATE TABLE IF NOT EXISTS `pub_trim` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pub_name` text NOT NULL,
  `bind` text NOT NULL,
  `head_trim` text,
  `foot_trim` text,
  `delivered_size` text,
  `face_trim` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);