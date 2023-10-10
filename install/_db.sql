DROP TABLE IF EXISTS %%prefix%%users;
---sql stmt---
CREATE TABLE `%%prefix%%users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL DEFAULT '',
  `passwd` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `group` int(11) unsigned NOT NULL DEFAULT '1',
  `restore_code` varchar(32) DEFAULT NULL,
  `auto_login_hash` varchar(32) DEFAULT NULL,
  `cur_ip` varchar(32) DEFAULT NULL,
  `tm_reg` int(11) unsigned NOT NULL DEFAULT '0',
  `tm_last` int(11) unsigned NOT NULL DEFAULT '0',
  `last_ip` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxLogin` (`login`),
  UNIQUE KEY `idxEmail` (`email`),
  UNIQUE KEY `secIdx` (`restore_code`),
  KEY `alhIdx` (`auto_login_hash`),
  KEY `tmregIdx` (`tm_reg`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='пользователи системы';
---sql stmt---
INSERT INTO %%prefix%%users (`login`,`passwd`,`name`,`email`,`group`,`tm_reg`) VALUES('%%admin_login%%',md5('%%admin_passwd%%'),'Администратор','%%service_emails%%',7,%%time%%);
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%settings;
---sql stmt---
CREATE TABLE `%%prefix%%settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(1024) NOT NULL DEFAULT '',
  `desc` varchar(255) DEFAULT NULL,
  `expire` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nameIdx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='установки системы';
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('TIME_ZONE','%%time_zone%%','Временная зона');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('FULL_SYSTEM_LANGUAGE','%%full_system_language%%','Язык системы');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('SYSTEM_LANGUAGE','%%system_language%%','Язык системы (кратко)');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('SYSTEM_AUTO_LOGIN',%%system_auto_login%%,'Поддержка системного автологина');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('DEBUG_MODE',%%debug_mode%%,'Отладочный режим');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('AUTO_LOGIN_ENABLED',%%auto_login_enabled%%,'Автоматическая авторизация пользователей');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('COOKIE_AGREEMENT_ENABLED',%%cookie_agreement_enabled%%,'Поддержка пользовательского соглашения по использованию файлов cookie');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('PAGI_MAX_LINKS_CNT',%%pagi_max_links%%,'Максимальное кол-во ссылок в пагинаторе');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('MAX_IMAGE_SIZE',%%max_image_size%%,'Максимальный размер загружаемого изображения');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('REAL_SEND_MAIL',%%real_send_mail%%,'Реальная отправка почты');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('ADMIN_PASS',md5('admin'),'Дополнительный пароль администратора');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('RECS_ON_PAGE',10,'Количество записей на странице');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('YANDEX_COUNTER','','Номер счетчика Яндекс.Метрики');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('GOOGLE_TAG_MANAGER','','Номер счетчика от Google');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('YANDEX_VERIFICATION','','Код верификации от Yandex');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('GOOGLE_VERIFICATION','','Код верификации от Google');
---sql stmt---
INSERT INTO %%prefix%%settings (`name`,`value`,`desc`) VALUES('REDIRECTOR_ENABLED',0,'Вкл./выкл. механизма переадресации ссылок со старого сайта');
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%redirector;
---sql stmt---
CREATE TABLE `%%prefix%%redirector` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `request_uri` varchar(4096) DEFAULT NULL,
  `redirect_to` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_uri` (`request_uri`,`redirect_to`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%entry_points;
---sql stmt---
CREATE TABLE `%%prefix%%entry_points` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(3) unsigned NOT NULL DEFAULT 0,
  `fixed` int(1) unsigned NOT NULL DEFAULT 0,
  `request_uri` varchar(4096) NOT NULL DEFAULT '',
  `referer` varchar(255) NOT NULL DEFAULT '',
  `get_params` varchar(1024) NOT NULL DEFAULT '',
  `tm` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_uri` (`request_uri`,`referer`) USING HASH,
  KEY `code` (`code`,`fixed`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%routing;
---sql stmt---
CREATE TABLE `%%prefix%%routing` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `controller_to` varchar(255) NOT NULL DEFAULT '',
  `method_to` varchar(255) NOT NULL DEFAULT '',
  `params_to` varchar(2048) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `canonical` varchar(255) DEFAULT NULL,
  `tm` int(11) unsigned NOT NULL DEFAULT '0',
  `is_last` int(1) unsigned NOT NULL DEFAULT '0',
  `hash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commIdx` (`controller`,`method`),
  UNIQUE KEY `hashIdx` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='системный роутинг';
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%history;
---sql stmt---
CREATE TABLE `%%prefix%%history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` enum('new','showed','system') NOT NULL DEFAULT 'new',
  `mess` varchar(512) DEFAULT NULL,
  `ip_addr` varchar(32) DEFAULT NULL,
  `tm` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userIdx` (`user_id`,`status`,`tm`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%tags_content;
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%tags;
---sql stmt---
CREATE TABLE `%%prefix%%tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `htag` varchar(255) DEFAULT NULL,
  `freq` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'частотность',
  PRIMARY KEY (`id`),
  UNIQUE KEY `htagIdx` (`htag`),
  KEY `freqIdx` (`freq`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='хэштеги системы';
---sql stmt---
CREATE TABLE `%%prefix%%tags_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned DEFAULT NULL,
  `content_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commIdx` (`tag_id`,`content_id`),
  CONSTRAINT `%%prefix%%tags_content_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `%%prefix%%tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%elf_forms_related;
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%elf_forms_fields;
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%elf_forms;
---sql stmt---
DROP TABLE IF EXISTS %%prefix%%sessions;
---sql stmt---
CREATE TABLE `%%prefix%%sessions` (
  `sessid` varchar(32) NOT NULL,
  `tm_created` int(11) unsigned NOT NULL DEFAULT 0,
  `tm_updated` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`sessid`),
  KEY `tm_updated` (`tm_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='системные сессии';
---sql stmt---
CREATE TABLE `%%prefix%%elf_forms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `fullpath` varchar(255) DEFAULT NULL,
  `table` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `method` enum('post','get') NOT NULL DEFAULT 'post',
  `getter` varchar(100) DEFAULT NULL,
  `getter_variable_name` varchar(20) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `redirect` varchar(512) DEFAULT NULL,
  `ajax_request` int(1) unsigned NOT NULL DEFAULT 0,
  `js_callback` varchar(100) DEFAULT NULL,
  `lang` varchar(50) DEFAULT NULL COMMENT 'название языкового файла для формы',
  `script` mediumblob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Таблица настроек системных представлений';
---sql stmt---
CREATE TABLE `%%prefix%%elf_forms_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) unsigned NOT NULL DEFAULT 0,
  `table_name` varchar(50) NOT NULL DEFAULT '',
  `field_name` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `type` enum('string','hidden','eval','text','int','float','link','select_simple','select_enum','checkbox','radio','date','password','h','hm','hms','wysiwyg','picture') NOT NULL DEFAULT 'string',
  `params` blob DEFAULT NULL,
  `size` blob DEFAULT NULL,
  `default_value` varchar(50) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `required` int(1) unsigned NOT NULL DEFAULT 0,
  `placeholder` varchar(50) NOT NULL DEFAULT '',
  `pattern` varchar(100) DEFAULT NULL,
  `autocomplete` int(1) unsigned NOT NULL DEFAULT 0,
  `show_in_related_data` int(1) unsigned NOT NULL DEFAULT 1,
  `sessid` varchar(32) DEFAULT NULL,
  `pos` int(2) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `sessid` (`sessid`),
  KEY `pos` (`form_id`,`table_name`,`sessid`,`pos`),
  CONSTRAINT `%%prefix%%elf_forms_fields_ibfk_1` FOREIGN KEY (`sessid`) REFERENCES `%%prefix%%sessions` (`sessid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `%%prefix%%forms_fields_ibfk_2` FOREIGN KEY (`form_id`) REFERENCES `%%prefix%%elf_forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
---sql stmt---
CREATE TABLE `%%prefix%%elf_forms_related` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `master_id` int(11) unsigned DEFAULT NULL,
  `slave_id` int(11) unsigned DEFAULT NULL,
  `relation_type` enum('single','multi','widemulti') NOT NULL DEFAULT 'single',
  `master_field` varchar(50) NOT NULL DEFAULT '',
  `slave_field` varchar(50) NOT NULL DEFAULT '',
  `rule` blob DEFAULT NULL,
  `sessid` varchar(32) DEFAULT NULL,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_id` (`master_id`,`slave_id`),
  KEY `sessid` (`sessid`),
  KEY `slave_id` (`slave_id`),
  KEY `master_id_2` (`master_id`,`pos`),
  CONSTRAINT `%%prefix%%elf_forms_related_ibfk_1` FOREIGN KEY (`sessid`) REFERENCES `%%prefix%%sessions` (`sessid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `%%prefix%%elf_forms_related_ibfk_2` FOREIGN KEY (`master_id`) REFERENCES `%%prefix%%elf_forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `%%prefix%%elf_forms_related_ibfk_3` FOREIGN KEY (`slave_id`) REFERENCES `%%prefix%%elf_forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
---sql stmt---
CREATE TABLE `%%prefix%%files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` enum('folder','video','file','image') DEFAULT NULL,
  `fname` varchar(512) DEFAULT NULL,
  `name` varchar(512) DEFAULT NULL,
  `ext` varchar(20) NOT NULL DEFAULT '' COMMENT 'расширение файла',
  `metadata` text DEFAULT NULL,
  `tm` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fname` (`parent_id`,`fname`),
  KEY `parent_id` (`type`,`name`,`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Файлы контента/каталога';
