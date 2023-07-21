DROP TABLE IF EXISTS form_data_count;
CREATE TABLE IF NOT EXISTS `form_data_count` (
	`id` int  NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`form_id`	int DEFAULT NULL,
	`job_id`	int DEFAULT NULL,
	`packaging`	varchar(50) NOT NULL,
	`full_boxes`	int DEFAULT NULL,
	`layers_last_box`	int DEFAULT NULL,
	`lifts_last_layer`	int DEFAULT NULL,
	`lift_size`	int DEFAULT NULL,
	`lifts_per_layer`	int DEFAULT NULL,
	`form_number`	varchar(50) DEFAULT NULL,
	`count`	int DEFAULT NULL,
	`layers_per_skid`	int DEFAULT NULL,
	`market`	varchar(50) DEFAULT NULL,
	`former`	varchar(50) DEFAULT NULL
);
