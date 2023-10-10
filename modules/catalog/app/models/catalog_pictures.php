<?php

namespace Elf\App\Models;

use Elf;

define ('CATALOG_IMGS_PATH',	'img/galery/%%albumdir%%/');
define ('CATALOG_ICONS_PATH',	'img/galery/%%albumdir%%/icons/');
define ('CATALOG_IMGS_FPATH',	ROOTPATH.CATALOG_IMGS_PATH);
define ('CATALOG_ICONS_FPATH',	ROOTPATH.CATALOG_ICONS_PATH);
define ('CATALOG_ICON_XSIZE',	150);
define ('CATALOG_ICON_YSIZE',	150);
define ('CATALOG_IMAGE_XSIZE',	600);
define ('CATALOG_IMAGE_YSIZE',	480);


class Catalog_pictures extends \Elf\Libs\Uploaders {
	
	function __construct() {
		parent::__construct('catalog_pictures','catalog');
	}
	protected function post_init($dir) {
		$this->path = str_replace("%%albumdir%%/",$dir?$dir.'/':'__tmp/',CATALOG_IMGS_PATH);
		$this->icons = str_replace("%%albumdir%%/",$dir?$dir.'/':'__tmp/',CATALOG_ICONS_PATH);
		$this->fpath = str_replace("%%albumdir%%/",$dir?$dir.'/':'__tmp/',CATALOG_IMGS_FPATH);
		$this->ficons = str_replace("%%albumdir%%/",$dir?$dir.'/':'__tmp/',CATALOG_ICONS_FPATH);
		$this->image_xsize = $this->w = CATALOG_IMAGE_XSIZE;
		$this->image_ysize = $this->h = CATALOG_IMAGE_YSIZE;
		$this->icon_xsize = $this->icon_w = CATALOG_ICON_XSIZE;
		$this->icon_ysize = $this->icon_h = CATALOG_ICON_YSIZE;
	}
	function get_parent_alias($cid) {
		$ret = '';
		$cat = new Catalog;
		if (($rec = $cat->get_by_id((int)$cid))
			&& $rec['parent_id']
			&& ($rec = $cat->get_by_id($rec['parent_id']))) {
			$ret = $rec['alias'];
		}
		return $ret;
	}
	function clear_vals_by_cid($cid) {
		return $this->_delete()->_where("`catalog_id`=".(int)$cid)->_orderby("`catalog_id`")->_execute();
	}
	function get_by_cid($cid) {
			$palias = $this->get_parent_alias((int)$cid);
			return $this->_select("t1.*,concat('/','".$this->path."',".($palias?"'".$palias."/icons/',":"")."t1.`name`) as `src_icon`,
										concat('/','".$this->path."',".($palias?"'".$palias."/',":"")."t1.`name`) as `src_image`")
						->_where("`catalog_id`=".(int)$cid)
						->_orderby('`catalog_id`,`pos`')
						->_execute();
	}
	function remfile($remfile, $icon = true) {
		if ($res = $this->get("`name`='".$remfile."'","`name`")) {
			$palias = $this->get_parent_alias($res['catalog_id']);
			parent::remfile(($palias?$palias.'/':'').$remfile, $icon);
			$this->_delete()->_where("`name`='".$remfile."'")->_orderby("`name`")->_execute();
		}
	}
	function remfile_by_id($cid) {
		if ($res = $this->_select()
							->_subquery('catalog','t2')
								->_select("t2.`parent_id`")->_where("t2.`id`=t1.`catalog_id`")
								->_closesquery('pid')
							->_subquery('catalog','t3')
								->_select("t3.`alias`")->_where("t3.`id`=`pid`")
								->_closesquery('palias')
							->_where("`catalog_id` IN (".$cid.")")->_orderby("`catalog_id`")->_execute()) {
			foreach ($res as $v) {
				@unlink($this->fpath.($v['palias']?$v['palias'].'/':'').$v['name']);
				@unlink($this->fpath.($v['palias']?$v['palias'].'/':'').'icons/'.$v['name']);
			}
		}
	}
	function save_to_db($fname, $params) {
		if (is_array($params) && isset($params['cid'])) {
			$this->_insert(['name'=>$fname,
									'orient'=>$this->orient,
									'wh_coof'=>number_format($this->w/$this->h,8,'.',''),
									'catalog_id'=>(int)$params['cid']])->_execute();
		}
	}
	function _data($params) {
		if (!empty($params['cid'])) {
			$palias = $this->get_parent_alias((int)$params['cid']);
			return $this->_select("t1.*,concat('/','".$this->path."',".($palias?"'".$palias."/icons/',":"")."t1.`name`) as `src_icon`,
										concat('/','".$this->path."',".($palias?"'".$palias."/',":"")."t1.`name`) as `src_image`")
							->_where("`catalog_id`=".(int)$params['cid'])
							->_orderby('`catalog_id`,`pos`')->_execute();
		}
		else
			return null;
	}
}