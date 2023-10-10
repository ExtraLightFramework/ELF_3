<?php

namespace Elf\App\Models;

use Elf;

define ('CONTENT_PATH',			'app/content/');
define ('CONTENT_FPATH',		ROOTPATH.CONTENT_PATH);
define ('CONTENT_FILE_EXT',		'.html');

define ('CONTENT_IMAGE_XSIZE',	2000);
define ('CONTENT_IMAGE_YSIZE',	600);
define ('CONTENT_ICON_XSIZE',	400);
define ('CONTENT_ICON_YSIZE',	120);

class Content extends Elf\Libs\Structure {
	
	private $parent_alias;
//	private $types;
	private $tags;
	
	function __construct() {
		parent::__construct('content','content');
		$this->parent_alias = null;
		$this->tags = new Elf\Libs\Tags;
	}
	function postinit($alias) {
		if ($alias && in_array($alias, $this->_get_rubric_aliases()))
			$this->parent_alias = $alias;
		else
			$this->parent_alias = null;
//			throw new \Exception ('Content rubric alias <b>'.$alias.'</b> is wrong.');
		return $this;
	}
	function post_init($dir) {
		parent::post_init($dir);
		$this->image_xsize = CONTENT_IMAGE_XSIZE;
		$this->image_ysize = CONTENT_IMAGE_YSIZE;
		$this->icon_xsize = CONTENT_ICON_XSIZE;
		$this->icon_ysize = CONTENT_ICON_YSIZE;
	}
	function check_alias($alias, $cid) {
		return $this->get("`alias`='".$alias."'".((int)$cid?" AND `id`!=".(int)$cid:""))?false:true;
	}
	function structure($admin = false) {
		if ($ret = $this->get_structure()) {
			foreach ($ret as $k=>$v) {
				if ($paliases = $this->_get_parent_aliases($v['id']))
					$paliases = implode('/',$paliases);
				$ret[$k]['items'] = $this->_select("*,'".$paliases."' as `paliases`")
										->_where("`parent_id`=".$v['id']." AND `type`='item'".(!$admin?" AND `visible`=1":""))
										->_orderby($v['content_order_type']?$v['content_order_type']:"`tm` DESC")
										->_limit(3)
										->_execute();
				$ret[$k]['childs_cnt'] = sizeof($ret[$k]['items']??[]);
			}
		}
		return $ret;
	}
	function get_structure($pid = 0, $level = 0) {
		$ret = array();
		if ($res = $this->_select("t1.*,".$level." as `level`")
						->_subquery("content","t2")->_select("COUNT(t2.`id`)")
									->_where("t2.`parent_id`=t1.`id` AND t2.`type`='rubric'")->_closesquery("childs_cnt")
						->_where("t1.`parent_id`=".(int)$pid." AND t1.`type`='rubric'")->_execute()) {
			foreach ($res as $v) {
				$ret[] = $v;
				if (!$level && $v['childs_cnt']) {
					$ret = array_merge($ret, $this->get_structure($v['id'],$level+1));
				}
			}
		}
		return sizeof($ret)?$ret:null;
	}
	function get_structure_1_level($pid = 0, $parent_aliases = false) {
		if ($ret = $this->_select()->_subquery("content","t2")->_select("COUNT(t2.`id`)")
									->_where("t2.`parent_id`=t1.`id` AND t2.`type`='rubric'")->_closesquery("childs_cnt")
							->_where("`parent_id`=".(int)$pid." AND t1.`type`='rubric'")->_execute()) {
			if ($parent_aliases) {
				foreach ($ret as $k=>$v)
					$ret[$k]['full_alias'] = '/'.implode('/',$this->_get_parent_aliases($v['id']));
			}
		}
		return $ret;
	}
	function rubric_selector($cur = 0, $parent_id = 0) {
		$ret = Elf::lang('content')->item('rubrics.not.found');
		if ($struct = $this->get_structure()) {
			$ret = '<select name="parent_id"><option value="0"'.($parent_id==0?' selected="selected"':'').'>'.Elf::lang('content')->item('root').'</option>';
			foreach ($struct as $v) {
				$ret .= '<option value="'.$v['id'].'"'.($v['id']==$cur?' disabled="disabled"':'').($parent_id==$v['id']?' selected="selected"':'').'>'.($v['level']?'|'.str_repeat('__',$v['level']):'').$v['title'].'</option>';
			}
			$ret .= '</select>';
		}
		return $ret;	
	}
	function content_type_selector($sel = '') {
		if ($res = Elf::settings()->_data("`name` LIKE 'CONTENT_TYPE_%'")) {
			$ret = '<select name="content_type">';
			$ret .= '<option value="">'.Elf::lang('content')->item('select.content_type').'</option>';
			foreach ($res as $v) {
				$ret .= '<option value="'.$v['value'].'" '.($v['value']==$sel?'selected="selected"':'').'>'.$v['desc'].'</option>';
			}
			$ret .= '</select>';
		}
		else
			$ret = Elf::lang('content')->item('content_types.not.found');
		return $ret;
	}
	function _data($parent_id = 0, $offset = 0, $admin = true) {
		$ret = $pagi = null;
		if (($parent = $this->_get($parent_id, true)) && isset($parent['id'])) {
			if ($ret = $this->_select("t1.*,
									concat('/','".$this->path."',t1.`picture`) as `picture_image`,
									concat('/','".$this->icons."',t1.`picture`) as `picture_icon`,
									'".$parent['content_order_type']."' AS `content_order_type`")
							->_where("`parent_id`='".$parent['id']."' AND `type`='item'".(!$admin?" AND `visible`=1":""))
							->_orderby(!$parent['content_order_type']?"`tm` DESC":$parent['content_order_type'])
							->_limit($parent['content_type']!=CONTENT_TYPE_FAQ?RECS_ON_PAGE:10000,$parent['content_type']!=CONTENT_TYPE_FAQ?(int)$offset*RECS_ON_PAGE:0)
							->_execute()) {
				Elf::$_data['breadcrumbsline'] = $this->bread_crumbs($parent['alias'], $admin);
				foreach ($ret as $k=>$v) {
					$ret[$k]['paliases'] = $this->_get_parent_aliases($v['parent_id']);
					$ret[$k]['tags'] = $this->tags->_get_content_tags($v['id'],5,"`freq` DESC");
				}
				if ($parent['content_type'] != CONTENT_TYPE_FAQ) {
					$pg = new Elf\Libs\Pagination;
					if ($admin)
						$pagi = $pg->create('/content/index/'.$parent['alias'].'/',
												$this->cnt("`parent_id`='".$parent['id']."' AND `type`='item'"), 
												(int)$offset, RECS_ON_PAGE, 4, 
												'/content/index/'.$parent['alias']);
					else
						$pagi = $pg->create('/'.$parent['alias'].'//',
												$this->cnt("`parent_id`='".$parent['id']."' AND `type`='item' AND `visible`=1"), 
												(int)$offset, RECS_ON_PAGE, 3, 
												'/'.$parent['alias'].'//','rubric-items');
					unset($pg);
				}
			}
		}
		return [$ret, $pagi, $parent];
	}
	function _get($aliasid, $admin = false, $parent_id = 0) {
		$rec = [];
		if (is_numeric($aliasid)) {
			$rec = $this->get_by_id((int)$aliasid);
		}
		else {
			$rec = $this->get("`alias`='".$aliasid."'","`alias`");
		}
		if ($rec) {
			if ($admin || (!$admin && $rec['visible'])) {
				if ($rec['type'] == 'item') {
					$rec['paliases'] = $this->_get_parent_aliases($rec['parent_id']);
				}
				if ($rec['picture']) {
					$rec['picture_image'] = '/'.$this->path.$rec['picture'];
					$rec['picture_icon'] = '/'.$this->icons.$rec['picture'];
				}
				$rec['tags'] = $this->tags->_get_content_tags($rec['id']);
				Elf::$_data['breadcrumbsline'] = $this->bread_crumbs($rec['alias'], $admin);
			}
			else
				$rec = [];
		}
		else
			$rec = [];
		$rec['pos'] = !$rec?$this->get_last_pos($parent_id):$rec['pos'];
		return $rec;
	}
	function _edit() {
		$data = Elf::input()->data();
		$ret = null;
		if ($parent = $this->_get((int)$data['parent_id'],true)) {
			$data['text'] = Elf::input()->get('text',false);
			$data['first_p'] = Elf::input()->get('first_p',false);
			$data['description'] = Elf::input()->get('description',false);
			$data['tm'] = Elf::date2timestamp($data['tm'])+(date('G')*SECONDS_IN_HOUR)+((int)date('i')*60)+(int)date('s');
			$data['tm_edit'] = time();
			$data['type'] = 'item';//$this->type;
			$data['content_type'] = $parent['content_type'];
			$data['paliases'] = $this->_get_parent_aliases($parent['id']);
			$retid = null;
			if ($data['content_type'] == CONTENT_TYPE_VIDEO) {
				if (preg_match_all("/http(s)?:\/\/(www\.)?(youtube|youtu)\.(be|com)\/(embed\/|watch\?v=)?([-_\w]+)$/",$data['picture'],$matches)
				&& !empty($matches[6][0]))
					$data['picture'] = $matches[6][0];
				else
					$data['picture'] = '';
			}
			if (empty($data['picture']))
				$data['picture'] = '';
			
			
			if (!empty($data['hot'])) {
				$data['hot'] = 1;
			}
			else {
				$data['hot'] = 0;
			}
			if ((int)$data['id'] && ($rec = $this->_get((int)$data['id'],true))) { // ITEM is exists
				if ($rec['picture'] && ($rec['content_type'] != CONTENT_TYPE_VIDEO)) {
					$data['picture'] = pathinfo($data['picture'],PATHINFO_BASENAME);
					if ($rec['picture'] != $data['picture']) {
						@unlink($this->fpath.$rec['picture']);
						@unlink($this->ficons.$rec['picture']);
					}
				}
				if (($data['alias'] != $rec['alias']) 
					|| ($data['paliases'] != $rec['paliases'])) {// && ($rec['type'] != CONTENT_TYPE_VIDEO)) {
					Elf::routing()->_del(implode('/',$rec['paliases']),$rec['alias']);
				}
				if ($rec['content_type'] != CONTENT_TYPE_VIDEO) {
					$data['text'] = $this->_repl_cont_images($data['text'],$data['alias']);
				}
				$ret = $this->_update($data)->_where("`id`=".$rec['id'])->_execute();
				$retid = $rec['id'];
			}
			else { // ITEM is new
				if (isset($data['id']))
					unset($data['id']);
				//$data['pos'] = $this->get_last_pos();
				$data['text'] = $this->_repl_cont_images($data['text'],$data['alias']);
				$retid = $ret = $this->_insert($data)->_execute();
			}
			if (!empty($data['alias'])) {
				Elf::routing()->_edit(implode('/',$data['paliases']),$data['alias'],'content','page');
			}
			if ($retid && !empty($data['picture']) && ($data['content_type'] != CONTENT_TYPE_VIDEO)) {
				$pic = new \Elf\Libs\Image($this->fpath.$data['picture']);
				$this->_update(['picture_w'=>$pic->get_w(),'picture_h'=>$pic->get_h(),'picture_ornt'=>$pic->get_orient()])
						->_where("`id`=".$retid)->_execute();
				$banner = new \Elf\Libs\Banners('content');
				if ($data['hot'])
					$banner->_create(pathinfo($data['picture'], PATHINFO_FILENAME),
										[
											'img'=>'/'.$this->path.$data['picture'],
											'alt'=>$data['picture_alt'],
											'id'=>$retid,
											'href'=>'/'.implode('/',$data['paliases']).'/'.$data['alias'],
											'title'=>$data['title'],
											'author'=>'Serb'
										]);
				else
					$banner->_remove(pathinfo($data['picture'], PATHINFO_FILENAME));
				unset($pic);
			}
			if ($retid && $data['tags']) { // update hash-tags
				$tag = explode(" ",$data['tags']);
				foreach ($tag as $t) {
					if ($t) {
						$this->tags->_edit($t, $retid);
					}
				}
			}
		}
		return $ret;
	}
	function _edit_rubric() {
		Elf::$_data['error'] = '';
		$data = Elf::input()->data();
		$data['tm'] = $data['tm_edit'] = time();
		$data['type'] = 'rubric';
		$ret = true;
		if (empty($data['title'])) {
			Elf::$_data['error'] .= Elf::lang('content')->item('error.title');
			$ret = false;
		}
		if (empty($data['alias']) || $this->get("`alias`='".$data['alias']."' AND `id`!=".(int)$data['id'])) {
			Elf::$_data['error'] .= Elf::lang('content')->item('error.alias');
			$ret = false;
		}
		if ((int)$data['id'] && ((int)$data['id'] == (int)$data['parent_id'])) {
			Elf::$_data['error'] .= Elf::lang('content')->item('error.parentid');
			$ret = false;
		}
		if ((int)$data['parent_id'] && !$this->get_by_id((int)$data['parent_id'])) {
			Elf::$_data['error'] .= Elf::lang('content')->item('error.exists.parentid');
			$ret = false;
		}
		if ($ret) {
			if ((int)$data['id'] && ($rec = $this->_get((int)$data['id'],true))) { // UPD
				$old_paliases = implode('/',$this->_get_parent_aliases($rec['parent_id'])).'/'.$data['alias'];
				
				// if new parent id IS child editing REC
				if ($this->_is_child($rec['id'],$data['parent_id'])
					&& ($child = $this->get_by_id($data['parent_id']))) {
					$this->_update(['parent_id'=>$rec['parent_id']])->_where("`id`=".$data['parent_id'])->_execute();
				}
				
				// if content_type was changed
				if ($data['content_type'] != $rec['content_type']) {
					$this->_update(['content_type'=>$data['content_type']])
							->_where("`parent_id`=".$rec['id'])
								->_and("`type`='item'")
							->_execute();
				}
				
				// UPD
				unset($data['tm']);
				$ret = $this->_update($data)->_where("`id`=".$rec['id'])->_execute();
				$root_parent = $this->_get_root_parent($rec['id']);
				if ($data['parent_id'] != $rec['parent_id'])
					Elf::routing()->_delete()->_where("`controller` LIKE '%".$rec['alias']."%'")->_execute();
			}
			else { // NEW
				if (isset($data['id']))
					unset($data['id']);
				if ($ret = $this->_insert($data)->_execute())
					$root_parent = $this->_get_root_parent($ret);
			}
			if (!empty($root_parent)) {
				Elf::routing()->_delete()->_where("`controller` LIKE '%".$root_parent['alias']."%'")->_execute();
				$this->_recalc_routing_controllers($root_parent['alias'],'rubric','content');
			}
		}
		return $ret;
	}
	function _last($alias, $cnt = 5) {
		if (($parent = $this->_get($alias, true)) && isset($parent['id'])) {
			return $this->_select("t1.*,
									concat('/','".$this->path."',t1.`picture`) as `picture_image`,
									concat('/','".$this->icons."',t1.`picture`) as `picture_icon`")
							->_where("`parent_id`='".$parent['id']."' AND `type`='item' AND `visible`=1")
							->_orderby(!$parent['content_order_type']?"`tm` DESC":$parent['content_order_type'])
							->_limit((int)$cnt)
							->_execute();
		}
		return null;
	}
	function last_hots($cnt = 3) {
		return $this->_select("t1.*,
							concat('/','".$this->path."',t1.`picture`) as `picture_image`,
							concat('/','".$this->icons."',t1.`picture`) as `picture_icon`")
				->_where("`hot`=1")
				->_orderby("`hot`,`tm` DESC")
				->_limit((int)$cnt)
				->_execute();
	}
	function refill_positions($alias) {
		if (($rec = $this->_select("`id`")
						->_where("`alias`='".addslashes($alias)."'")
							->_and("`type`='rubric'")
						->_execute())
			&& ($ret = $this->_select("`id`")
								->_where("`parent_id`=".$rec[0]['id'])
									->_and("`type`='item'")
								->_execute())) {
			$pos = 1;
			do {
				$idx = rand(0,sizeof($ret)-1);
				$this->_update(['pos'=>$pos])->_where("`id`=".$ret[$idx]['id'])->_limit(1)->_execute();
				$pos ++;
				unset($ret[$idx]);
				$ret = array_values($ret);
			} while (sizeof($ret));
		}
	}
	function _del($id) {
		if ($rec = $this->_get((int)$id,true)) {
			if ($rec['picture'] && ($rec['content_type'] != CONTENT_TYPE_VIDEO)) {
				@unlink($this->fpath.$rec['picture']);
				@unlink($this->ficons.$rec['picture']);
			}
//			@unlink(CONTENT_FPATH.$rec['type'].'/'.$rec['alias'].CONTENT_FILE_EXT);
			$paliases = $this->_get_parent_aliases($rec['parent_id']);
			Elf::routing()->_del(implode('/',$paliases),$rec['alias']);
			$this->tags->_del_all_tags_from_content($rec['id']);
			$this->_delete()->_where("`id`=".$rec['id'])->_limit(1)->_execute();
		}
	}
	function _del_rubric($id) {
		$ret = true;
		if (($rec = $this->_get((int)$id,true))
			&& ($rec['type'] == 'rubric')) {
			if (!$rec['parent_id']
				&& $this->cnt("`parent_id`=".$rec['id'])) {
				$ret = false;
				Elf::set_data('error',Elf::lang('content')->item('error.rubric.del'));
			}
			else {
				Elf::routing()->_delete()->_where("`controller` LIKE '%".$rec['alias']."%'")->_execute();
				$childs = $this->_get_childs_ids($rec['id'],true);
				$this->_delete()->_where("`id`=".$rec['id'])->_execute();
				if ($childs) {
					$this->_update(['parent_id'=>$rec['parent_id']])->_where("`id` IN (".implode(',',$childs).")")->_execute();
					if (!$rec['parent_id']) {
						if ($res = $this->_select()->_where("`id` IN (".implode(',',$childs).")")->_execute()) {
							foreach ($res as $v)
								$this->_recalc_routing_controllers($v['alias'],$v['type'],'content');
						}
					}
					elseif ($res = $this->get_by_id($rec['parent_id']))
						$this->_recalc_routing_controllers($res['alias'],$res['type'],'content');
				}
			}
		}
		return $ret;
	}
	function set_new_pos($id, $pos) {
		if ($rec = $this->get_by_id((int)$id)) {
			$this->_update(['pos'=>(int)$pos])->_where("`id`=".$rec['id'])->_execute();
			if ($arec = $this->get("`parent_id`=".$rec['parent_id']." AND `pos`=".(int)$pos))
				$this->_update(['pos'=>$rec['pos']])->_where("`id`=".$arec['id'])->_execute();
			return 'ok';
		}
		return 'error';
	}
	function sw_visible($id, $vis) {
		if ($rec = $this->get_by_id((int)$id)) {
			$this->_update(['visible'=>(int)$vis])->_where("`id`=".$rec['id'])->_limit(1)->_execute();
			return 'ok';
		}
		return 'error';
	}
	function set_order_type($pid, $tp) {
		if (($rec = $this->get_by_id((int)$pid))
			&& ($rec['type'] == 'rubric')
			&& (($tp = base64_decode($tp)) !== false)) {
			$this->_update(['content_order_type'=>$tp])->_where("`id`=".$rec['id'])->_execute();
		}
	}
	function ch_pos($id, $parent_id, $direct) {
		if ($rec = $this->get_by_id((int)$id)) {
			switch ($direct) {
				case 'up':
					$ch = $this->get("`pos`<=".($rec['pos']-1)." AND `parent_id`=".$parent_id,"`pos` DESC");
					break;
				case 'dn':
					$ch = $this->get("`pos`>=".($rec['pos']+1)." AND `parent_id`=".$parent_id,"`pos` ASC");
					break;
			}
			if (!empty($ch)) {
				$this->_update(['pos'=>$ch['pos']])->_where("`id`=".$rec['id'])->_limit(1)->_execute();
				$this->_update(['pos'=>$rec['pos']])->_where("`id`=".$ch['id'])->_limit(1)->_execute();
			}
		}
	}
	function get_last_pos($parent_id) {
	//	echo "`type`='".$this->type."'";exit;
		if ($ret = $this->get("`parent_id`=".$parent_id,"`pos` DESC")) {
			$ret = $ret['pos'] + 1;
		}
		else
			$ret = 1;
		return $ret;
	}
	function bread_crumbs($aliasid, $admin = false) {
		$ret = '';
		if ((is_numeric($aliasid) && ($rec = $this->get_by_id((int)$aliasid)))
			|| (is_string($aliasid) && ($rec = $this->get("`alias`='".$aliasid."'")))) {
				
			$ret = Elf::load_template('content/breadcrumbs',['title'=>$rec['title'],
																'paliases'=>$this->_get_parent_aliases($rec['parent_id']),
																'admin'=>$admin]);	
		}
		return $ret;
	}
	function rss() {
		if ($ret = $this->_select("t1.*,
									concat('/','".$this->path."',t1.`picture`) as `picture_image`,
									concat('/','".$this->icons."',t1.`picture`) as `picture_icon`")
							->_where("`type`='item' AND `visible`=1")
							->_orderby("`id`")
							->_execute()) {
			$items  = Elf::load_template('content/rss/channel');
			$items .= Elf::load_template('content/rss/index');
			foreach ($ret as $v) {
				$v['paliases'] = implode("/",$this->_get_parent_aliases($v['parent_id']));
				$v['link'] = Elf::site_url().$v['paliases'].'/'.$v['alias'].'/';
				$v['date'] = date('D, d M y H:i:s O',$v['tm']);
				$v['category'] = Elf::lang('content')->item('rss.categoty.'.$v['content_type']);
				$v['author'] = Elf::lang('content')->item('rss.author');
				$v['header'] = Elf::load_template('content/rss/header',
																['title'=>$v['title'],
																	'img'=>Elf::site_url(false).($v['picture']?$v['picture_image']:Elf::lang('content')->item('rss.defaultimgpath'))]);
				$v['content'] = html_entity_decode($v['text']);
				if (!empty($v['first_p']))
					$v['content'] = html_entity_decode($v['first_p']).$v['content'];
				$items .= Elf::load_template('content/rss/item',$v);
				// AMP Creator
				if ($v['paliases']) {
					$dir = '';
					foreach (explode("/",$v['paliases']) as $a) {
						$dir .= $a.'/';
						if (!is_dir(ROOTPATH.'amp/'.$dir))
							mkdir(ROOTPATH.'amp/'.$dir);
					}
				}
				if ($f = fopen(ROOTPATH.'amp/'.$v['paliases'].'/'.$v['alias'].'.html','w')) {
					$v['header'] = str_replace("<img","<amp-img layout=\"responsive\" width=\"600\" height=\"400\"",$v['header']);
					$v['content'] = str_replace("<img","<amp-img layout=\"responsive\" width=\"600\" height=\"400\"",$v['content']);
					fwrite($f,Elf::load_template('content/amp/tmpl',$v));
					fclose($f);
				}
				// --- AMP
			}
			if ($f = fopen(ROOTPATH.'rss.xml','w')) {
				fwrite($f,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".Elf::load_template('content/rss/rss',['items'=>$items]));
				fclose($f);
			}
		}
	}
}
