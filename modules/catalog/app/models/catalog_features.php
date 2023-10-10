<?php 

namespace Elf\App\Models;

use Elf;

class Catalog_features extends \Elf\Libs\Db {
	
	private $null_group;
	
	function __construct() {
		parent::__construct('catalog_features');
		$this->null_group = ['id'=>0,'type'=>'group','group_id'=>0,'unit_id'=>0,'feature_id'=>0,'value'=>'',
					'name'=>Elf::lang('catalog')->item('features.ungroup'),
					'desc'=>Elf::lang('catalog')->item('features.ungroup.desc'),
					'pos'=>0];
	}
	function _data($cid = 0, $strict = null) {
		$ret = [];
		if (!($res = $this->_select("t1.*,t1.`id` as `feature_id`,'' as `value`")->_where("`type`='group'")->_orderby("`pos`")->_execute())) {
			$res = [];
		}
		$res[] = $this->null_group;
		foreach ($res as $g) {
			$this->_select(((int)$cid?"t3.*,":'')."t2.*,t1.*,
										t2.`name` as `unit_name`,
										t2.`type` as `unit_type`")
							->_join('catalog_units','t2','t2.`id`=t1.`unit_id`');
			if ((int)$cid)
				$this->_join('catalog_features_values','t3','t3.`feature_id`=t1.`id` AND t3.`catalog_id`='.(int)$cid);
//			echo $this->_prepare()->_sql();
//			return;
			
			$this->_where("t1.`type`='feature' AND `group_id`=".$g['id']);
			
			if ($strict)
				$this->_and("t1.`id` NOT IN (".$strict.")");
			
			if ((int)$cid)
				$this->_and("t3.`catalog_id` IS NOT NULL");

			$feats = $this->_orderby("t1.`pos`")->_execute();
		
			if (((int)$cid && $feats)
				|| !(int)$cid) {
				$ret[] = $g;
				if ($feats)
					$ret = array_merge($ret, $feats);
			}
		}
		return $ret;
	}
	function _edit() {
		$ret = true;
		$data = Elf::input()->data();
		if (empty($data['name'])) {
			Elf::$_data['error'] = Elf::lang('catalog')->item('error.featurename');
			$ret = false;
		}
		if ($ret) {
			if (!empty($data['id']) && ($rec = $this->get_by_id((int)$data['id']))) {
				$ret = $rec['id'];
				$this->_update($data)->_where("`id`=".$rec['id'])->_orderby("`id`")->_limit(1)->_execute();
			}
			else {
				$ret = $this->_insert($data)->_execute();
			}
			return $ret;
		}
		else
			return Elf::$_data['error'];
	}
	function _get($fid) {
		$ret = null;
		if ((int)$fid && ($ret = $this->_select("t2.*,t1.*,t2.`type` as `unit_type`")
					->_join('catalog_units','t2','t2.`id`=t1.`unit_id`')
					->_where("t1.`id`=".(int)$fid)
					->_orderby("t1.`id`")->_limit(1)->_execute())) {
			$ret = $ret[0];
		}
		elseif ((int)$fid === 0) {
			$ret = $this->null_group;
		}
		return $ret;
	}
	function _edit_group() {
		$ret = false;
		$data = Elf::input()->data();
		if (!empty($data['id'])
			&& ($rec = $this->get_by_id((int)$data['id']))
			&& ($rec['type'] == 'group')) {
			$ret = $rec['id'];
			$this->_update($data)->_where("`id`=".$ret)->_orderby("`id`")->_limit(1)->_execute();
		}
		else {
			$data['type'] = 'group';
			$ret = $this->_insert($data)->_execute();
		}
		if ($ret) {
			$this->_update(['group_id'=>0])->_where("`group_id`=".$ret)->_and("`type`='feature'")->_orderby("`group_id`,`type`")->_execute();
			if (!empty($data['feature']) && sizeof($data['feature'])) {
				$ids = '';
				foreach ($data['feature'] as $k=>$v)
					$ids .= ($ids?',':'').$k;
				$this->_update(['group_id'=>$ret])->_where("`id` IN (".$ids.")")->_execute();
			}
		}
		return $ret;
	}
	function _group_features($gid, $strict = null) {
		$this->_select("t2.*,t1.*,
							t2.`name` as `unit_name`,
							t2.`type` as `unit_type`")
				->_join('catalog_units','t2','t2.`id`=t1.`unit_id`')
				->_where("t1.`type`='feature' AND `group_id`=".(int)$gid);
		
		if ($strict)
			$this->_and("t1.`id` NOT IN (".$strict.")");
		
		return $this->_orderby("t1.`pos`")->_execute();
	}
	function __get_group($gid) {
		if ($ret = $this->get_by_id((int)$gid)) {
			$ret = array_merge($ret, $this->get_features_for_group($ret['id']));
		}
		return $ret;
	}
	private function get_features_for_group($gid = 0) {
		$ret['features'] = $this->_select("t3.*,t1.*,t3.`name` as `unit_name`,t3.`type` as `unit_type`")
								->_subquery('catalog_features','t2')
									->_select('t2.`name`')->_where('t2.`id`=t1.`group_id`')->_closesquery('group_name')
								->_join('catalog_units','t3','t3.`id`=t1.`unit_id`')
								->_where("t1.`type`='feature'")->_orderby("t1.`type`,t1.`name`")->_execute();
		$ret['fids'] = array();
		if ((int)$gid) {
			if ($fids = $this->_select("`id`")->_where("`type`='feature'")->_and("`group_id`=".(int)$gid)->_orderby("`type`,`group_id`")->_execute())
				foreach ($fids as $v)
					$ret['fids'][] = $v['id'];
		}
		return $ret;
	}
	function _data_add_dialog($strict = null) {
		return $this->_select("t2.*,t1.*,t2.`type` as `unit_type`,t2.`name` as `unit_name`,LOWER(t1.`name`) as `name_lower`")
					->_join('catalog_units','t2','t2.`id`=t1.`unit_id`')
					->_where("t1.`type`='feature'".($strict?" AND t1.`id` NOT IN (".$strict.")":""))
					->_orderby("t1.`name`")
					->_execute();
	}
	function groups_selector($sel = '') {
		$ret = '<select name="group_id">';
		$ret .= '<option value="0">'.Elf::lang('catalog')->item('feature.noingroup').'</option>';
		if ($res = $this->_select()->_where("`type`='group'")->_orderby("`type`,`name`")->_execute()) {
			foreach ($res as $v)
				$ret .= '<option value="'.$v['id'].'" '.($sel==$v['id']?'selected="selected"':'').'>'.$v['name'].'</option>';
		}
		else
			$ret .= '<option value="0" disabled="disabled">'.Elf::lang('catalog')->item('nodata').'</option>';
		$ret .= '</select>';
		return $ret;
	}
}