DROP TABLE `criminal`;
DROP TABLE `test`;
DROP TABLE `temp_path`;
DROP TABLE `temp_ip`;
DROP TABLE `temp_created`;

CREATE TABLE `plans` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `plan` longblob
);