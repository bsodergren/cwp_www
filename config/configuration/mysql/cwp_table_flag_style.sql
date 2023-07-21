DROP TABLE IF EXISTS flag_style;
CREATE TABLE IF NOT EXISTS `flag_style` (
  `id` integer  NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`style_name`	varchar(150) NOT NULL,
	`ecol`	varchar(5) DEFAULT NULL,
	`erow`	varchar(3) DEFAULT NULL,
	`text`	varchar(50) DEFAULT NULL,
	`bold`	int DEFAULT NULL,
	`font_size`	int DEFAULT NULL,
	`h_align`	int DEFAULT NULL,
	`v_align`	int DEFAULT NULL,
	`width`	int DEFAULT NULL
);
--
-- Dumping data for table `flag_style`
--
