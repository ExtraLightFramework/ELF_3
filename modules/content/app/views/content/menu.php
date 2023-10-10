<script>
$(function() {
	var _hd_sub_mnu = null;
	$("a.admin-submnu-lnk-content").mouseover(function() {
		$('div.admin-subsubmnu').hide();
		clearTimeout(_hd_sub_mnu);
		if ($("div").is("#admin-subsubmnu-"+$(this).attr('data-rid'))) {
			$("#admin-subsubmnu-"+$(this).attr('data-rid')).show();
		}
		else if (parseInt($(this).attr('data-childs-cnt'))) {
			showWW();
			var _obj = $(this);
			$.post('/content/get_rubric_submnu',{pid:$(this).attr('data-rid'),alias:'<% alias %>'},function(data) {
				hideWW();
				$('body').append(data);
				$('#admin-subsubmnu-'+_obj.attr('data-rid')).css({left:_obj.offset().left+'px',top:(_obj.offset().top+23)+'px'});
			});
		}
	}).mouseout(function() {
		var _obj = $(this);
		_hd_sub_mnu = setTimeout(function(){$("#admin-subsubmnu-"+_obj.attr('data-rid')).hide();},500);
	});
	$("body").on('mouseover','div.admin-subsubmnu',function() {
		clearTimeout(_hd_sub_mnu);
	}).on('mouseout','div.admin-subsubmnu',function() {
		var _obj = $(this);
		_hd_sub_mnu = setTimeout(function(){_obj.hide();},500);
	});
});
</script>
<?php
	$cont = new \Elf\App\Models\Content;
	$struct = $cont->get_structure_1_level();
?>
<div class="admin-sub-mnu" id="admin-sub-mnu">
	<a href="/content" class="admin-submnu-lnk<?=Elf::routing()->method_to()=='index'&&Elf::get_data('ctype')==''?' selected':''?>"><% lang:content:title %></a>
	<a href="/content/structure" class="admin-submnu-lnk<?=Elf::routing()->method()=='structure'?' selected':''?>"><% lang:content:structure %></a>
	<?php if (!empty($struct)):
			echo '&nbsp;|&nbsp;';
			$palias = !empty(Elf::$_data['root_parent'])?Elf::$_data['root_parent']['alias']:'';
	?>
		<?php foreach ($struct as $v):?>
		<a href="/content/index/<?=$v['alias']?>" class="admin-submnu-lnk admin-submnu-lnk-content<?=$v['alias']==$palias?' selected':''?>" data-rid="<?=$v['id']?>" data-childs-cnt="<?=$v['childs_cnt']?>"><?=$v['title']?> <span title="<% lang:content:subrubricscnt %>">(<?=$v['childs_cnt']?>)</span></a>
		<?php endforeach;?>
	<?php endif;?>
	<div class="admin-sub-mnu-lft-sec">
		<?php if (Elf::get_data('alias')):?>
			<a href="/content/refill_positions/<% alias %>" class="admin-sub-mnu-lft-sec" title="<% lang:content:admin.refillpos.tlt %>"><i class="fas fa-retweet"></i></a>
		<?php endif;?>
		<a href="/content/rss/<% alias %>" class="admin-sub-mnu-lft-sec" title="<% lang:content:admin.createamp.tlt %>"><i class="fas fa-upload"></i></a>
	</div>
</div>
