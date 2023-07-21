DROP TABLE IF EXISTS media_forms;
CREATE TABLE IF NOT EXISTS `media_forms` (
  `id` integer  NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`job_id`	int NOT NULL,
	`form_number`	int NOT NULL,
	`product`	varchar(26) NOT NULL,
	`count`	int NOT NULL,
	`bind`	varchar(4) NOT NULL,
	`config`	varchar(20) NOT NULL
);
