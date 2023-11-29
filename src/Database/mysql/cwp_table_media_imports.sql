CREATE TABLE `media_imports` (
    `id` int(11) NOT NULL COMMENT 'Primary Key',
    `pdf_file` varchar(255) DEFAULT NULL,
    `job_number` varchar(255) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  ALTER TABLE `media_imports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pdf_file` (`pdf_file`);