<?php

namespace Elf\Libs;

use Elf;

class Options extends Db {
	
	function __construct() {
		parent::__construct('options');
	}
	function _get($name, $dt = null) {
		if (!$dt)
			$dt = date('Y-m-d');
		else {
			$dt = new \DateTime($dt);
			$dt = $dt->format('Y-m-d');
		}
		return ($ret = $this->_select("`value`")
								->_where("`name`='{$name}'")
									->_and("DATE(`valid_from`)<='{$dt}'")
									->_and("DATE(`valid_to`)>='{$dt}'")
								->_limit(1)
								->_execute())?$ret[0]['value']:null;
						
	}
}