DROP TABLE IF EXISTS settings;
CREATE TABLE IF NOT EXISTS `settings` (
	  `id` integer  NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`setting_name` TEXT NOT NULL,
	`setting_value`	TEXT DEFAULT NULL,
	`setting_type`	TEXT NOT NULL
);
