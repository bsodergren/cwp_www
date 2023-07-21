DROP TABLE IF EXISTS media_job;
CREATE TABLE IF NOT EXISTS `media_job` (
  `job_id` int  NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`job_number`	int NOT NULL,
	`pdf_file`	varchar(200) NOT NULL,
	`zip_file`	varchar(300) DEFAULT NULL,
	`xlsx_dir`	varchar(300) DEFAULT NULL,
	`close`	varchar(70) DEFAULT NULL,
	`hidden`	int NOT NULL DEFAULT '0'
);

ALTER TABLE `media_job`  ADD UNIQUE KEY `id` (`job_id`),  ADD KEY `job_id` (`job_id`);