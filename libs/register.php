<?php

namespace Elf\Libs;

use Elf;

class Register {
	
	static protected $db;
	static protected $data_table;
	static protected $search_by_field;
	static protected $return_data_field;
	static protected $conn_by_field;
	
	// Update
	// ??? static protected $updated_search_field; // поле, по значению из которого, будет производиться поиск последней (активной) записи регистра
	
	static function _init($ret_field_name = null) {
	}
	static function _obj() {
		return new self;
	}
	static function _get($value, $dt = null) {
		if (!$dt)
			$dt = date('Y-m-d');
		$whr = '';
		if (!is_array(self::$search_by_field))
			$whr = "t1.`".self::$search_by_field."`='{$value}'";
		elseif (is_array(self::$search_by_field)
			&& is_array($value)) {
			foreach (self::$search_by_field as $k=>$sf)
				$whr .= ($whr?' AND ':'')."t1.`{$sf}` IN ({$value[$k]})";
		}
		return ($ret = self::$db->_select("t2.`".self::$return_data_field."` AS `value`")
						->_join(self::$data_table,'t2',"t2.`id`=t1.`".self::$conn_by_field."`")
						->_where($whr)
							->_and("DATE('{$dt}')>=t1.`from`")
							->_and("DATE('{$dt}')<=t1.`to`")
						->_limit(1)
						->_execute())?$ret[0]['value']:null;
						
	}
	static function _set($upd, $search_value, $new = false) {
		// в $upd передается ассоциативный массив, где ключ - название поля, значение - данные, которые необходимо записать
		if (!empty(self::$search_by_field)
			&& is_array($upd)) {
			$dt = new \DateTime();// дата всегда будет равна текущей дате, т.к. это регистр, предыдущие данные - исторические
			$whr = "`".self::$search_by_field."`='{$search_value}' AND `to`='9999-12-31'";
			$upd = array_merge(['from'=>!$new?$dt->format('Y-m-d'):'0000-00-00',
								'to'=>'9999-12-31',
								self::$search_by_field=>$search_value],
								$upd);
			if ($rec = self::$db->_select()->_where($whr)->_orderby("`to` DESC")->_limit(1)->_execute()) {
				$rec = $rec[0];
				if ($rec['from'] == (string)$dt->format('Y-m-d')) {
					self::$db->_update($upd)->_where($whr)->_orderby("`to` DESC")->_limit(1)->_execute();
				}
				else {
					$dt->modify('-1 Day');
					self::$db->_update(['to'=>$dt->format('Y-m-d')])->_where($whr)->_orderby("`to` DESC")->_limit(1)->_execute();
					self::$db->_insert($upd)->_execute();
				}
			}
			else
				self::$db->_insert($upd)->_execute();
		}
	}
	static function data_selector($name, $sel = 0) {
		$data = new Db(self::$data_table);
		if ($res = $data->_select("`id`,`".self::$return_data_field."`")
					->_orderby(self::$return_data_field)->_execute()) {
			$ret = "<select name='{$name}' class='form-control' required='required'>";
			$ret .= "<option value=''></option>";
			foreach ($res as $v) {
				$ret .= "<option value='".$v['id']."' ".($sel==$v['id']?"selected='selected'":"").">".$v[self::$return_data_field]."</option>";
			}
			$ret .= "</select>";
		}
		return $ret;
	}
}