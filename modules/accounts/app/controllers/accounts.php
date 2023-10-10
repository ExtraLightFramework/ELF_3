<?php

namespace Elf\App\Controllers;

use Elf;

class Accounts extends \Elf\Controllers\Admin {
	
	private $accs;

	function __construct() {
		parent::__construct();
		$this->accs = new \Elf\App\Models\Accounts;
	}
	function index($offset = 0) {
		Elf::$_data['offset'] = (int)$offset;
		list(Elf::$_data['data'],Elf::$_data['pagi']) = $this->accs->_data((int)$offset);
		echo Elf::set_layout('admin')->load_view('accounts/index');
	}
	function _edit($offset = 0) {
		$this->accs->_edit();
		Elf::redirect('accounts/index/'.(int)$offset);
	}
	function _search($offset = 0, $search_hash = '') {
		list(Elf::$_data['data'],Elf::$_data['pagi'],Elf::$_data['shash']) = $this->accs->_search($offset, $search_hash);
		Elf::$_data['offset'] = (int)$offset;
		Elf::$_data['search_on'] = true;
		Elf::$_data['search_values'] = Elf::input()->data();
		echo Elf::set_layout('admin')->load_view('accounts/index');
	}
}
