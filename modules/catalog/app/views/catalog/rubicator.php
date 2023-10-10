<script>
var _hcs = null;
$(function() {
	$("#catalog-main-panel").css('min-height',$("#content-cont").height());
	$("a.childs").mouseover(function() {
		if (_hcs) {
			clearTimeout(_hcs);
			_hcs = null;
		}
		$("#catalog-sub-panel").css('top',$(this).offset().top-1).show();
		$("#catalog-sub-panel div.shora").height($(this).height()+parseInt($(this).css('padding-top'))+parseInt($(this).css('padding-bottom')));
		$("div.cat-sub-cont").hide();
		$("#"+$(this).attr('data-trgt-show')).show();
	}).mouseout(function() {
		_hide_cat_sub();
	});
	$("#catalog-sub-panel").mouseover(function() {
		clearTimeout(_hcs);
		_hcs = null;
	}).mouseout(function() {
		_hide_cat_sub();
	});
});
function _hide_cat_sub() {
	_hcs = setTimeout('$("#catalog-sub-panel").hide()',200);
}
</script>
<?php
	$cat = new \Elf\App\Models\Catalog;
	$res = $cat->_data();
?>
<div id="catalog-main-panel">
	<?php foreach ($res['rubicator'] as $v):
		$v['name'] = str_replace(Elf::lang('catalog')->item('replacement.for'),"",$v['inner_name']?$v['inner_name']:$v['name']);
		?>
		<?php if (!$v['level']):?>
		<a href="/<?=$v['alias']?>" data-trgt-show="cat-sub-<?=$v['id']?>" <?=$v['has_childs']?'class="childs"':''?>><?=$v['name']?> (<?=$v['items_cnt']?>)<?php if ($v['has_childs']):?><div></div><?php endif;?></a>
		<?php endif;?>
	<?php endforeach;?>
</div>
<div id="catalog-sub-panel">
	<div class="shora"></div>
	<?php foreach ($res['rubicator'] as $k=>$v):
			$v['name'] = $v['inner_name']?$v['inner_name']:$v['name'];
		if (!$v['level']) {
			if ($k)
				echo '</div>';
			echo '<div id="cat-sub-'.$v['id'].'" class="cat-sub-cont"><h3>'.$v['name'].'</h3>';
			continue;
		}
		elseif ($v['level'] == 1) {
			echo '<a href="/'.$v['alias'].'"><h4>'.$v['name'].' ('.$v['items_cnt'].')</h4></a>';
			continue;
		}
	?>
		<a href="/<?=$v['alias']?>" class="simple"><?=$v['name']?> (<?=$v['items_cnt']?>)</a>
	<?php endforeach;?>
	</div>
</div>