DROP TABLE IF EXISTS paper_type;
CREATE TABLE IF NOT EXISTS `paper_type` (
  `id` integer  NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`paper_wieght`	int NOT NULL,
	`paper_size`	varchar(20) NOT NULL,
	`pages`	int NOT NULL
);
