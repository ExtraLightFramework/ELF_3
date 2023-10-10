<?php

namespace Elf\App\Controllers;

use Elf;

class Catalog extends \Elf\Controllers\Main {
	
	private $cat;
	private $user_methods = ['rubric','item','alias_checker','search','test'];
	
	function __construct() {
//		parent::__construct();
		if (!in_array(Elf::routing()->method_to(),$this->user_methods) && !(Elf::session()->get('group')&GROUP_ADMIN))
			Elf::redirect();
		$this->cat = new \Elf\App\Models\Catalog;
	}
	function rubric($offset = 0) {
		$alias = explode('/', Elf::routing()->controller());
		$alias = array_pop($alias);
		if (!Elf::is_xml_request()) {
			$itms = $this->cat->_items($alias,(int)$offset);
			if ((int)$offset)
				Elf::$_data['title'] .= Elf::lang('catalog')->item('thepage',(int)$offset+1);
			echo Elf::load_view('catalog/index',$itms);
		}
		else {
			list ($res,$p,$l) = $this->cat->_items($alias,(int)$offset);
			if ($res) {
				$ret = '';
				foreach ($res as $v)
					$ret .= Elf::load_template('catalog/item_templ',['item'=>$v]);
				echo json_encode(['data'=>$ret,'butt'=>Elf::$_data['pagination.showmorebutt']]);
			}
		}
	}
	function item() {
//		echo Elf::routing()->method();exit;
		if ($rec = $this->cat->_get(Elf::routing()->method(),true)) {
//			print_r($rec);exit;
			if ($rec[0]['clone_id'])
				$rec['catalog.seo'] = '<link rel="canonical" href="'.Elf::site_url().$rec[0]['clone_alias'].'" />';
			echo Elf::load_view('catalog/item',$rec);
		}
		else
			Elf::redirect('main/_404');
	}
	function alias_checker() {
		$alias = Elf::gen_chpu(Elf::input()->get('text'));
		echo json_encode(array('status'=>$this->cat->check_alias($alias,Elf::input()->get('cid'))?'ok':'error','alias'=>$alias));
	}
	function search($str = '', $offset = 0) {
		if (!empty($str)) {
			echo Elf::load_view('catalog/index',$this->cat->search(urldecode($str),'all',(int)$offset));
		}
		else
			echo Elf::load_template('catalog/search',array('srch'=>$this->cat->search(Elf::input()->get('srch'),10)));
	}
	function test() {
		print_r($this->cat->_get_childs_ids(21));
	}
// =============================	
// ===== ADMIN SECTION =========
// =============================

	function admin($sel = 0) {
		echo Elf::set_layout('admin')->load_view("catalog/admin",$this->cat->_data($sel));
	}
	function items() {
		echo Elf::load_template("catalog/items",
							$this->cat->_items((int)Elf::input()->get('cid'),
												(int)Elf::input()->get('offset'),true));
	}
	function edit() {
		$this->cat->_edit();
		Elf::redirect('catalog/admin/'.(int)Elf::input()->get('cursel'));
	}
	function chk_reg_fields() {
		if ($this->cat->chk_req_fields())
			echo 'ok';
		else
			echo Elf::$_data['error'];
	}
	function del() {
//		$this->cat->_del(29,false);
//		exit;
		if (!($ret = $this->cat->_del((int)Elf::input()->get('cid'),Elf::input()->get('subs')=='recursive'?true:false)))
			echo json_encode(['error'=>Elf::lang('catalog')->item('error.del.item')]);
		else
			echo json_encode(['ok'=>1]);
//		Elf::redirect('catalog/admin/'.(int)Elf::input()->get('cursel'));
	}
	function ch_pos($cid, $direct, $sel) {
		$this->cat->ch_pos($cid,$direct);
		Elf::redirect('catalog/admin/'.(int)$sel);
	}
// ======= FEATURES
	function features() {
		$feats = new \Elf\App\Models\Catalog_features;
		echo Elf::set_layout('admin')->load_view("catalog/features",['feats'=>$feats->_data()]);
	}
	function feature_edit() {
		$feats = new \Elf\App\Models\Catalog_features;
		if (Elf::is_xml_request())
			echo $feats->_edit();
		else {
			if (!($ret = $feats->_edit()))
				Elf::messagebox($ret);
			Elf::redirect('catalog/features');
		}
	}
	function feature_group_edit() {
		$feats = new \Elf\App\Models\Catalog_features;
		if (!($ret = $feats->_edit_group()))
			Elf::messagebox(Elf::$_data['error']);
		Elf::redirect('catalog/features');
	}
	function feature_add() {
		$this->cat->_feature_add(Elf::input()->get('cid'),Elf::input()->get('fid'));
//		echo json_encode(['alert'=>Elf::$_data['error']]);
	}
	function feature_remove() {
		$this->cat->_feature_remove(Elf::input()->get('cid'),Elf::input()->get('fid'));
		echo json_encode(['alert'=>Elf::$_data['error']]);
	}
	function feature_share() {
		$this->cat->_feature_share(Elf::input()->get('cid'),Elf::input()->get('fid'));
		echo json_encode(['alert'=>Elf::$_data['error']]);
	}
	function add_group_with_features() {
		echo $this->cat->add_group_with_features(Elf::input()->get('cid'),Elf::input()->get('gid'));
	}
	function group_info() {
		$feats = new \Elf\App\Models\Catalog_features;
		Elf::set_data('cid', (int)Elf::input()->get('cid'));
		echo Elf::load_template('catalog/feature_create',['feat'=>json_encode($feats->_get(Elf::input()->get('gid')))]);
	}
	function group_remove() {
		$this->cat->_feature_group_remove(Elf::input()->get('cid'),Elf::input()->get('gid'));
		echo json_encode(['alert'=>Elf::$_data['error']]);
	}
// ======= UNITS
	function units() {
		$units = new \Elf\App\Models\Catalog_units;
		echo Elf::set_layout('admin')->load_view("catalog/units",['units'=>$units->_data()]);
	}
	function edit_unit() {
		$units = new \Elf\App\Models\Catalog_units;
		$units->_edit();
		Elf::redirect('catalog/units');
	}
}
