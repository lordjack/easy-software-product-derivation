# Host: localhost  (Version 5.7.14)
# Date: 2018-02-10 18:31:28
# Generator: MySQL-Front 6.0  (Build 1.157)


#
# Structure for table "feature"
#

DROP TABLE IF EXISTS `feature`;
CREATE TABLE `feature` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `module_id` bigint(20) NOT NULL,
  `title` varchar(120) COLLATE utf8_bin NOT NULL,
  `name` varchar(80) COLLATE utf8_bin NOT NULL,
  `type` varchar(40) COLLATE utf8_bin NOT NULL,
  `feature_id` bigint(20) NOT NULL,
  `level` bigint(20) NOT NULL,
  `group` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feature_feature_fk` (`feature_id`),
  KEY `module_fk` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

#
# Structure for table "module"
#

DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  `title` varchar(100) COLLATE utf8_bin NOT NULL,
  `description` varchar(150) COLLATE utf8_bin NOT NULL,
  `imagem` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id_fk` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

#
# Structure for table "product"
#

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  `description` varchar(150) COLLATE utf8_bin NOT NULL,
  `logo` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

