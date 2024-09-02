<?php if (!defined('_VALID_CODE')) {header('HTTP/1.1 404 Not Found');echo '<h2>Page Not Found</h2>';exit;}
//print_r($_SERVER);exit;
require_once ("_ini.php");

// ==== !!!!!!
Elf::init();
// ==== !!!!!!

class Elf {
	static private $views_path;
	static private $models_path;
	static private $controllers_path;
	static private $libs_path;
	static private $lang_path;

	static private $app_views_path;
	static private $app_models_path;
	static private $app_controllers_path;
	static private $app_lang_path;
	
	static private $layout;
	static private $elfapp;

	static private $groups;
	static private $input;
	static private $routing;
	static private $session;
	static private $settings;
	static private $options;
	static private $history;
	static private $slog;
	static private $lang;
	static private $loaded_classes;
	static public $_data;

	static public function init() {
		self::$loaded_classes = self::$_data = [];

		self::$controllers_path = ROOTPATH.CONTROLLERS_DIR.'/';
		self::$views_path = ROOTPATH.VIEWS_DIR.'/';
		self::$models_path = ROOTPATH.MODELS_DIR.'/';
		self::$libs_path = ROOTPATH.LIBS_DIR.'/';
		self::$app_controllers_path = ROOTPATH.APP_DIR.'/'.CONTROLLERS_DIR.'/';
		self::$app_views_path = ROOTPATH.APP_DIR.'/'.VIEWS_DIR.'/';
		self::$app_models_path = ROOTPATH.APP_DIR.'/'.MODELS_DIR.'/';
		self::$lang_path = ROOTPATH.LANGS_DIR.'/'.SYSTEM_LANGUAGE.'/';
		self::$app_lang_path = ROOTPATH.APP_DIR.'/'.LANGS_DIR.'/'.SYSTEM_LANGUAGE.'/';
		self::$elfapp = null;
//		echo self::$app_views_path;exit;

		set_exception_handler('Elf::exception_handler');
		spl_autoload_register('Elf::_autoload');
		
		self::$settings = new Elf\Libs\Settings;
		self::$options = new Elf\Libs\Options;
		
		if (REDIRECTOR_ENABLED) // From old Site redirector
			Elf\Libs\Redirector::redirect();

		self::$groups = new Elf\Libs\Users_groups;
		self::$input = new Elf\Libs\Input;
		self::$routing = new Elf\Libs\Routing;
		self::$session = new Elf\Libs\Session;
		self::$history = new Elf\Libs\History;
		self::$slog = new Elf\Libs\Slog;
		
		if (DEBUG_MODE) {
			ini_set('display_errors', true);
			error_reporting(E_ALL);
		}
		if (AUTO_LOGIN_ENABLED) {
			self::$session->check_auto_login();
		}
		if (COOKIE_AGREEMENT_ENABLED)
			Elf::$_data['cookie.agreement'] = self::session()->cookie_agreement();

		date_default_timezone_set(TIME_ZONE);
		self::$layout = DEFAULT_LAYOUT;
		
		// Init flash data
		if (Elf::session()->get('flashdata'))
			Elf::$_data = array_merge(Elf::$_data, Elf::session()->get('flashdata'));
	}
	static private function is_loaded_class($class) {
		return isset(self::$loaded_classes[$class]);
	}
	static public function exception_handler($exception) {
		if (self::is_xml_request()) {
			echo json_encode(['exception'=>((int)self::settings()->_get('DEBUG_MODE')?"System exception with message \"":"").$exception->getMessage().((int)self::settings()->_get('DEBUG_MODE')?"\" in file \"".$exception->getFile()."\" in line ".$exception->getLine():'')]);
			exit;
		}
		else {
			if (!self::settings() || !(int)self::settings()->_get('DEBUG_MODE')) {
				header('HTTP/1.1 404 Not Found');
				header('Content-Type: text/html; charset=utf-8');
				echo iconv('WINDOWS-1251','UTF-8','<h1>Страница не найдена</h1>');
				echo iconv('WINDOWS-1251','UTF-8','<p>Запрошенная страница не найдена</p>');
				exit;
			}
			else {
				echo "System exception:<br />";
				echo "<strong>Message:</strong> ".$exception->getMessage()."<br />";
				echo "<strong>File:</strong> ".$exception->getFile()."<br />";
				echo "<strong>Line:</strong> ".$exception->getLine()."<br />";
				echo "<strong>Trace:</strong> ".$exception->getTraceAsString()."<br />";
			}
		}
	}
	static public function _autoload($class = '') {
		$path = '';
		if (empty($class)) {
			throw new \Exception('Class name is empty');
		}
		$path = str_replace("Elf\\","",$class);
		$path = strtolower(str_replace("\\","/",$path));
		$path = strpos($path,"/")===0?substr($path,1):$path;
		$classld = $class;
		$class = explode("\\",$class);
		$class = $class[sizeof($class)-1];
		if (!self::is_loaded_class($class)) {
			if (file_exists(ROOTPATH.$path.EXT)) { // any class
				require_once(ROOTPATH.$path.EXT);
				self::$loaded_classes[$class] = $classld;
			}
			else
//				throw new \Exception('Class <b>'.$classld.'</b> not found');
				return false;
		}
	}
// ========= PUBLIC GETTERS =========	
	static public function groups() {
		return self::$groups;
	}
	static public function input() {
		return self::$input;
	}
	static public function routing() {
		return self::$routing;
	}
	static public function session() {
		return self::$session;
	}
	static public function settings() {
		return self::$settings;
	}
	static public function options() {
		return self::$options;
	}
	static public function history() {
		return self::$history;
	}
	static public function slog() {
		return self::$slog;
	}
// =========== LANGS =================
	static public function lang($lang = 'main') {
		if (empty(self::$lang))
			self::$lang = new Elf\Libs\Lang;
		self::$lang->_load($lang,self::$lang_path.$lang.'.lng',self::$app_lang_path.$lang.'.lng');
		return self::$lang;
	}
	
// =========== VIEWS/TEMPLATES BLOCK =================
	static public function set_layout($layout) {
		self::$layout = $layout;
		return new static();
	}
	static public function load_view($view = '', $data = null, $show = true) {
		if ($data) {
			$pdata = self::$_data;
			self::$_data = array_merge(self::$_data,$data);
		}
		if (empty($view)) {
			throw new \Exception ('View name is empty!');
		}
		ob_start(null,0,PHP_OUTPUT_HANDLER_REMOVABLE|PHP_OUTPUT_HANDLER_CLEANABLE);
		if (file_exists(self::$app_views_path.strtolower($view).EXT))
			include (self::$app_views_path.strtolower($view).EXT);
		elseif (file_exists(self::$views_path.strtolower($view).EXT))
			include (self::$views_path.strtolower($view).EXT);
		else {
			@ob_end_clean();
			throw new \Exception ('View file <strong>'.$view.'</strong> not found.');
		}
				
		self::$_data['content'] = ob_get_contents();
		self::$_data['content'] = self::_parse(self::$_data['content']);
		@ob_end_clean();
		ob_start();
		if (file_exists(self::$app_views_path.strtolower(self::$layout).EXT))
			include (self::$app_views_path.strtolower(self::$layout).EXT);
		elseif (file_exists(self::$views_path.strtolower(self::$layout).EXT))
			include (self::$views_path.strtolower(self::$layout).EXT);
		else {
			@ob_end_clean();
			throw new \Exception ('Layout file not found.');
		}
		$buffer = ob_get_contents();
		$buffer = self::_parse($buffer);
		@ob_end_clean();
		if (!empty($pdata)) {
			self::$_data = $pdata;
			unset($pdata);
		}
		if ($show) {
			echo $buffer;
			exit;
		}
		else
			return $buffer;
	}
	static public function load_template($view = '', $data = null) {
		if ($data) {
			if (!is_array($data))
				throw new \Exception("Data is not array");
			$pdata = self::$_data;
			self::$_data = array_merge(self::$_data,$data);
		}
		if (empty($view)) {
			throw new \Exception ('Template name is empty!');
		}
		ob_start(null,0,PHP_OUTPUT_HANDLER_REMOVABLE|PHP_OUTPUT_HANDLER_CLEANABLE);
		if (file_exists(self::$app_views_path.strtolower($view).EXT))
			include (self::$app_views_path.strtolower($view).EXT);
		elseif (file_exists(self::$views_path.strtolower($view).EXT))
			include (self::$views_path.strtolower($view).EXT);
		else {
			@ob_end_clean();
			throw new \Exception ('Template file <strong>'.$view.'</strong> not found.');
		}
				
		$buffer = ob_get_contents();
		$buffer = self::_parse($buffer);
		@ob_end_clean();
		if (!empty($pdata)) {
			self::$_data = $pdata;
			unset($pdata);
		}
		return $buffer;
	}
	static private function _parse($buffer) {
		$buffer = preg_replace_callback("/<% ([a-zA-Z0-9_.]+) %>/",'Elf::_prc_data', $buffer);
		$buffer = preg_replace_callback("/<% lang:(([a-z0-9_.]+):)?([a-zA-Z0-9_.]+) %>/",'Elf::_prc_lang', $buffer);
		$buffer = preg_replace_callback("/<% session:([a-zA-Z0-9_.]+) %>/",'Elf::_prc_session', $buffer);
		return preg_replace_callback("'{([a-zA-Z_]+[a-zA-Z0-9_]*)}'",'Elf::_prc_exec', $buffer);
	}
	static private function _prc_data($matches) {
		return self::get_data($matches[1]);
	}
	static private function _prc_lang($matches) {
		return self::lang($matches[2]?$matches[2]:'main')->item($matches[3]);
	}
	static private function _prc_session($matches) {
		return self::session()->get($matches[1]);
	}
	static private function _prc_exec($matches) {
		if (method_exists(__CLASS__, $matches[1]))
			return call_user_func_array([__CLASS__, $matches[1]], []);
	}
	static public function isset_data($k) {
		return isset(self::$_data[$k]);
	}
	static public function get_data($k) {
		return self::isset_data($k)?self::$_data[$k]:null;
	}
	static public function set_data($k, $v = null, $rewrite = true) {
		$ret = self::get_data($k);
		self::$_data[$k] = $rewrite?$v:(self::isset_data($k)?self::$_data[$k].$v:$v);
		return $ret;
	}
	static public function show_dialog($view, $params = null) {
		$data['dialog'] = $view;
		$data['wid'] = time();
		if (!empty($params) && is_array($params)) {
			foreach ($params as $k=>$v)
				$data[$k] = $v;
		}
		self::$_data['preloadialog'] = $data['preloadialog'] = self::load_template(!empty($data['appearance'])?$data['appearance']:'main/dialog',$data);
		self::session()->set('flashdata',array_merge(self::$_data,$data));
		if (!empty(self::$_data['title']) || !empty($params['title']))
			self::session()->set('title',!empty(self::$_data['title'])?self::$_data['title']:$params['title']);
		if (!empty(self::$_data['description']) || !empty($params['description']))
			self::session()->set('description',!empty(self::$_data['description'])?self::$_data['description']:$params['description']);
		if (!empty(self::$_data['canonical']) || !empty($params['canonical']))
			self::session()->set('canonical',!empty(self::$_data['canonical'])?self::$_data['canonical']:$params['canonical']);
	}
	static public function messagebox($message) {
		$data['message'] = $message;
		//$data['dialog'] = 'main/messagebox';
		$data['wid'] = time();
		self::$_data['preloadialog'] = $data['preloadialog'] = self::load_template('main/messagebox',$data);
		self::session()->set('flashdata',array_merge(self::$_data,$data));
	}
	static public function no_cache() {
		// ==== NO CACHE ======
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: " . date("r"));
		// ====================
	}

// ========= NAVIGATION METHODS ==================
	static public function redirect($url = '', $hide = null) { // $hide [null|true|'app']
		if (strpos($url,'/') === 0)
			$url = substr($url,1);
		if ($hide === null) {
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: '.($url?DIR_ALIAS.'/'.$url:self::site_url()));
		}
		else {
			$ex = explode("/",$url);
			if (!empty($ex[0]) && !empty($ex[1])) {
				$c = "\\Elf\\".($hide==='app'?'':'App\\')."Controllers\\".$ex[0];
				$c = new $c;
				call_user_func_array([$c, $ex[1]],[]);
			}
		}
		exit;
	}
	static public function site_url($end_slash = true) {
		return SITE_PROTOCOL."://".SITE.($end_slash?"/":"");
	}
	static public function ip_addr() {
		return !empty($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
	}
	static private function get_x_request() {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])?$_SERVER['HTTP_X_REQUESTED_WITH']:SITE_PROTOCOL;
	}
	static public function is_xml_request() {
		return (self::get_x_request() == XML_STRING);
	}
	static public function accept_json() {
		return (bool)(!empty($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false));
	}
	static public function get_app_views_path() {
		return self::$app_views_path;
	}
	static public function app() {
		if (!self::$elfapp) {
			if (file_exists(ROOTPATH.APP_DIR.'/elfapp.php')) {
				require_once ROOTPATH.APP_DIR.'/elfapp.php';
				if (get_parent_class("Elf\\App\\Elfapp") == "Elf\\Elfapp")
					self::$elfapp = new Elf\App\Elfapp;
				else
					throw new \Exception("Elf\\App\\Elfapp class must be extends of Elf\\Elfapp class");
			}
			elseif (file_exists(ROOTPATH.'/elfapp.php')) {
				require_once ROOTPATH.'/elfapp.php';
				self::$elfapp = new Elf\Elfapp;
			}
			else
				throw new \Exception('Elfapp class not defined');
		}
		return self::$elfapp;
	}
	static public function write_log($mess, $logfile = 'log.txt') {
		if ($f = fopen(ROOTPATH.'logs/'.$logfile, 'ab')) {
			fwrite($f, date('d/m/y H:i:s').": ".$mess."\n");
			fclose($f);
		}
	}
}
