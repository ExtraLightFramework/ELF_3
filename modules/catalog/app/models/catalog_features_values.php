<?php 

namespace Elf\App\Models;

use Elf;

class Catalog_features_values extends \Elf\Libs\Db {
	
	function __construct() {
		parent::__construct('catalog_features_values');
	}
	function clear_vals_by_cid($cid) {
		return $this->_update(['value'=>''])->_where("`catalog_id`=".(int)$cid)->_orderby("`catalog_id`")->_execute();
	}
	function get_by_cid($cid, $gid = null) {
		return $this->_select("t3.*,t2.*,t1.*,t3.`name` as `unit_name`,t3.`type` as `unit_type`,t2.`id` as `feature_id`")
					->_join('catalog_features','t2','t2.`id`=t1.`feature_id`'.($gid !== null?" AND t2.`group_id`=".(int)$gid:""))
					->_join('catalog_units','t3','t3.`id`=t2.`unit_id`')
					->_where("t1.`catalog_id`=".(int)$cid.($gid !== null?" AND t2.`name`!=''":""))
					->_orderby("t2.`group_id`,t2.`pos`")
					->_execute();
	}
	function _get($cid, $fid) {
		if ($ret = $this->_select("t2.*,t1.*")
							->_join('catalog_features','t2','t2.`id`=t1.`feature_id`')
							->_where("t1.`catalog_id`=".(int)$cid." AND t1.`feature_id`=".(int)$fid)
							->_execute())
			$ret = $ret[0];
		return $ret;
	}
	function _upd_val($cid, $fid, $v) {
		if ($rec = $this->_get($cid, $fid))
			$this->_update(['value'=>$v])
						->_where("`id`=".$rec['id'])
						->_execute();
		else
			$this->_insert(['catalog_id'=>$cid,'feature_id'=>$fid,'value'=>$v])->_execute();
	}
}