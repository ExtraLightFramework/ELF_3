<?php
define ('_VALID_CODE', 1);

$ret = ['ok'=>1,'error'=>null];

if (!empty($_POST['step']) && (int)$_POST['step']) {
	if (!empty($_POST['params']) && ($params = (array)json_decode($_POST['params'])))
		foreach ($params as $k=>$v)
			$_POST[$k] = $v;
	switch ((int)$_POST['step']) {
		case 1:
			if (empty($_POST['protocol']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'Site protocol' required";
			if (empty($_POST['domain']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'Site domain' required";
			if (empty($_POST['db_host']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'DB Host' required";
			if (empty($_POST['db_name']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'DB Name' required";
			if (empty($_POST['db_user']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'DB User' required";
			if (empty($_POST['db_pass']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'DB Password' required";
			if (empty($ret['error'])) {
				$mysqli = @new Mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
				if ($mysqli->connect_errno) {
					$ret['error'] = "Can't connect to database: ".$mysqli->connect_error;
					break;
				}
				if (!($ini = @fopen("_ini.php","r"))) {
					$ret['error'] = "Can't open ini-file";
					break;
				}
				if (!($data = @fread($ini,filesize('_ini.php')))) {
					$ret['error'] = "Can't read from ini-file";
					break;
				}
				fclose($ini);
				$data = str_replace('<% ini.diralias %>',$_POST['diralias'],$data);
				$data = str_replace('<% ini.domain %>',$_POST['domain'],$data);
				$data = str_replace('<% ini.protocol %>',$_POST['protocol'],$data);
				$data = str_replace('<% ini.db.host %>',$_POST['db_host'],$data);
				$data = str_replace('<% ini.db.base %>',$_POST['db_name'],$data);
				$data = str_replace('<% ini.db.user %>',$_POST['db_user'],$data);
				$data = str_replace('<% ini.db.pass %>',$_POST['db_pass'],$data);
				$data = str_replace('<% ini.db.prefix %>',$_POST['db_prefix'],$data);
				$data = str_replace('<% ini.mail.sender %>',$_POST['email_sender'],$data);
				$data = str_replace('<% ini.service.emails %>',$_POST['service_emails'],$data);
				if (!($ini = @fopen('../_ini.php','wb'))) {
					$ret['error'] = "Can't open ini-file to write";
					break;
				}
				if (@fwrite($ini, $data) === false) {
					$ret['error'] = "Can't write data to ini-file";
					break;
				}
				fclose($ini);
				if (!($ini = @fopen('robots.txt','rb'))) {
					$ret['error'] = "Can't open file robots.txt ";
					break;
				}
				if (!($data = @fread($ini,filesize('robots.txt')))) {
					$ret['error'] = "Can't read from robots.txt";
					break;
				}
				fclose($ini);
				$data = str_replace('<% site_url %>',$_POST['protocol'].'://'.$_POST['domain'].'/',$data);
				if (!($ini = @fopen('../robots.txt','wb'))) {
					$ret['error'] = "Can't open to write robots.txt";
					break;
				}
				if (!@fwrite($ini,$data)) {
					$ret['error'] = "Can't write data to robots.txt";
					break;
				}
				fclose($ini);
			}
			break;
		case 2:
			if (empty($_POST['admin_login']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'Administrator Login' required";
			if (empty($_POST['admin_passwd']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'Administrator Password' required";
			if (empty($_POST['time_zone']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'Time Zone' required";
			if (empty($_POST['full_system_language']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'Full System Language' required";
			if (empty($_POST['system_language']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'System Language (short)' required";
			if (empty($_POST['pagi_max_links']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'Max Links in Paginator' required";
			if (empty($_POST['max_image_size']))
				$ret['error'] .= (!empty($ret['error'])?"\n":"")."Field 'Max Upload Image Size (bytes)' required";
			if (!isset($_POST['system_auto_login']))
				$_POST['system_auto_login'] = 0;
			if (!isset($_POST['debug_mode']))
				$_POST['debug_mode'] = 0;
			if (!isset($_POST['real_send_mail']))
				$_POST['real_send_mail'] = 0;
			if (empty($ret['error'])) {
				if ((@include "../_ini.php") == false) {
					$ret['error'] = "Can't include file _ini.php";
					break;
				}
				$mysqli = @new Mysqli(DB_HOST, DB_USER, DB_PASS, DB_BASE);
				if ($mysqli->connect_errno) {
					$ret['error'] = "Can't connect to database: ".$mysqli->connect_error;
					break;
				}
				if (!($sql = @fopen("_db.sql","r"))) {
					$ret['error'] = "Can't open to read SQL file _db.sql";
					break;
				}
				if (!($data = @fread($sql,filesize('_db.sql')))) {
					$ret['error'] = "Can't read from SQL file _db.sql";
					break;
				}
				fclose($sql);
				$_POST['prefix'] = DB_PREFIX;
				$_POST['service_emails'] = SERVICE_EMAILS;
				$_POST['time'] = time();
				foreach ($_POST as $k=>$v)
					$data = str_replace('%%'.$k.'%%',$v,$data);
				$mysqli->query("SET NAMES utf8");
				$stmt = explode("---sql stmt---\n",$data);
				foreach ($stmt as $v) {
					@$mysqli->query($v);
					if ($mysqli->errno)
						$ret['error'] .= (!empty($ret['error'])?"\n":"")."SQL statement error: ".$mysqli->error;
				}
			}
			break;
		case 3:
			foreach ($_POST as $k=>$v) {
				if ((strpos($k, 'module_') !== false) && ($v == 1)) {
					$modules[] = str_replace('module_','',$k);
				}
			}
			if (!empty($modules)) {
				if ((@include "../_ini.php") == false) {
					$ret['error'] = "Can't include file _ini.php";
					break;
				}
				$mysqli = @new Mysqli(DB_HOST, DB_USER, DB_PASS, DB_BASE);
				if ($mysqli->connect_errno) {
					$ret['error'] = "Can't connect to database: ".$mysqli->connect_error;
					break;
				}
				foreach ($modules as $v) {
					try {
						full_copy(__DIR__.'/../modules/'.$v,__DIR__.'/..',$v);
					}
					catch (Exception $e) {
						$ret['error'] = $e->getMessage();
						break;
					}
				}
			}
			if (empty($ret['error']))
				rename(__DIR__.'/../install',__DIR__.'/../_install');
			break;
		default:
			$ret['error'] = 'Incorrect step number';
			break;
	}
}
else
	$ret['error'] = 'Incorrect use script step.php';

echo json_encode($ret);

function full_copy($source, $target, $module) {
	global $mysqli, $ret;//, $processUser;
	if (@is_dir($source)) {
		if (!@is_dir($target)) {
			if (!@mkdir($target))
				throw new Exception("Can't create directory ".$target);
			else
				chmod($target, 0777);
		}
		$d = @dir($source);
		while (false !== ($entry = @$d->read())) {
			if ($entry == '.' || $entry == '..') continue;
			elseif ($entry == '_sql') {
				$ds = @dir("$source/$entry");
				while (false !== ($sql = @$ds->read())) {
					if ($sql == '.' || $sql == '..') continue;
					elseif (!@is_dir($source.'/_sql/'.$sql)
						&& ($f = @fopen($source.'/_sql/'.$sql,'rb'))) {
						$data = @fread($f,filesize($source.'/_sql/'.$sql));
						@fclose($f);
						$data = str_replace('%%prefix%%',DB_PREFIX,$data);
						$mysqli->query("SET NAMES utf8");
						$stmt = explode("---sql stmt---\n",$data);
						foreach ($stmt as $v) {
							@$mysqli->query($v);
							if ($mysqli->errno)
								throw new Exception("In SQL statement error: ".$mysqli->error."\n SQL statement: ".$v);
						}
					}
				}
				@$ds->close();
				unset($ds);
			}
			else
				full_copy("$source/$entry", "$target/$entry", $module);
		}
		@$d->close();
	}
	else {
		$fi = pathinfo($target);
		switch ($fi['extension']) {
			case 'css':
				insert_lnk_in_layouts("<link href=\"".str_replace(__DIR__.'/../','/',$fi['dirname'])."/".$fi['basename']."\" rel=\"stylesheet\" type=\"text/css\" />\n","<!-- module css files -->\n");
				break;
			case 'js':
				insert_lnk_in_layouts("<script src=\"".str_replace(__DIR__.'/../','/',$fi['dirname'])."/".$fi['basename']."\"></script>\n","<!-- module js files -->\n");
				break;
			case 'mnu':
				if ($f = @fopen($source,'rb')) {
					$str = @fread($f,filesize($source));
					insert_lnk_in_layouts($str,"<!-- insert below module items [DO NOT REMOVE THIS]-->\n");
				}
				return;
		}
		copy($source, $target);
		chmod($target, 0666);
	}
}
function insert_lnk_in_layouts($lnk, $repl) {
	$fs = [__DIR__.'/../views/admin.php',
		__DIR__.'/../views/layout.php',
		__DIR__.'/../views/admin/menu.php'];
	foreach ($fs as $v) {
		if (file_exists($v)) {
			$vv = str_replace('/views','/app/views',$v);
			if (!file_exists($vv))
				copy($v, $vv);
			if ($f = @fopen($vv,'rb')) {
				$str = @fread($f,filesize($vv));
				@fclose($f);
				if ($f = @fopen($vv,'wb')) {
					$str = str_replace($repl,$repl."\n".$lnk,$str);
					if (@fwrite($f,$str) === false)
						throw new Exception('Can not write in file '.$vv);
					@fclose($f);
				}
				else
					throw new Exception('Can not open file '.$vv.' for writing.');
			}
		}
	}
}
