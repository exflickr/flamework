DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `deleted` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `password` char(64) DEFAULT NULL,
  `conf_code` char(24) DEFAULT NULL,
  `confirmed` int(10) unsigned NOT NULL,
  `cluster_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `by_email` (`email`),
  UNIQUE KEY `by_username` (`username`,`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users_password_reset`;

CREATE TABLE `users_password_reset` (
  `user_id` int(10) unsigned NOT NULL,
  `reset_code` char(32) DEFAULT NULL,
  `created` int(10) unsigned NOT NULL,
  UNIQUE KEY `by_code` (`reset_code`),
  KEY `by_user` (`user_id`),
  KEY `by_timestamp` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
