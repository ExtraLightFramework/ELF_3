<?php

namespace Elf;

use Elf;

class Elfapp {
	public function gen_alias($text, $add = '') {
		return self::gen_chpu($text).($add?'-'.$add:'');
	}
	public function gen_chpu($text) {
		return strtolower(preg_replace('/[^a-zA-Z0-9\-]+/i','',str_replace([" ","/"],"-",self::translit(self::show_words($text,12)))));
	}
	public function show_words($s, $col) {
		$i = 0;
		$pos = mb_strpos($s, ' ');
		while ((++$i < $col) && ($pos !== false)) {
			$pos = mb_strpos($s, ' ', $pos+1);
		}
		if ($pos === false)
			$pos = strlen($s);
		return mb_substr($s, 0, $pos);
	}
	public function translit($st) {
	// Сначала заменяем "односимвольные" фонемы.
		$st = iconv('UTF-8','WINDOWS-1251', $st);
		if ($st) {
			$st=strtr($st,iconv('UTF-8','WINDOWS-1251',"абвгдеёзийклмнопрстуфхъэ "),
					"abvgdeezijklmnoprstufh_e-");
			$st=strtr($st,iconv('UTF-8','WINDOWS-1251',"АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЭ "),
					"ABVGDEEZIJKLMNOPRSTUFH_E-");
	// Затем - "многосимвольные".
			$st=strtr($st, 
					array(
					iconv('UTF-8','WINDOWS-1251',"ж")=>"zh",
					iconv('UTF-8','WINDOWS-1251',"ц")=>"ts",
					iconv('UTF-8','WINDOWS-1251',"ч")=>"ch",
					iconv('UTF-8','WINDOWS-1251',"ш")=>"sh", 
					iconv('UTF-8','WINDOWS-1251',"щ")=>"shch",
					iconv('UTF-8','WINDOWS-1251',"ь")=>"",
					iconv('UTF-8','WINDOWS-1251',"ъ")=>"",
					iconv('UTF-8','WINDOWS-1251',"ю")=>"yu",
					iconv('UTF-8','WINDOWS-1251',"я")=>"ya",
					iconv('UTF-8','WINDOWS-1251',"ы")=>"yi",
					iconv('UTF-8','WINDOWS-1251',"Ж")=>"ZH",
					iconv('UTF-8','WINDOWS-1251',"Ц")=>"TS",
					iconv('UTF-8','WINDOWS-1251',"Ч")=>"CH",
					iconv('UTF-8','WINDOWS-1251',"Ш")=>"SH", 
					iconv('UTF-8','WINDOWS-1251',"Щ")=>"SHCH",
					iconv('UTF-8','WINDOWS-1251',"Ь")=>"",
					iconv('UTF-8','WINDOWS-1251',"Ъ")=>"",
					iconv('UTF-8','WINDOWS-1251',"Ю")=>"YU",
					iconv('UTF-8','WINDOWS-1251',"Я")=>"YA",
					iconv('UTF-8','WINDOWS-1251',"Ы")=>"YI",
					iconv('UTF-8','WINDOWS-1251',"ї")=>"i",
					iconv('UTF-8','WINDOWS-1251',"Ї")=>"Yi",
					iconv('UTF-8','WINDOWS-1251',"є")=>"ie",
					iconv('UTF-8','WINDOWS-1251',"Є")=>"Ye")
				);
//			$st = iconv('windows-1251','utf-8',$st);
		}
		return $st;
	}
	public function sec_to_hms($sec) {
		$h = (int)($sec/3600);
		$m = (int)(($sec - ($h*3600))/60);
		$s = $sec - ($h*3600) - ($m*60);
		return str_pad($h, 2, '0', STR_PAD_LEFT).":".str_pad($m, 2, '0', STR_PAD_LEFT).":".str_pad($s, 2, '0', STR_PAD_LEFT);
	}
	public function captcha($name = 'captcha', $len = 4, $force = false) {
		if ((Elf::session()->get($name) && $force)
			|| !Elf::session()->get($name)) {
			$rndint = Elf::app()->gen_password($len);
			Elf::session()->set($name,$rndint);
		}
		else
			$rndint = Elf::session()->get($name);		
		$height=30; 
		$width=100;  
		$img = imagecreate($width, $height);
		
		$black = imagecolorallocate($img, 0, 0, 0);
		$white = imagecolorallocate($img, 255, 255, 255);
		$gray = imagecolorallocate($img, 249, 249, 249);
		$orange = imagecolorallocate($img, 255, 128, 64);
		$lightorange = imagecolorallocate($img, 255, 220, 164);
		$green = imagecolorallocate($img, 63, 166, 150);
		$darkgreen = imagecolorallocate($img, 14, 63, 56);
		$red = imagecolorallocate($img, 255, 0, 0);
		$blue = imagecolorallocate($img, 0, 115, 187);//0073bb
		
		imagefilledrectangle($img, 0, 0, $width, $height, $white);
	//	imagerectangle($img, 0, 0, $width-1, $height-1, $darkgreen);
		
		imagettftext($img, 24, 0, 10, 25, $black, ROOTPATH.'fonts/PTSansBold.ttf', $rndint);
		
		for ($i=1; $i<=70; $i++) {
				$int1=rand(5,$width-4);
				$int2=rand(0,$height);
				imagesetpixel($img, $int1, $int2, $black);
				$int3=rand(0,15);
				$int4=rand(0,15);
				imageline($img, $int1, $int2, $int1+$int3, $int2+$int4, $white);
		}
		
		$str = '';
		if (!is_dir(ROOTPATH."img/captcha")) {
			mkdir(ROOTPATH."img/captcha");
		}
		$t = time();
		if (imagepng($img,ROOTPATH."img/captcha/captcha".$t.".png")) {
			if ($f = fopen(ROOTPATH.'img/captcha/captcha'.$t.'.png','rb')) {
				$str = fread($f,filesize(ROOTPATH.'img/captcha/captcha'.$t.'.png'));
				$str = base64_encode($str);
				fclose($f);
				@unlink(ROOTPATH.'img/captcha/captcha'.$t.'.png');
			}
		}
		return $str?"data:image/png;base64,{$str}":"";
	}
	public function json_decode_to_array($json) {
		if (!empty($json))
			return (array)json_decode(htmlspecialchars_decode($json));
		else
			return null;
	}
	public function padezh($num, $p1 = '', $p2 = '', $p3 = '') {
		$ed = (int)(($num/10-(int)($num/10))*10);
		$de = (int)(($num/100-(int)($num/100))*10);
		if ($num == 0)
			return $p3;
		elseif ((($de>=0) && ($de<1)) || ($de>=2)) {
			if ($ed == 0)
				return $p3;
			elseif ($ed == 1)
				return $p1;
			elseif (($ed > 1) && ($ed <= 4))
				return $p2;
			elseif ($ed >= 5)
				return $p3;
		}
		elseif ($de==1) {
			return $p3;
		}
	}
	function normalize_date($date, $type, $delimeter = '') { // $type = db | app
		if (($type == 'db') && preg_match('/^(\d{2})\.(\d{2})\.(\d{4})/', $date))
			$ret = preg_replace('/^(\d{2})\.(\d{2})\.(\d{4})/', '$3-$2-$1', $date);
		elseif (($type == 'app') && preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $date))
			$ret = preg_replace('/^(\d{4})-(\d{2})-(\d{2})/', '$3.$2.$1', $date);
		else
			$ret = $date;
		return $delimeter?str_replace(' ',$delimeter, $ret):$ret;
	}
	function year_from_date($date) {
		if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})/', $date, $matches))
			return $matches[3];
		elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $date, $matches))
			return $matches[1];
		else
			return date('Y');
	}
	function localize_date($dt) {
		$dt = new \DateTime($dt);
		switch ($dt->format('m')) {
			case '01': $m='января'; break;
			case '02': $m='февраля'; break;
			case '03': $m='марта'; break;
			case '04': $m='апреля'; break;
			case '05': $m='мая'; break;
			case '06': $m='июня'; break;
			case '07': $m='июля'; break;
			case '08': $m='августа'; break;
			case '09': $m='сентября'; break;
			case '10': $m='октября'; break;
			case '11': $m='ноября'; break;
			case '12': $m='декабря'; break;
		}
		return $dt->format('d').' '.$m.' '.$dt->format('Y').'г.';
	}
    function utf8_ucfirst($string) {
        if (function_exists('mb_strtoupper')
			&& function_exists('mb_substr')) {
            $string = mb_strtolower($string, 'UTF-8');
            $upper = mb_strtoupper($string, 'UTF-8');
            preg_match('#(.)#us', $upper, $matches);
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, 'UTF-8'), 'UTF-8');
        }
		else {
            $string = ucfirst($string);
        }
        return $string;
    } 
	function num2words($num) {
		$nul = 'ноль';
		$ten = [
				['','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'],
				['','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'],
		];
		$a20 = ['десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать'];
		$tens = [2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто'];
		$hundred = ['','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот'];
		$unit = [ // Units
			['копейка' ,'копейки' ,'копеек'		,1],
			['рубль'   ,'рубля'   ,'рублей'		,0],
			['тысяча'  ,'тысячи'  ,'тысяч'		,1],
			['миллион' ,'миллиона','миллионов'	,0],
			['миллиард','милиарда','миллиардов'	,0],
		];
		//
		list($rub,$kop) = explode('.', sprintf("%015.2f", floatval($num)));
		$out = [];
		if ((int)$rub > 0) {
			foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
				if (!(int)$v) continue;
				$uk = sizeof($unit) - $uk - 1; // unit key
				$gender = $unit[$uk][3];
				list($i1, $i2, $i3) = array_map('intval',str_split($v,1));
				// mega-logic
				$out[] = $hundred[$i1]; # 1xx-9xx
				if ($i2>1)
					$out[] = $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
				else
					$out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
				// units without rub & kop
				if ($uk>1)
					$out[]= self::padezh($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
			} //foreach
		}
		else
			$out[] = $nul;
		$out[] = self::padezh((int)$rub, $unit[1][0], $unit[1][1], $unit[1][2]); // rub
		$out[] = $kop.' '.self::padezh($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
		return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
	}
	public function date2timestamp($str) {
		$ret = 0;
		$str = explode(".", $str);
		if (sizeof($str) == 3) {
			switch ($str[1]) {
				case '01':
				case '1':
					$str[1] = 'January';
					break;
				case '02':
				case '2':
					$str[1] = 'February';
					break;
				case '03':
				case '3':
					$str[1] = 'March';
					break;
				case '04':
				case '4':
					$str[1] = 'April';
					break;
				case '05':
				case '5':
					$str[1] = 'May';
					break;
				case '06':
				case '6':
					$str[1] = 'June';
					break;
				case '07':
				case '7':
					$str[1] = 'July';
					break;
				case '08':
				case '8':
					$str[1] = 'August';
					break;
				case '09':
				case '9':
					$str[1] = 'September';
					break;
				case '10':
					$str[1] = 'October';
					break;
				case '11':
					$str[1] = 'November';
					break;
				case '12':
					$str[1] = 'December';
					break;
				default:
					$str[1] = 'January';
					break;
			}
			$ret = strtotime($str[0]." ".$str[1]." ".$str[2]);
		}
		return $ret>=0?$ret:0;
	}
	public function gen_password($number) {
/*		'a','b','c','d','e','f',
        'g','h','i','j','k','l',
        'm','n','o','p','r','s',
        't','u','v','x','y','z',
		'A','B','C','D','E','F',
         'G','H','I','J','K','L',
         'M','N','O','P','R','S',
         'T','U','V','X','Y','Z',
*/
    	$arr = array(
                'a','b','c','d','e','f',
        		'g','h','i','j','k','l',
        		'm','n','o','p','q','r','s',
		        't','u','v','w','x','y','z');//, 
//                 '1','2','3','4','5','6',
//                '7','8','9','0');
	    $pass = "";

	    for($i = 0; $i < $number; $i++)
	    {
			$index = rand(0, count($arr) - 1);
			$pass .= $arr[$index];
		}
    	return $pass;
	}
	public function send_mail($to, $subject, $text, $sign='', $from = '', $attach_files = []) {
		$text = stripslashes(wordwrap($text, 70));
		$to = trim($to);
		$subject = trim($subject);
		$from = $from?$from:MAIL_SENDER;
		$sign = $sign?$sign:(defined('MAIL_SIGN')?MAIL_SIGN:$from);

		// Заголовки письма === >>>
		$headers = "Return-Path: ".$from."\r\nReply-to: ".$from."\r\n";
		$headers .= "From: =?utf-8?B?".base64_encode($sign)."?= <".$from.">\r\n";
		$headers .= "Date: " . date("r") . "\r\n";
	//	$headers .= "X-Mailer: Php-mailer(".phpversion().")\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "List-Unsubscribe: <".self::site_url()."main/unsubscribe/".base64_encode($to).">\r\n";
		$headers .= "Content-Type: multipart/alternative;\r\n";
		$baseboundary = "------------" . strtoupper(md5(uniqid(rand(), true)));
		$headers .= "  boundary=\"$baseboundary\"\r\n";
		// <<< ====================

		// Тело письма === >>>
		$message  =  "--$baseboundary\r\n";
		$message .= "Content-Type: text/plain;\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$message .= strip_tags($text)."\nUnsubscribe - ".self::site_url()."main/unsubscribe/".base64_encode($to)."\r\n";
		$message .= "--$baseboundary\r\n";
		$message .= "Content-Type: text/html; charset=utf-8\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$message .= $text . "<br /><br /><p style=\"font-size:10px;color:#999;\">Unsubscribe <a href=\"".self::site_url()."main/unsubscribe/".base64_encode($to)."\">link</a></p>\r\n\r\n";
		if (sizeof($attach_files)) {
			foreach ($attach_files as $v) {
				if (is_file($v) && ($f = fopen($v, 'rb'))) {
					$file = fread($f, filesize($v));
					fclose($f);
					$message .= "--$baseboundary\r\n"; 
					$message .= "Content-Type: application/octet-stream; name=\"".basename($v)."\"\r\n";  
					$message .= "Content-Transfer-Encoding: base64\r\n"; 
					$message .= "Content-Disposition: attachment; filename=\"".basename($v)."\"\r\n\r\n"; 
					$message .= chunk_split(base64_encode($file))."\r\n";
				}
			}
		}
		// <<< ==============
		if (REAL_SEND_MAIL) {
			mail($to, $subject, $message, $headers, "-f ".$from);
		}
		else {
			if ($log = fopen(ROOTPATH.'logs/lastmail.html','wb')) {
				fputs($log, "To: ".$to."\r\n"); 
				fputs($log, "Subject: =?utf-8?B?".base64_encode($subject)."?=\r\n"); 
				fputs($log, $headers);
				fputs($log, $message); 
				fclose($log);
			}
		}
		return true;
	}
	public function curl_request($url, $dt = null, $method = "POST", $cookie = '', $headers = null) {
		if (!function_exists('curl_init'))
			throw new \Exception('CURL module not installed');
		$ch = curl_init();
		if ($headers) {
			if (!is_array($headers))
				$headers = explode("\r\n",$headers);
			if ($dt)
				$headers[] = 'Content-Length: '.strlen($dt);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, 0);
		}
		if ($cookie) {
			if (!is_array($cookie))
				$cookie = explode("\r\n",$cookie);
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if ($dt) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $dt);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		if (($ret = curl_exec($ch)) === false) {
			$error['error'] = curl_error($ch);
			$error = json_encode($error);
		}
		curl_close($ch);
		return isset($error)?json_decode($error, true):json_decode($ret, true);
	}
}
