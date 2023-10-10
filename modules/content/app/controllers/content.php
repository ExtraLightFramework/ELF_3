<?php

namespace Elf\App\Controllers;

use Elf;
use Elf\Libs\Tags;
//use Elf\App\Models\Content;

class Content extends \Elf\Controllers\Main {
	
	private $cont;
	private $tags;
	private $user_methods = ['rubric','tagsearch','alias_checker','page'];

/* ======= COMMON SECTION ===== */	
	function __construct() {
//		parent::__construct();
		if (!in_array(Elf::routing()->method_to(),$this->user_methods) && !(Elf::session()->get('group')&(GROUP_ADMIN|GROUP_TECH)))
			Elf::redirect();
		$this->cont = new Elf\App\Models\Content;
		$this->tags = new Elf\Libs\Tags;
		Elf::set_layout('content');
	}
	function rubric($offset = 0) {
//		if (empty($ctype))
		$palias = explode("/",Elf::routing()->controller());
		$palias = $palias[sizeof($palias)-1];
		//echo $palias;exit;
//		$this->cont->postinit($ctype);
		if (!Elf::is_xml_request()) {
			if (Elf::$_data['data'] = $this->cont->_data($palias, (int)$offset, false)) {
				if ((int)$offset) {
					Elf::$_data['title'] = Elf::$_data['data'][2]['title'].' #'.((int)$offset+1);
					Elf::$_data['description'] = Elf::$_data['data'][2]['description'].Elf::lang('content')->item('pageof',(int)$offset+1);
				}
				else {
					Elf::$_data['title'] = Elf::$_data['data'][2]['title'];
					Elf::$_data['description'] = Elf::$_data['data'][2]['description'];
				}
				Elf::$_data['keywords'] = Elf::$_data['data'][2]['keywords'];
				echo Elf::load_view('content/rubric_'.Elf::$_data['data'][2]['content_type']);
			}
			else
				Elf::redirect('main/_404');
		}
		else {
			list ($res,$p) = $this->cont->_data((int)$offset,false);
			if ($res) {
				$ret = '';
				foreach ($res as $v)
					$ret .= Elf::load_template('content/rubric_item_'.$v['content_type'],$v);
				echo json_encode(array('data'=>$ret,'butt'=>Elf::$_data['pagination.showmorebutt']));
			}
		}

	}
	function tagsearch($offset = 0) {
		$tag = urldecode(Elf::routing()->method());
		if (!Elf::is_xml_request()) {
			echo Elf::load_view('content/rubric',array('ctype'=>'TAGS','data'=>$this->tags->_data_by_tag($tag,(int)$offset, $this->cont->path, $this->cont->icons)));
		}
		else {
			list ($res,$p) = $this->tags->_data_by_tag($tag,(int)$offset, $this->cont->path, $this->cont->icons);
			if ($res) {
				$ret = '';
				foreach ($res as $v)
					$ret .= Elf::load_template('content/rubric_item',$v);
				echo json_encode(array('data'=>$ret,'butt'=>Elf::$_data['pagination.showmorebutt']));
			}
		}
	}
	function alias_checker() {
		$alias = Elf::gen_chpu(Elf::input()->get('text'));
		echo json_encode(array('status'=>$this->cont->check_alias($alias,Elf::input()->get('cid'))?'ok':'error','alias'=>$alias));
	}
	function page() {
//		echo Elf::routing()->method();exit;
		if (($data =  $this->cont->_get(Elf::routing()->method()?Elf::routing()->method():(isset($_GET['method'])?$_GET['method']:'')))
			&& !empty($data['content_type'])) {
			ELF::set_data('title',$data['title']);
			echo Elf::load_view('content/'.$data['content_type'],$data);
		}
		else
			Elf::redirect('main/_404',true);
	}
	
// ======= ADMIN SECTION ===========
	function index() {
		switch (func_num_args()) {
			case 0:
				$alias = null;
				$offset = 0;
				break;
			case 1:
				list ($alias) = func_get_args();
				$offset = 0;
				break;
			case 2:
			default:
				list ($alias, $offset) = func_get_args();
				break;
		}
		
		if ($alias)
			list($data['content'],$data['pagi']) = $this->cont->postinit($alias)->_data($alias, (int)$offset);
		else
			$data['content'] = $this->cont->structure(true);
		
		$data['breadcrumbs'] = $this->cont->bread_crumbs($alias, true);
		if ($alias && ($rec = $this->cont->get("`alias`='".addslashes($alias)."'"))) {
			$data['root_parent'] = $this->cont->_get_root_parent($rec['id']);
		}
		$data['offset'] = (int)$offset;
		$data['alias'] = $alias;
		$data['title'] = !empty($rec)?Elf::lang('content')->item('titlewithrub',$rec['title']):Elf::lang('content')->item('title');
		$data['ctype'] = !empty($rec)?$rec['content_type']:'';
		$data['parent_id'] = !empty($rec)?$rec['id']:0;
		echo Elf::set_layout('admin')->load_view('content/index',$data);
	}
	function test() {
		$cat = new \Elf\App\Models\Catalog_features_values;
echo $cat->_select("t3.*,t2.*,t1.*,t3.`name` as `unit_name`,t3.`type` as `unit_type`")
					->_join('catalog_features','t2','t2.`id`=t1.`feature_id`')
					->_join('catalog_units','t3','t3.`id`=t2.`unit_id`')
					->_where("`catalog_id`=5 OR `catalog_id`=6")
					->_groupby('`feature_id`')
					->_orderby('`pos`')
					->_prepare()->_sql();
	}
	function _edit() {
		$this->cont->_edit();
		Elf::redirect('content/index/'.Elf::input()->get('parent_alias').'/'.Elf::input()->get('offset'));
	}
	function _edit_rubric() {
		if (!$this->cont->_edit_rubric())
			Elf::messagebox(Elf::$_data['error']);
		Elf::redirect('content/structure');
	}
	function _del($id,$palias,$offset = 0) {
		$this->cont->_del((int)$id);
		Elf::redirect('content/index/'.$palias.'/'.(int)$offset);
	}
	function rubric_del($id) {
		if (!$this->cont->_del_rubric((int)$id)) {
			Elf::messagebox(Elf::get_data('error'));
		}
		Elf::redirect('content/structure');
	}
	function refill_positions($alias) {
		$this->cont->refill_positions($alias);
		Elf::redirect('content/index/'.$alias);
	}
	function rss($alias = '') {
		$this->cont->rss();
		Elf::routing()->sitemap();
		Elf::messagebox(Elf::lang('content')->item('rss.successfull'));
		Elf::redirect('content/index/'.$alias);
	}
	function set_new_pos() {
		echo $this->cont->postinit(Elf::input()->get('ctype'))->set_new_pos((int)Elf::input()->get('id'),(int)Elf::input()->get('pos'));
		exit;
	}
	function sw_visible() {
		echo $this->cont->sw_visible(Elf::input()->get('id'),Elf::input()->get('visible'));
		exit;
	}
	function ch_pos($id, $direct, $parent_id, $parent_alias, $offset = 0) {
		$this->cont->ch_pos((int)$id, (int)$parent_id, addslashes($direct));
		Elf::redirect('content/index/'.$parent_alias.'/'.(int)$offset);
	}
	function upload() {
		echo json_encode($this->cont->postinit(Elf::input()->get('ctype'))->_upload('uplfile'));
	}
	function structure() {
		echo Elf::set_layout('admin')->load_view('content/structure',array('structure'=>$this->cont->get_structure()));
	}
	function get_rubric_submnu() {
		if ($res = $this->cont->get_structure(Elf::input()->get('pid')))
			echo Elf::load_template('content/rubric_submnu',array('items'=>$res,'pid'=>Elf::input()->get('pid'),'alias'=>Elf::input()->get('alias')));
	}
	function set_order_type($palias = '', $offset, $parent_id, $type) {
		$this->cont->set_order_type($parent_id, $type);
		Elf::redirect('content/index/'.$palias.'/'.(int)$offset);
	}
}