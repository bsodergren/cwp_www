CREATE TABLE IF NOT EXISTS `email_list` (
  `id` integer  NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(70) NOT NULL,
  `name` varchar(70) NOT NULL
);