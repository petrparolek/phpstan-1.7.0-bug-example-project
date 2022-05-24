SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `register_date` datetime DEFAULT NULL,
  `last_visit_date` datetime DEFAULT NULL,
  `recovery_token` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `register_date`, `last_visit_date`, `recovery_token`) VALUES
(2,	'admin',	'$2y$10$6puNwzWCf400q59AmOCu4..1eMuOuBTl7/4.UJVcHhr11NfYtM50S',	'admin@example.com',	'admin',	'2019-09-05 23:55:01',	'2022-05-24 16:49:20',	NULL)
