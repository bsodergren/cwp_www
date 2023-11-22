CREATE TABLE IF NOT EXISTS  `job_destination` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `job_destination`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `job_destination`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;