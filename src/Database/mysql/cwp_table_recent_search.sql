DROP TABLE IF EXISTS recent_search;
CREATE TABLE IF NOT EXISTS `recent_search` (
    `search_id` int NOT NULL,
    `search_query` varchar(300) NOT NULL,
    `search_table` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `recent_search`  ADD PRIMARY KEY (`search_id`);
ALTER TABLE `recent_search`  MODIFY `search_id` int NOT NULL AUTO_INCREMENT;
