<?php

namespace Elf\App\Models;

use Elf;

define ('CATALOG_ITEMS_ON_PAGE',20);

class Catalog extends \Elf\Libs\Structure {
	
	private $units;
	private $cfv;
	private $pics;
	private $feats;
	
	function __construct() {
		parent::__construct('catalog','catalog');
		$this->units = new Catalog_units;
		$this->cfv = new Catalog_features_values;
		$this->feats = new Catalog_features;
		$this->pics = new Catalog_pictures;
	}
	function check_alias($alias, $cid) {
		return $this->get("`alias`='".$alias."'".((int)$cid?" AND `id`!=".(int)$cid:""))?false:true;
	}
	private function _repl_pics_and_rem_dirs($id, $alias) {
		if ($res = $this->_get_childs_ids($id, true)) {
			$palias = $this->_get_parent_alias($id);
			foreach ($res as $v) {
				if ($pics = $this->pics->get_by_cid($v['id'])) {
					foreach ($pics as $pic) {
						rename($this->pics->fpath.$alias.'/'.$pic['name'],$this->pics->fpath.($palias?$palias.'/':'').$pic['name']);
						rename($this->pics->fpath.$alias.'/icons/'.$pic['name'],$this->pics->fpath.($palias?$palias.'/':'').'icons/'.$pic['name']);
					}
				}
			}
		}
		rmdir($this->pics->fpath.($alias?$alias.'/':'').'icons');
		rmdir($this->pics->fpath.$alias);
	}
	function _edit() {
		if ($ret = $this->chk_req_fields()) {
			$ret = false;
			$data = Elf::input()->data();
			$data['name'] = Elf::input()->get('name',false);
			$data['inner_name'] = Elf::input()->get('inner_name',false);
			if ((int)$data['id']
				&& ($rec = $this->get_by_id((int)$data['id']))) { // existing REC
				
				// changing type from RUBRIC to ITEM
				if (($data['type'] != $rec['type'])
					&& ($data['type'] == 'item')) {
					// replace pics and recalc routing
					$this->_repl_pics_and_rem_dirs($rec['id'], $rec['alias']);
					// === route EDIT
					$paliases = implode('/',$this->_get_parent_aliases($rec['parent_id']));
					$this->_clear_routing_controllers($rec['alias'], $rec['type']);
					$this->_update(['type'=>$data['type']])->_where("`id`=".$rec['id'])->_execute();
					$this->_recalc_routing_controllers($paliases?$paliases:'catalog/'.$v['alias'], 'rubric', 'catalog');
					// ===============
				}
				
				// changing alias
				if (($data['alias'] != $rec['alias'])
					&& ($data['type'] == 'rubric')) {
					if (is_dir($this->pics->fpath.$rec['alias'])) {
						rename($this->pics->fpath.$rec['alias'],$this->pics->fpath.$data['alias']);
					}
					else {
						mkdir($this->pics->fpath.$data['alias'], 0777);
						mkdir($this->pics->fpath.$data['alias'].'/icons', 0777);
					}
				}
				// changing rubric for item, then rename item pics
				if (($rec['parent_id'] != $data['parent_id'])
					&& ($pics = $this->pics->get_by_cid($rec['id']))) {
					if ((int)$data['parent_id'])
						$new_parent = $this->get_by_id((int)$data['parent_id']);
					if ($rec['parent_id'])
						$old_parent = $this->get_by_id($rec['parent_id']);
					foreach ($pics as $v) {
						rename($this->pics->fpath.(!empty($old_parent)?$old_parent['alias'].'/':'').$v['name'],
								$this->pics->fpath.(!empty($new_parent)?$new_parent['alias'].'/':'').$v['name']);
						rename($this->pics->fpath.(!empty($old_parent)?$old_parent['alias'].'/':'').'icons/'.$v['name'],
								$this->pics->fpath.(!empty($new_parent)?$new_parent['alias'].'/':'').'icons/'.$v['name']);
					}
				}
				
				$ret = $rec['id'];
				$this->_update($data)->_where("`id`=".$ret)->_orderby("`id`")->_limit(1)->_execute();

				// ==== new route RULE
				if (($rec['parent_id'] != $data['parent_id'])
					|| ($data['alias'] != $rec['alias'])) {
					//Elf::routing()->_del_by_hash($rec['hash']);
					$this->_clear_routing_controllers($rec['alias'], $rec['type']);
					$palias = implode('/',$this->_get_parent_aliases($rec['id']));
					$this->_recalc_routing_controllers($palias?$palias:'catalog/'.$data['alias'], $data['type'], 'catalog');
				}
				// =====
			}
			else { // new REC
				$newrec = true;
				Elf::input()->set('pos',$this->get_last_pos($data['parent_id'],$data['type'])+1);
				if ($ret = $this->_insert($data)->_execute()) {
					if (!empty($data['feature'])
						&& sizeof($data['feature'])) {
						foreach ($data['feature'] as $k=>$v) {
							$this->cfv->_insert(['feature_id'=>$k,'catalog_id'=>$ret,'value'=>$v])->_execute();
						}
					}
				}
			}
			if ($ret && $data['alias']) {
				if ($data['type'] == 'rubric') {
					if (!is_dir($this->pics->fpath.$data['alias'])) {
						mkdir($this->pics->fpath.$data['alias'],0777);
						mkdir($this->pics->fpath.$data['alias'].'/icons',0777);
					}
				}
				if (!empty($newrec)) {
					$controller = $this->_get_parent_aliases($ret);
					$method = $data['type']=='rubric'?'*':array_pop($controller);
					if ($hash = Elf::routing()->_edit(implode('/',$controller),
											$method,
											'catalog',
											$data['type'],null,$data))
						$this->_update(['hash'=>$hash])->_where("`id`=".$ret)->_execute();
				}
			}
			if ($ret) {
//				$this->cfv->clear_vals_by_cid($ret);
				if (($rec['type'] == 'item')
					&& !isset($newrec)
					&& !empty($data['feature'])
					&& sizeof($data['feature'])) {
					foreach ($data['feature'] as $k=>$v) {
						$this->cfv->_upd_val($ret, $k, $v);
					}
				}
				$pics = $data['picture'];
				if (!empty($pics) && sizeof($pics)) {
					//$names = '';
					$palias = $this->_get_parent_alias($ret);
					foreach ($pics as $k=>$v) {
						if ($pic = $this->pics->get("`name`='".$v."' AND (`catalog_id`=".$ret." OR `catalog_id` IS NULL)","`catalog_id`")) {
							$this->pics->_update(['pos'=>$k,'catalog_id'=>$ret])
											->_where("`id`=".$pic['id'])
											->_orderby("`id`")
											->_limit(1)->_execute();
							if ($palias && is_file($this->pics->fpath.$v)) {
								rename($this->pics->fpath.$v,$this->pics->fpath.$palias.'/'.$v);
								rename($this->pics->ficons.$v,$this->pics->fpath.$palias.'/icons/'.$v);
							}
						}
					}
				}
			}
		}
		else {
			Elf::messagebox(Elf::$_data['error']);
		}
		return $ret;
	}
	function chk_req_fields() {
		$ret = true;
		Elf::$_data['error'] = '';
		if (!Elf::input()->get('name')) {
			Elf::$_data['error'] .= Elf::lang('catalog')->item('error.name');
			$ret = false;
		}
		if ((Elf::input()->get('type') == 'item')
			&& !(int)Elf::input()->get('parent_id')) {
			Elf::$_data['error'] .= Elf::lang('catalog')->item('error.item.parentid');
			$ret = false;
		}
		return $ret;
	}
	function _del($cid, $subs = false) {
		if ((is_numeric($cid) && ($rec = $this->get_by_id((int)$cid)))
			|| ($rec = $this->get("`alias`='".addslashes($cid)."'","`alias`"))) {
			if ($rec['type'] != 'clone') {
				if (!$subs) { // удаление без подрубрик
					Elf::routing()->_del_by_hash($rec['hash']);
					$paliases = implode('/',$this->_get_parent_aliases($rec['parent_id']));
					$this->pics->remfile_by_id($rec['id']);
					if ($rec['type'] == 'rubric') {
						$this->_repl_pics_and_rem_dirs($rec['id'], $rec['alias']);
					}
					$this->_delete()->_where("`id`=".$rec['id'])->_orderby("`id`")->_limit(1)->_execute();
					$this->_update(['parent_id'=>$rec['parent_id']])
							->_where("`parent_id`=".$rec['id'])
							->_execute();
					// === route EDIT
//					Elf::routing()->_del_by_hash($v['hash']);
					$this->_clear_routing_controllers($rec['alias'], $rec['type']);
					$this->_recalc_routing_controllers($paliases?$paliases:'catalog/'.$v['alias'], 'rubric', 'catalog');
					// ===============
				}
				else { // удаление со всеми дочерними элементами
					$childs = $rec['id'];
					$hashes = "'".$rec['hash']."'";
					if ($res = $this->_get_childs_ids($rec['id'])) { // получаем все дочерние элементы, для удаления
						foreach ($res as $v) {
							$childs .= ($childs?',':'').$v['id'];
							$hashes .= ($hashes?',':'')."'".$v['hash']."'";
						}
					}
					else
						$res = [];
					$res[] = $rec;
					$this->pics->remfile_by_id($childs);
					foreach ($res as $v) {
						if ($v['type'] == 'rubric') {
							@rmdir($this->pics->fpath.($v['alias']?$v['alias'].'/':'').'icons');
							@rmdir($this->pics->fpath.$v['alias']);
						}
					}
					$this->_delete()->_where("`id` IN (".$childs.")")->_execute();
					Elf::routing()->_delete()->_where("`hash` IN (".$hashes.")")->_execute();
				}
			}
			else {
				$this->_delete()->_where("`id`=".$rec['id'])->_orderby("`id`")->_limit(1)->_execute();
				Elf::routing()->_del_by_hash($rec['hash']);
			}
			return true;
		}
		return false;
	}
	function _get($cid, $lm = false) {
		if ((is_numeric($cid) && ($rec = $this->get_by_id((int)$cid)))
			|| ($rec = $this->get("`alias`='".addslashes($cid)."'","`alias`"))) {
			if ($ret = $this->_select("t2.*,t1.*")
						->_subquery('catalog','t3')
							->_select('t3.`name`')->_where('t3.`id`=t1.`parent_id`')
								->_and('t3.`type` IN ("rubric","item")')
								->_orderby("t3.`id`,t3.`type`")->_closesquery('parent_name')
						->_join('routing','t2','t2.`hash`=t1.`hash`')
						->_where("t1.`id`=".$rec['id'])
						->_orderby("t1.`id`")->_limit(1)->_execute()) {
				$ret = $ret[0];
				$ret['uri'] = implode('/',$this->_get_parent_aliases($ret['id']));
				
				Elf::$_data['title'] = $ret['title']?$ret['title']:($ret['inner_name']?$ret['inner_name']:$ret['name']);
				Elf::$_data['description'] = $ret['description'];
				Elf::$_data['keywords'] = $ret['keywords'];
				
				if ($rec['type'] != 'clone') {
//					$ret['features'] = $this->feats->_data($ret['id']);
//					print_r($ret['features']);exit;
					if (!($ret['features'] = $this->feats->_data($ret['id'])) && $ret['parent_id'])
//						&& ($ret['type'] == 'item'))
						$ret['features'] = $this->feats->_data($ret['parent_id']);
					$ret['pictures'] = $this->pics->get_by_cid($ret['id']);
				}
				else {
					$prev = $ret;
					$ret = $this->_get($ret['clone_id']);
					$ret['clone_id'] = $prev['clone_id'];
					$ret['id'] = $prev['id'];
					$ret['clone_alias'] = $ret['alias'];
					$ret['alias'] = $prev['alias'];
					$ret['avails'] = $this->_select("t1.`id`") // применимость для других моделей
									->_subquery('catalog','t3')
										->_select('t3.`alias`')->_where('t3.`id`=t1.`parent_id`')
											->_orderby("t3.`id`")->_closesquery('parent_alias')
									->_subquery('catalog','t2')
										->_select('t2.`name`')->_where('t2.`id`=t1.`parent_id`')
											->_orderby("t2.`id`")->_closesquery('parent_name')
								->_where("t1.`id`=".$ret['clone_id'])
									->_or("t1.`clone_id`=".$ret['clone_id'])
								->_orderby("t1.`id`,t1.`clone_id`")->_execute();
				}
				if ($lm) {
					$linemenu = Elf::load_template('catalog/linemenu',['paliases'=>$this->_get_parent_aliases($ret['id'])]);
				}
			}
		}
		if (!empty($ret)&&!empty($linemenu))
			$ret = array($ret,$linemenu);
		elseif (empty($ret))
			$ret = null;
		return $ret;
	}
//	function _get_features($cid) {
//		if ($res = Elf::load_model('catalog_features_values'))
//	}
	function _items($cid, $offset, $admin = false) {
		$ret = $pagi = $linemenu = null;
		if ($admin) {
			if ($rec = $this->_get((int)$cid)) {
				if ($ret = $this->_select("t2.*,t1.*")
						->_join('routing','t2','t2.`controller`=t1.`alias`')
						->_where("t1.`parent_id`=".(int)$cid)
						->_orderby("t1.`type`,t1.`parent_id`,t1.`pos`")
						->_limit(CATALOG_ITEMS_ON_PAGE,$offset*CATALOG_ITEMS_ON_PAGE)->_execute()) {
					foreach ($ret as $k=>$v) {
						$ret[$k]['uri'] = implode('/',$this->_get_parent_aliases($v['id']));
						if ($v['type'] == 'clone') {
							$ret[$k] = $this->_get($v['clone_id']);
							$ret[$k]['id'] = $v['id'];
							$ret[$k]['clone_id'] = $v['clone_id'];
							$ret[$k]['alias'] = $v['alias'];
						}
						else {
							$ret[$k]['features'] = $this->cfv->get_by_cid($v['id']);
						}
					}
					$pg = new \Elf\Libs\Pagination;
					$pagi = $pg->create('javascript:showCatalogItems("'.(int)$cid.'//")',
										$this->cnt("`parent_id`=".(int)$cid,"`parent_id`"),
										$offset,
										CATALOG_ITEMS_ON_PAGE,
										1);
				}
			}
		}
		else {
			if (!($rec = $this->_get($cid))) {
				$rec['id'] = 0;
			}
			if ($ret = $this->_select("t2.*,t1.*")
					->_subquery('catalog','t3')
						->_select('t3.`name`')->_where('t3.`id`=t1.`parent_id`')
							->_and('t3.`type` IN ("rubric","item")')
							->_orderby("t3.`parent_id`,t3.`type`")->_closesquery('parent_name')
					->_join('routing','t2','t2.`hash`=t1.`hash`')
					->_where("t1.`parent_id`=".$rec['id'])
					->_orderby("t1.`type`,t1.`pos`")
					->_limit(CATALOG_ITEMS_ON_PAGE,$offset*CATALOG_ITEMS_ON_PAGE)->_execute()) {
				foreach ($ret as $k=>$v) {
					$ret[$k]['uri'] = implode('/',$this->_get_parent_aliases($v['id']));
					if ($v['type'] == 'clone') {
						$ret[$k] = $this->_get($v['clone_id']);
						$ret[$k]['id'] = $v['id'];
						$ret[$k]['clone_id'] = $v['clone_id'];
						$ret[$k]['alias'] = $v['alias'];
						$ret[$k]['parent_name'] = $v['parent_name'];
					}
					else {
						$ret[$k]['features'] = $this->cfv->get_by_cid($v['id']);
					}
				}
				$pg = new \Elf\Libs\Pagination;
				$pagi = $pg->create(!empty($rec['alias'])?'/'.$rec['alias'].'/page/':'/main/page/',
											$this->cnt("`parent_id`=".$rec['id'],"`parent_id`"),
											$offset,
											CATALOG_ITEMS_ON_PAGE,
											3,!empty($rec['alias'])?'/'.$rec['alias']:'/','cat-items');
			}
			if (!$rec['id']) {
				$linemenu = Elf::load_template('catalog/linemenu',['paliases'=>$this->_get_parent_aliases($rec['id'])]);
			}
		}
		return [$ret, $pagi, $linemenu];
	}
	function _items_by_ids($ids) {
		if ($ret = $this->_select("t2.*,t1.*")
				->_subquery('catalog','t3')
					->_select('t3.`name`')->_where('t3.`id`=t1.`parent_id`')
						->_and('t3.`type` IN ("rubric","item")')
						->_orderby("t3.`parent_id`,t3.`type`")->_closesquery('parent_name')
				->_join('routing','t2','t2.`controller`=t1.`alias`')
				->_where("t1.`id` IN (".$ids.")")
				->_orderby("t1.`id`")->_execute()) {
			foreach ($ret as $k=>$v) {
				if ($v['type'] == 'clone') {
					$ret[$k] = $this->_get($v['clone_id']);
					$ret[$k]['id'] = $v['id'];
					$ret[$k]['clone_id'] = $v['clone_id'];
					$ret[$k]['alias'] = $v['alias'];
					$ret[$k]['parent_name'] = $v['parent_name'];
				}
				else
					$ret[$k]['features'] = $this->cfv->get_by_cid($v['id']);
			}
		}
		return $ret;
	}
	function _feature_add($cid, $fid) {
		if ((int)$cid && ($feat = $this->feats->_get($fid))) {
			switch ($feat['unit_type']) {
				case 'select':
				case 'radio':
				case 'checkbox':
					$value = explode("\n",$feat['prevalues']);
					$value = trim($value[0]);
					break;
				default:
					$value = $feat['prevalues'];
					break;
			}
			$this->cfv->_insert(['catalog_id'=>(int)$cid,'feature_id'=>$feat['id'],'value'=>$value])
							->_execute(null, true);
		}
		$this->upd_catalog_edit_fids($fid,'add');
	}
	function _feature_remove($cid, $fid) {
		if ((int)$cid && ($feat = $this->cfv->_get($cid, $fid))) {
			$this->cfv->_delete()->_where("`id`=".$feat['id'])->_execute();
			Elf::$_data['error'] = Elf::lang('catalog')->item('feature.removed',$feat['name']);
		}
		else
			Elf::$_data['error'] = Elf::lang('catalog')->item('feature.notfound.forcid');
		$this->upd_catalog_edit_fids($fid,'remove');
	}
	function add_group_with_features($cid, $gid) {
		$ret = '';
		if ($res = $this->feats->_group_features($gid, Elf::session()->get('catalog_fids'))) {
			foreach ($res as $v) {
				$ret .= Elf::load_template('catalog/feature_create',['feat'=>json_encode($v),'cid'=>(int)$cid]);
				if ((int)$cid) {
					switch ($v['unit_type']) {
							case 'select':
							case 'radio':
							case 'checkbox':
								$value = explode("\n",$v['prevalues']);
								$value = trim($value[0]);
								break;
							default:
								$value = $v['prevalues'];
								break;
					}
					$this->cfv->_insert(['catalog_id'=>(int)$cid,'feature_id'=>$v['id'],'value'=>$value])
								->_execute(null, true);
				}
			}
		}
		return $ret;
	}
	function _feature_group_remove($cid, $gid) {
		if ((int)$cid && ($feats = $this->cfv->get_by_cid($cid, $gid))) {
			$fids = '';
			foreach ($feats as $v) {
				$fids .= ($fids?',':'').$v['id'];
				$this->upd_catalog_edit_fids($v['feature_id'],'remove');
			}
			$this->cfv->_delete()->_where("`id` IN (".$fids.")")->_execute();
			Elf::$_data['error'] = Elf::lang('catalog')->item('group.removed');
		}
		else
			Elf::$_data['error'] = Elf::lang('catalog')->item('group.notfound.forcid');
		
	}
	private function upd_catalog_edit_fids($fid, $oper) {
		$ret = [];
		$fids = explode(",",Elf::session()->get('catalog_fids'));
		switch ($oper) {
			case 'remove':
				foreach ($fids as $f)
					if ($f != $fid)
						$ret[] = $f;
//				$fids = preg_replace("/(,".$fid.")|(".$fid.",)/","",$fids);
				break;
			case 'add':
				$ret = $fids?$fids:[];
				$ret[] = $fid;
				break;
		}
		Elf::session()->set('catalog_fids',implode(",",$ret));
	}
	function _feature_share($cid, $fid) {
		if (($feat = $this->cfv->_get($cid, $fid))
			&& ($childs = $this->_get_childs_ids((int)$cid, true))) {
			unset($feat['id']);
			$cnt = 0;
			foreach ($childs as $c) {
				$feat['catalog_id'] = $c['id'];
				if ($this->cfv->_insert($feat)->_execute(null, true))
					$cnt ++;
			}
			if (!$cnt)
				Elf::$_data['error'] = Elf::lang('catalog')->item('featureiset');
			else
				Elf::$_data['error'] = Elf::lang('catalog')->item('featureisetcnt', $cnt);
		}
		else
			Elf::$_data['error'] = Elf::lang('catalog')->item('direct.childs.notfound');
	}
	function _data($sel = 0) {
		return ['rubicator'=>$this->_rubicator(0,0,$sel),'cursel'=>(int)$sel];
	}
	function rubric_selector($sel, $cid = 0, $add = '') {
		$ret = '<select name="parent_id" '.$add.'><option value="0" data-paliases="">'.Elf::lang('catalog')->item('root').'</option>';
		if ($res = $this->_rubicator()) {
			foreach ($res as $v)
				$ret .= '<option value="'.$v['id'].'" data-paliases="'.$v['paliases'].'" '.($sel==$v['id']?'selected="selected"':($cid==$v['id']?'disabled="disabled"':'')).'>'.($v['level']?'|':'').str_repeat('_',$v['level']*3).'&nbsp;&nbsp;'.$v['name'].'</option>';
		}
		$ret .= '<option value="1">test</option>';
		$ret .= '</select>';
		return $ret;
	}
	private function _rubicator($root = 0, $level = 0, $sel = 0) {
		$ret = [];
		$items_cnt = 0;
		if ($res = $this->_select('*,'.$level.' as `level`')
					->_subquery('catalog','t2')
						->_select('count(t2.`id`)')->_where('t2.`parent_id`=t1.`id`')
							->_and('t2.`type`="rubric"')
							->_orderby("t2.`parent_id`,t2.`type`")->_closesquery('has_childs')
					->_subquery('catalog','t2')
						->_select('count(t2.`id`)')->_where('t2.`parent_id`=t1.`id`')
							->_and('t2.`type`="item"')
							->_orderby("t2.`parent_id`,t2.`type`")->_closesquery('items_cnt')
					->_where("`type`='rubric'")
						->_and("`parent_id`=".(int)$root)
					->_orderby("`type`,`parent_id`,`pos`")->_execute()) {
			$selbranch = false;
			foreach ($res as $k=>$v) {
//				$v['items_cnt'] = $this->cnt("`parent_id`=".$v['id']." AND `type`='item'","`parent_id`,`type`");
				$items_cnt += $v['items_cnt'];
			//	$v['has_childs'] += $childs;
				if ($v['id'] == (int)$sel) {
					$v['selected'] = true;
					if ($level)
						$selbranch = true;
				}
				$v['paliases'] = implode('/',$this->_get_parent_aliases($v['id']));
				$ret[] = $v;
				end($ret);
				$akey = key($ret);
			//	if ($this->get("`type`='rubric' AND `parent_id`=".$v['id'],"`type`,`parent_id`"))
			//	list($r,$childs) = $this->_rubicator($v['id'],$level+1);
				$ret = array_merge($ret,$this->_rubicator($v['id'],$level+1,$sel));
				$ret[$akey]['items_cnt'] += $ret['items_cnt'];
				$items_cnt += $ret['items_cnt'];
				unset($ret['items_cnt']);
				if (!empty($ret['selbranch'])) {
					$ret[$akey]['opened'] = true;
				}
				if (!$level && !empty($ret['selbranch'])) {
					unset($ret['selbranch']);
				}	
			}
		}
		return array_merge($ret,!$level?[]:['items_cnt'=>$items_cnt],empty($selbranch)?[]:['selbranch'=>true]);//a_rray($ret,$childs_cnt);
	}
	function units_selector($sel = '') {
		return $this->units->_selector($sel);
	}
	function ch_pos($cid,$direct) {
		if ($rec = $this->get_by_id((int)$cid)) {
			switch ($direct) {
				case 'up':
					$rec_sw = $this->get("`parent_id`=".$rec['parent_id']." AND `type`='".$rec['type']."' AND `pos`<".$rec['pos'],"`parent_id`,`type`,`pos` DESC");
					break;
				case 'dn':
					$rec_sw = $this->get("`parent_id`=".$rec['parent_id']." AND `type`='".$rec['type']."' AND `pos`>".$rec['pos'],"`parent_id`,`type`,`pos` ASC");
					break;
			}
			if (!empty($rec_sw)) {
				$this->_update(['pos'=>$rec_sw['pos']])->_where("`id`=".$rec['id'])->_limit(1)->_execute();
				$this->_update(['pos'=>$rec['pos']])->_where("`id`=".$rec_sw['id'])->_limit(1)->_execute();
			}
		}
	}
	function get_last_pos($pid,$type) {
		if ($rec = $this->get("`parent_id`=".(int)$pid." AND `type`='".$type."'","`parent_id`,`type`,`pos` DESC"))
			$ret = $rec['pos'];
		return isset($ret)?(int)$ret:-1;
	}
	function search($str, $limit = 0, $offset = 0) {
		if ($limit != 'all')
			return $this->_select("t2.*,t1.*")
				->_subquery('catalog','t2')
					->_select('count(t2.`id`)')->_where('t2.`parent_id`=t1.`id`')
						->_and('t2.`type`="item"')
						->_orderby("t2.`parent_id`,t2.`type`")->_closesquery('items_cnt')
				->_join('routing','t2','t2.`controller`=t1.`alias`')
				->_where("t1.`name` like '%".$str."%'")
				->_orderby("t1.`type`,t1.`pos`")->_limit((int)$limit?(int)$limit:100)->_execute();
		else {
			$ret = $pagi = $linemenu = null;
			if ($ret = $this->_select("t2.*,t1.*")
					->_subquery('catalog','t3')
						->_select('t3.`name`')->_where('t3.`id`=t1.`parent_id`')
							->_and('t3.`type` IN ("rubric","item")')
							->_orderby("t3.`parent_id`,t3.`type`")->_closesquery('parent_name')
					->_join('routing','t2','t2.`controller`=t1.`alias`')
					->_where("t1.`name` like '%".addslashes($str)."%'")
					->_orderby("t1.`type`,t1.`pos`")
					->_limit(CATALOG_ITEMS_ON_PAGE,$offset*CATALOG_ITEMS_ON_PAGE)->_execute()) {
				foreach ($ret as $k=>$v) {
					if ($v['type'] == 'clone') {
						$ret[$k] = $this->_get($v['clone_id']);
						$ret[$k]['id'] = $v['id'];
						$ret[$k]['clone_id'] = $v['clone_id'];
						$ret[$k]['alias'] = $v['alias'];
						$ret[$k]['parent_name'] = $v['parent_name'];
					}
					else {
						$ret[$k]['features'] = $this->cfv->get_by_cid($v['id']);
						$ret[$k]['pictures'] = $this->pics->get_by_cid($v['id']);
					}
				}
				//$pagi = $this->app->load_lib('pagination');
				$cnt = $this->cnt("`name` like '%".addslashes($str)."%'","`name`");
				$pg = new \Elf\Libs\Pagination;
				$pagi = $pg->create('/catalog/search/'.urlencode($str).'/',
									$cnt,
									$offset,
									CATALOG_ITEMS_ON_PAGE,
									4);
				Elf::load_template('catalog/linemenu',['paliases'=>$this->_get_parent_aliases($rec['id'])]);
				$linemenu = Elf::load_template('catalog/linemenu',
									['paliases'=>[Elf::lang('catalog')->item('searchres',$str,$cnt,1+$offset)=>'/']]);
				return [$ret, $pagi, $linemenu];
			}
		}
	}
}