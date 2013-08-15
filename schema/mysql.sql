# Dump of table social
# ------------------------------------------------------------

DROP TABLE IF EXISTS `social`;

CREATE TABLE `social` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `service` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `uid` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `end_of_life` int(11) DEFAULT NULL,
  `refresh_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_token_secret` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extra_params` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_service_uid_unique` (`service`,`uid`),
  UNIQUE KEY `social_user_id_service_unique` (`user_id`,`service`),
  UNIQUE KEY `social_service_access_token_unique` (`service`,`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
