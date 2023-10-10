<?php

namespace Elf\App\Models;

use Elf;

class Accounts extends \Elf\Libs\Users {
	
	function _data($offset = 0, $whr = null) {
		$ret = $pagi = null;
		if ($ret = $this->_select()
					->_where($whr)
					->_orderby("`tm_reg` DESC")
					->_limit(RECS_ON_PAGE,$offset*RECS_ON_PAGE)
					->_execute()) {
			$pg = new \Elf\Libs\Pagination;
			$pagi = $pg->create('/accounts/index',$this->cnt(),(int)$offset,RECS_ON_PAGE,3);
		}
		return [$ret,$pagi];
	}
	function chk_reg_fields() {
		$ret = true;
		$data = Elf::input()->data();
		if (empty($data['email'])
			|| !preg_match("/^([a-zA-Z0-9_]|\-|\.)+@(([a-z0-9]|\-)+\.)+[a-z]{2,6}$/",$data['email'])) {
			Elf::$_data['error'] .= Elf::lang('accounts')->item('reg.error.email')."\n";
			$ret = false;
		}
		elseif ($this->get("`email`='".$data['email']."'","`email`")) {
			Elf::$_data['error'] .= Elf::lang('accounts')->item('reg.error.emailexists')."\n";
			$ret = false;
		}
		if (empty($data['passwd'])
			|| empty($data['repasswd']) 
			|| ($data['passwd']!=$data['repasswd']) 
			|| (strlen($data['passwd']) < 6)) {//!preg_match("/^[a-zA-Z0-9_]{6,12}$/",$data['passwd'])) {
			Elf::$_data['error'] .= Elf::lang('accounts')->item('reg.error.password')."\n";
			$ret = false;
		}
		if (empty($data['phone'])
			|| !preg_match("/^\+((\d{1,2})[\- ]?)?(\(?\d{2,4}\)?[\- ]?)?[\d\- ]{7,10}$/",$data['phone'])) {
			Elf::$_data['error'] .= Elf::lang('accounts')->item('reg.error.phone')."\n";
			$ret = false;
		}
		return $ret;
	}
	function reg() {
		$data = Elf::input()->data();
		if ($ret = $this->chk_reg_fields()) {
		}
		return $ret;
	}
	function auto_login($uid) {
		if ($u = $this->get_by_id($uid)) {
			$this->set_sess_vars($u);
			unset($u);
		}
	}
	function _edit() {
//		print_r(Elf::input()->data());exit;
		$mess = '';
		$ret = true;
		$data = Elf::input()->data();
		if ($rec = $this->get_by_id((int)$data['id'])) {
			if (empty($data['email'])
				|| !preg_match("/^([a-zA-Z0-9_]|\-|\.)+@(([a-z0-9]|\-)+\.)+[a-z]{2,6}$/",$data['email'])) {
				$mess .= Elf::lang('accounts')->item('error.email');
				$ret = false;
			}
			if (!empty($data['passwd'])) {
				if (empty($data['repasswd']) 
					|| ($data['passwd']!=$data['repasswd']) 
					|| (strlen($data['passwd']) < 6)) {//!preg_match("/^[a-zA-Z0-9_]{6,12}$/",$data['passwd'])) {
					$mess .= Elf::lang('accounts')->item('error.password');
					$ret = false;
				}
				else
					$data['passwd'] = md5($data['passwd']);
			}
			elseif (isset($data['passwd']))
				unset($data['passwd']);
			
			$grp = 0;
			if (!empty($data['group']) && is_array($data['group']) && sizeof($data['group'])) {
				foreach ($data['group'] as $k=>$v)
					$grp |= $k;
				$data['group'] = $grp;
			}
			else
				$data['group'] = GROUP_USER;
			unset($grp);
			
			if ($ret) {
				$this->_update($data)->_where("`id`=".$rec['id'])->_orderby("`id`")->_limit(1)->_execute();
			}
			else
				Elf::messagebox($mess);
		}
		else
			Elf::messagebox('account.error.userfound');
	}
// ==== SEARCH
	function _search($offset = 0, $shash = '') {
		$ret = $pagi = null;
		if (list($data,$shash) = $this->prepare_data($shash)) {
			$whr = null;
			if (!empty($data['id'])) {
				$whr = "`id`=".(int)$data['id']." OR `login` LIKE '".$data['id']."%' OR `email` LIKE '%".$data['id']."%'";
			}
//			echo $whr;exit;
			if (list($ret,$pagi) = $this->_data($offset,$whr)) {
					$pg = new \Elf\Libs\Pagination;
					$pagi = $pg->create('/accounts/_search//'.$shash,
												$this->cnt($whr),
												(int)$offset,RECS_ON_PAGE,3);
			}
		}	
		return [$ret,$pagi,$shash];
	}
	private function prepare_data($shash) {
		if ($shash) {
			if (($res = json_decode(base64_decode($shash)))
				&& is_object($res)
				&& sizeof($res)) {
				foreach ((array)$res as $k=>$v) {
					if (is_object($v) && sizeof($v)) {
						foreach ((array)$v as $kk=>$vv)
							$data[$k][$kk] = $vv;
					}
					else
						$data[$k] = $v;
					Elf::input()->set($k,$data[$k]);
				}
			}
		}
		else {
			$data = Elf::input()->data();
			$shash = base64_encode(json_encode($data));
		}
		return !empty($data)?[$data,$shash]:[null,null];
	}
}
