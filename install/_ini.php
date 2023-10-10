<?php defined('_VALID_CODE') or die('Access denied');

#Time constants
define ('SECONDS_IN_HOUR',	3600);
define ('SECONDS_IN_DAY',	86400);
define ('SECONDS_IN_WEEK',	604800);
define ('SECONDS_IN_MONTH',	2592000);
define ('SECONDS_IN_YEAR',	31536000);
define ('XML_STRING', 		'XMLHttpRequest');

define ('DIR_ALIAS',		'<% ini.diralias %>');
define ('ROOTPATH', str_replace("\\","/",realpath(__DIR__).'/'));
define ('EXT',				'.php');
define ('SITE',				'<% ini.domain %>'.DIR_ALIAS);
define ('SITE_PROTOCOL',	'<% ini.protocol %>');
define ('SYSTEM_LANGUAGE',	'ru');
define ('APP_DIR',			'app');
define ('INTERFACES_DIR',	'interfaces');
define ('CONTROLLERS_DIR',	'controllers');
define ('MODELS_DIR',		'models');
define ('VIEWS_DIR',		'views');
define ('LIBS_DIR',			'libs');
define ('LANGS_DIR',		'languages');
define ('DEFAULT_CONTROLLER','Main');
define ('DEFAULT_METHOD',	'index');
define ('METHOD_404',		'_404');
define ('DEFAULT_LAYOUT',	'layout');
define ('MAX_IMAGE_SIZE',	5000000);

define ('COOKIE_EXPIRE',	SECONDS_IN_YEAR*5);
define ('SESSION_EXPIRE',	3600);
define ('ALH_EXPIRE',		SECONDS_IN_YEAR);
define ('SESSION_PATH',		__DIR__.'/logs/sessions/');

define ('DB_HOST',	'<% ini.db.host %>');
define ('DB_BASE',	'<% ini.db.base %>');
define ('DB_USER',	'<% ini.db.user %>');
define ('DB_PASS',	'<% ini.db.pass %>');
define ('DB_PREFIX','<% ini.db.prefix %>');

define ('GROUP_USER',	1);
define ('GROUP_ADMIN',	2);
define ('GROUP_TECH',	4);

// System settings
define ('SECRET_WORD',		'TybjVjhbrjyt');
define ('MAIL_SENDER',		'<% ini.mail.sender %>');
define ('SERVICE_EMAILS',	'<% ini.service.emails %>');


ini_set('session.gc_maxlifetime',	SESSION_EXPIRE);
ini_set('session.cookie_lifetime',	SESSION_EXPIRE);
ini_set('session.save_path',		SESSION_PATH);
