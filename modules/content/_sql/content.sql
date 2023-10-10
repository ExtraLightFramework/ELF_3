DROP TABLE IF EXISTS `%%prefix%%content`;
---sql stmt---
CREATE TABLE `%%prefix%%content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `type` enum('rubric','item') DEFAULT 'item',
  `content_type` varchar(20) NOT NULL DEFAULT '',
  `content_order_type` varchar(255) NOT NULL DEFAULT '',
  `visible` int(1) unsigned NOT NULL DEFAULT 1,
  `first_p` text DEFAULT NULL,
  `text` mediumtext NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `picture_alt` varchar(70) NOT NULL DEFAULT '' COMMENT 'альтернативный текст картинки',
  `picture_w` int(11) unsigned NOT NULL DEFAULT 0,
  `picture_h` int(11) unsigned NOT NULL DEFAULT 0,
  `picture_ornt` enum('horizontal','vertical','square') NOT NULL DEFAULT 'horizontal',
  `tm` int(11) unsigned NOT NULL DEFAULT 0,
  `tm_edit` int(11) unsigned NOT NULL DEFAULT 0,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  `hot` int(1) unsigned NOT NULL DEFAULT 0,
  `hash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aliasIdx` (`alias`),
  KEY `tmIdx` (`tm`),
  KEY `hotIdx` (`hot`),
  KEY `typeIdx` (`type`,`visible`),
  KEY `parentIdx` (`parent_id`,`type`),
  KEY `posIdx` (`parent_id`,`pos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('CONTENT_TYPE_PAGE','page','Статическая страница');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('CONTENT_TYPE_NEWS','news','Новости');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('CONTENT_TYPE_DOC','doc','Документ');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('CONTENT_TYPE_FAQ','faq','Вопросы-ответы');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('CONTENT_TYPE_VIDEO','video','Видеоматериал');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('CONTENT_TYPE_REVIEWS','reviews','Отзывы');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('CONTENT_TYPE_ARTICLE','article','Статьи');
