<?php
/**
 * CWP Media tool for load flags
 */

$alter_table = [
   'form_data' => ['ADD' => ['unique','original']]
];

// ALTER TABLE `form_data` ADD UNIQUE(`original`);
