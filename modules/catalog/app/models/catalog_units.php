<?php 

namespace Elf\App\Models;

use Elf;

class Catalog_units extends \Elf\Libs\Db {
	
	function __construct() {
		parent::__construct('catalog_units');
	}
	function _edit() {
		$ret = true;
		if (!Elf::input()->get('name')) {
			Elf::$_data['error'] .= Elf::lang('catalog')->item('error.unitname');
			$ret = false;
		}
		else
			Elf::input()->set('alias',Elf::gen_alias(Elf::input()->get('name')));
		
		if ($ret) {
			Elf::input()->set('name',Elf::input()->get('name'));
			if ((int)Elf::input()->get('id')
				&& $this->get_by_id((int)Elf::input()->get('id'))) {
				$this->_update(Elf::input()->data())->_where("`id`=".(int)Elf::input()->get('id'))->_orderby("`id`")->_limit(1)->_execute();
			}
			else {
				$this->_insert(Elf::input()->data())->_execute();
			}
		}
		else
			Elf::messagebox(Elf::$_data['error']);
		return $ret;
	}
	function _data() {
		return $this->_select()->_orderby("`name`")->_execute();
	}
	function _selector($sel = '') {
		$ret = '<select name="unit_id">';
		if ($res = $this->_select()->_orderby('name')->_execute()) {
			foreach ($res as $v)
				$ret .= '<option value="'.$v['id'].'" title="'.$v['desc'].'" '.($sel==$v['id']?'selected="selected"':'').'>'.$v['name'].($v['short_name']?' ('.$v['short_name'].')':'').'</option>';
		}
		else
			$ret .= '<option value="0" disabled="disabled">'.Elf::lang('catalog')->item('nodata').'</option>';
		$ret .= '</select>';
		return $ret;
	}
}