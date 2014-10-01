
CREATE DATABASE IF NOT EXISTS `zsql`;

GRANT USAGE ON *.* TO 'zsql'@'localhost';
DROP USER 'zsql'@'localhost';

CREATE USER 'zsql'@'localhost' IDENTIFIED BY 'nopass';
GRANT ALL ON zsql.* TO 'zsql'@'localhost';

DROP TABLE IF EXISTS `zsql`.`fixture1`;
CREATE TABLE `zsql`.`fixture1` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `varchar` varchar(255) NOT NULL default '',
  `double` double NOT NULL default '0',
  `null` tinyint(1) default NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `zsql`.`fixture1` VALUES
(1, 'test', 2.14, NULL),
(2, 'blah', 343434.14, 1);

DROP TABLE IF EXISTS `zsql`.`fixture2`;
CREATE TABLE `zsql`.`fixture2` LIKE `zsql`.`fixture1`;
INSERT `zsql`.`fixture2` SELECT * FROM `zsql`.`fixture1`;

DROP TABLE IF EXISTS `zsql`.`migrations`;
CREATE TABLE `zsql`.`migrations` (
  `version` bigint(20) unsigned NOT NULL,
  `name` varchar(255) default NULL,
  `state` enum('initial', 'failed', 'failed-down', 'success') NOT NULL default 'initial',
  PRIMARY KEY (`version`),
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
