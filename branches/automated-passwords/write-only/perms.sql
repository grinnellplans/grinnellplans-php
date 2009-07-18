create table perms (
  `userid` smallint(5) unsigned NOT NULL auto_increment,
  `status` smallint(5) NOT NULL 
)

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `username` varchar(16) NOT NULL default '',
  `created` datetime default NULL,
  `password` varchar(20) default NULL,
  `email` varchar(64) default NULL,
  `pseudo` varchar(64) default NULL,
  `login` datetime default NULL,
  `changed` datetime default NULL,
  `plan` text,
  `poll` tinyint(3) unsigned default NULL,
  `group_bit` char(1) default NULL,
  `spec_message` varchar(255) default NULL,
  `grad_year` varchar(4) default NULL,
  `edit_cols` tinyint(3) unsigned default NULL,
  `edit_rows` tinyint(3) unsigned default NULL,
  `webview` char(1) default '0',
  `edit_text` text,
  `notes_asc` char(1) default NULL,
  `user_type` varchar(128) default NULL,
  `show_images` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `username` (`username`),
  KEY `changed` (`changed`),
  FULLTEXT KEY `password` (`password`),
  FULLTEXT KEY `password_2` (`password`),
  FULLTEXT KEY `username_2` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

