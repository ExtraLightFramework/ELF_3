<?php

namespace Elf\App\Controllers;

use Elf;

class Galery {
	
	function items() {
		$m = "\\Elf\\App\\Models\\".Elf::input()->get('galery');
		$gal = new $m;
		echo json_encode($gal->_data(Elf::json_decode_to_array(Elf::input()->get('params'))));
	}
}
