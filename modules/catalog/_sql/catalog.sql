DROP TABLE IF EXISTS `%%prefix%%catalog_features_values`
---sql stmt---
DROP TABLE IF EXISTS `%%prefix%%catalog_pictures`
---sql stmt---
DROP TABLE IF EXISTS `%%prefix%%catalog`
---sql stmt---
DROP TABLE IF EXISTS `%%prefix%%catalog_units`
---sql stmt---
DROP TABLE IF EXISTS `%%prefix%%catalog_features`
---sql stmt---
CREATE TABLE `%%prefix%%catalog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` enum('rubric','item','clone') NOT NULL DEFAULT 'rubric',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'название,оно же title',
  `inner_name` varchar(255) DEFAULT NULL COMMENT 'для SEO оптимизации, заменяет title из роутинга',
  `alias` varchar(255) DEFAULT NULL,
  `price` float(12,2) unsigned NOT NULL DEFAULT 0.00 COMMENT 'стоимость',
  `quantity` int(11) unsigned NOT NULL DEFAULT 1 COMMENT 'колчество в наличии',
  `unit_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'единица измерения',
  `desc` mediumtext DEFAULT NULL COMMENT 'описание на сайте',
  `pos` int(4) unsigned NOT NULL DEFAULT 0,
  `clone_id` int(11) DEFAULT NULL,
  `url_donor` varchar(512) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aliasIdx` (`alias`),
  KEY `commIdx` (`type`,`parent_id`),
  KEY `nameIdx` (`name`),
  KEY `posIdx` (`pos`),
  KEY `cloneIdx` (`clone_id`),
  KEY `urldonorIdx` (`url_donor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
---sql stmt---
CREATE TABLE `%%prefix%%catalog_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(50) DEFAULT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `short_name` varchar(30) DEFAULT NULL,
  `type` enum('simple','textarea','wysiwyg','select','radio','checkbox') NOT NULL DEFAULT 'simple',
  `desc` varchar(100) DEFAULT NULL,
  `prevalues` text DEFAULT NULL COMMENT 'предустановленные значения, список значений',
  PRIMARY KEY (`id`),
  UNIQUE KEY `commIdx` (`name`),
  UNIQUE KEY `aliasIdx` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='единицы измерения (справочник)';
---sql stmt---
INSERT INTO `%%prefix%%catalog_units` VALUES (1,'shtuki','Штуки','шт.','simple','штуки/штука',NULL),
										(2,'metry','Метры','м.','simple','метры',NULL),
										(3,'kilogrammy','Килограммы','кг','simple','килограммы',NULL),
										(4,'millimetry','Миллиметры','мм.','simple','миллиметры',NULL),
										(5,'dyuymy','Дюймы','``','simple','дюймы',NULL),
										(6,'vybor-iz-spiska-danet','Выбор из списка да/нет',NULL,'select','выбор значения да/нет из списка','да\r\nнет'),
										(7,'vybor-estynet','Выбор есть/нет',NULL,'radio','выбор значения из вариантов (есть/нет)','есть\r\nнет'),
										(8,'chekboks-danet','Чекбокс да/нет',NULL,'checkbox','установка значения флажком (да/нет)','да\r\nнет'),
										(9,'tsvet','Цвет',NULL,'select','выбор цвета из списка','белый\r\nчерный\r\nкрасный\r\nзеленый\r\nголубой\r\nсиний\r\nсветло-голубой\r\nсеребристый\r\nзолотистый\r\nсерый\r\nрозовый'),
										(10,'vatty','Ватты','Вт','simple','ватты',NULL),
										(11,'kilovatty','Киловатты','кВт','simple','киловатты',NULL),
										(12,'santimetry','Сантиметры','см','simple','сантиметры',NULL),
										(13,'tip-kompyyutera','Тип компьютера',NULL,'select','выбор из списка типа устройства компьютера','Ноутбук\r\nУльтрабук\r\nНетбук\r\nСтационарный\r\nМоноблок'),
										(14,'operatsionnaya-sistema','Операционная система',NULL,'select','выбор из списка типа операционной системы компьютера','Не установлена\r\nLinux\r\nUnix\r\nWindows 10 Professional\r\nWindows 10 Home'),
										(15,'tekstovoe-pole','Текстовое поле',NULL,'simple','Текстовое поле, принимающее любое значение',NULL),
										(16,'tehnologiya-zhk','Технология ЖК',NULL,'select','технологии изготовления матриц жидкокристаллических дисплеев','IPS\r\nTN+film\r\n*VA');
---sql stmt---
CREATE TABLE `%%prefix%%catalog_pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'SEO описание картинки, имя картинки',
  `orient` enum('horizontal','vertical','square') NOT NULL DEFAULT 'horizontal',
  `wh_coof` float(10,8) unsigned NOT NULL DEFAULT 1.00000000,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `posIdx` (`pos`),
  KEY `cidIdx` (`catalog_id`,`name`),
  CONSTRAINT `%%prefix%%catalog_pictures_ibfk_1` FOREIGN KEY (`catalog_id`) REFERENCES `%%prefix%%catalog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
---sql stmt---
CREATE TABLE `%%prefix%%catalog_features` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('feature','group') NOT NULL DEFAULT 'feature',
  `group_id` int(11) NOT NULL DEFAULT 0,
  `unit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  `desc` varchar(1024) DEFAULT NULL,
  `pos` int(2) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `grpIdx` (`group_id`),
  KEY `nameIdx` (`name`),
  KEY `typeIdx` (`type`,`pos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
---sql stmt---
CREATE TABLE `%%prefix%%catalog_features_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) unsigned NOT NULL DEFAULT 0,
  `feature_id` int(11) unsigned NOT NULL DEFAULT 0,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commIdx` (`catalog_id`,`feature_id`),
  KEY `cidIdx` (`catalog_id`),
  KEY `fidIdx` (`feature_id`),
  CONSTRAINT `%%prefix%%catalog_features_values_ibfk_1` FOREIGN KEY (`catalog_id`) REFERENCES `%%prefix%%catalog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `%%prefix%%catalog_features_values_ibfk_2` FOREIGN KEY (`feature_id`) REFERENCES `%%prefix%%catalog_features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;