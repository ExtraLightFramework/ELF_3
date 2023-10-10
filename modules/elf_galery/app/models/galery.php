<?php

namespace Elf\App\Models;

use Elf;

class Galery extends \Elf\Libs\Uploaders {
	
	function __construct() {
		parent::__construct('galery');
		$this->post_init('');
	}
	function remfile($remfile, $icon = true) {
		if ($res = $this->get("`name`='".$remfile."'","`name`")) {
			parent::remfile($remfile, $icon);
			$this->_delete()->_where("`name`='".$remfile."'")->_orderby("`name`")->_execute();
		}
	}
	function save_to_db($fname, $params) {
		if (is_array($params) && isset($params['type'])) {
			$type = $params['type']?$params['type']:'common';
			if ($rec = $this->get("`name`='".$fname."' AND `type`='".$type."'","`name`,`type`"))
				return $this->_update(['title'=>!empty($params['title'])?$params['title']:'',
										'pos'=>(int)$params['pos']?(int)$params['pos']:$this->get_last_pos($type)])
							->_where("`id`=".$rec['id'])->_limit(1)->_execute();
			else
				return $this->_insert(['name'=>$fname,
									'title'=>!empty($params['title'])?$params['title']:'',
									'orient'=>$this->orient,
									'wh_coof'=>number_format($this->w/$this->h,8,'.',''),
									'pos'=>(int)$params['pos']?(int)$params['pos']:$this->get_last_pos($type),
									'type'=>$type])->_execute();
		}
	}
	function _data($params) {
		if (!empty($params['type'])) {
			return $this->_select("t1.*,concat('/','".$this->icons."',t1.`name`) as `src_icon`,
									concat('/','".$this->path."',t1.`name`) as `src_image`")
							->_where("`type`='".$params['type']."'")
							->_orderby('`type`,`pos`')->_execute();
		}
		else
			return null;
	}
	private function get_last_pos($type) {
		$ret = $this->get("`type`='".$type."'","`type`,`pos` DESC");
		return $ret?$ret['pos']+1:1;
	}
}
