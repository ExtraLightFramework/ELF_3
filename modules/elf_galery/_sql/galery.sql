DROP TABLE IF EXISTS `%%prefix%%galery`;
---sql stmt---
CREATE TABLE `%%prefix%%galery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT 'имя файла картинки',
  `title` varchar(255) DEFAULT NULL COMMENT 'имя картинки, SEO, alt',
  `type` enum('common','animals','auto','moto') NOT NULL DEFAULT 'common' COMMENT 'условная тема галереи. укажите свои',
  `orient` enum('horizontal','vertical','square') NOT NULL DEFAULT 'horizontal' COMMENT 'ориентация изображения',
  `wh_coof` float(10,8) unsigned NOT NULL DEFAULT '1.00000000' COMMENT 'коэффициент соотношения ширины и высоты',
  `pos` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `commIdx` (`pos`,`type`),
  KEY `nameIdx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
