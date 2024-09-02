<?php

namespace Elf\Libs;

use Elf;

class Users_groups extends Db {
	
	function __construct() {
		parent::__construct('users_groups');
		if ($res = $this->_data()) {
			foreach ($res as $v) {
				$alias = strtoupper($v['alias']);
				if (!defined("GROUP_{$alias}")) {
					define ("GROUP_{$alias}", $v['id']);
				}
			}
		}
	}
	function _data() {
		return $this->_select()->_execute();
	}
}