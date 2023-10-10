<?=Elf::load_template('catalog/menu')?>
<script>
var catalog_rubric_sel = <?=Elf::$_data['cursel']?>;
function _showCatalogItems(cid,offset) {
	showWW();
	$.post('/catalog/items',{cid:cid,offset:offset},function(data) {
		$("#catalog-items-cont").html(data);
		hideWW();
	});	
}
function showCatalogItems(params) {
	params = params.split("/");
	_showCatalogItems(params[0],params[1]);
}
function CatalogChPos(id,pid,level,direct) {
//	if (direct == 'up')
//		var _id = $("#rubrica-"+id).prev('.level-'+level+'-'+pid).attr('id');
//	else
//		var _id = $("#rubrica-"+id).next('.level-'+level+'-'+pid).attr('id');
	var _obj = $("#rubrica-17");//.next('div.rubrica-disp');
	for(var key in _obj) {
   console.log(_obj[key]);
}
	alert(_obj.attr('id'));
	if (typeof _id != 'undefined') {
		var _html = $("#rubrica-"+id).parent().html();
		$("#rubrica-"+id).parent().html($("#"+_id).html());
		$("#"+_id).html(_html);
	}
}
function rem_catalog_item(cid, wid) {
	showWW();
	$.post('/catalog/del',{cid:cid,subs:$("#catalog-frm-del input[name=subs]:checked").val()},function(data) {
		hideWW();
		if (data.ok) {
			$("#rubrica-"+cid+",#catalog-item-"+cid).remove();
		}
		else
			alert(data.error);
		hideDialog(wid);
	},'json');
}
$(function() {
	$('div.rdisp-selected').each(function() {
		_showCatalogItems($(this).attr('data-rid'),0);
		$('#rubrica-sub-'+$(this).attr('data-rid')).show();
		$(this).find('div.rdisp').toggleClass('rdisp-close rdisp-open');
	});
	$('div.rdisp-open').each(function() {
		$('#rubrica-sub-'+$(this).attr('data-rid')).show();
	});
	$('div.rdisp').click(function(data) {
		$(this).closest('div.rubrica-disp').toggleClass('close open');
		$(this).toggleClass('rdisp-close rdisp-open');
		if ($(this).hasClass('rdisp-open')) {
			$('#rubrica-sub-'+$(this).attr('data-rid')).show();
		}
		else {
			$('#rubrica-sub-'+$(this).attr('data-rid')).hide();
		}
	});
	$('div.rubrica-disp span.rubric-name').click(function(data) {
		$('div.rubrica-disp').removeClass('rdisp-selected');
		$(this).closest('div.rubrica-disp').addClass('rdisp-selected');
		_showCatalogItems($(this).attr('data-rid'),0);
		catalog_rubric_sel = $(this).attr('data-rid');
	});
});
</script>
<h2 class="catalog-admin-tlt">
	<% lang:catalog:catalog.title %>
	<a href="javascript:;" data-params="dialog=catalog/edit;cid=0;caption=<% lang:catalog:newitem.dtitle %>" title="<% lang:catalog:newitem.tlt %>" onclick="showDialog(this)"><i class="fas fa-plus-circle"></i></a>
</h2>

<?php
//	print_r(Elf::$_data['rubicator']);
	?>
<table id="catalog-index">
	<tr>
		<td id="catalog-rubicator">
			<?php if (!empty(Elf::$_data['rubicator'])):?>
				<?php
					$level = -1;
					foreach (Elf::$_data['rubicator'] as $k=>$v):
						if ($level != -1) {
							if ($level < $v['level']) {
								echo '<div id="pid-'.$v['parent_id'].'" class="parent-'.$v['parent_id'].'" data-pos="'.$v['pos'].'"><div class="rubrica-sub" id="rubrica-sub-'.$previd.'" style="padding-left:'.(10*$v['level']).'px;">';
							}
							elseif ($level > $v['level']) {
								echo str_repeat('</div> <!-- END SUB -->',$level-$v['level']+1);
								//echo '</div> <!-- END SUB -->';
							}
						}
					?>
					<div id="rubrica-<?=$v['id']?>" data-rid="<?=$v['id']?>" class="rubrica-disp <?=!empty($v['selected'])?'rdisp-selected':''?> <?=empty($v['opened'])?'close':'open'?>" title="<% lang:catalog:showrubricitems %>: <?=$v['name']?>">
						<div data-rid="<?=$v['id']?>" class="cat-amd-ctrl rdisp <?=empty($v['opened'])?'rdisp-close':'rdisp-open'?>" title="<% lang:catalog:swsubrubrics %>"></div>
						<div class="cat-amd-ctrl redtitem" title="<% lang:catalog:add.item.in.the.rubric %>" data-params="dialog=catalog/edit;cid=0;parent_id=<?=$v['id']?>;caption=<% lang:catalog:edt.rubric %>" onclick="showDialog(this)"></div>
						<div class="cat-amd-ctrl redt" title="<% lang:catalog:edt.item %>" data-params="dialog=catalog/edit;cid=<?=$v['id']?>;caption=<% lang:catalog:edt.rubric %>" onclick="showDialog(this)"></div>
						<div class="cat-amd-ctrl rdel" title="<% lang:catalog:rem.item %>" data-params="dialog=catalog/del_<?=$v['type']?>;cid=<?=$v['id']?>;caption=<% lang:catalog:rem.rubric %>" onclick="showDialog(this)"></div>
						<div class="cat-amd-ctrl r-pos rup" title="<% lang:catalog:chpositemup %>" onclick="location.href='/catalog/ch_pos/<?=$v['id']?>/up/'+catalog_rubric_sel"></div>
						<div class="cat-amd-ctrl r-pos rdn" title="<% lang:catalog:chpositemdn %>" onclick="location.href='/catalog/ch_pos/<?=$v['id']?>/dn/'+catalog_rubric_sel"></div>
						<span class="rubric-name" data-rid="<?=$v['id']?>">#<?=$v['id']?> <?=wordwrap($v['name'],40,'<br />',true)?></span> (<span title="<% lang:catalog:subrubric.cnt %>"><?=$v['has_childs']?></span>/<span title="<% lang:catalog:items.cnt %>"><?=$v['items_cnt']?></span>)
					</div>
					
				<?php
					if (!$v['has_childs']) {
						echo '<div class="rubrica-sub rubrica-sub-nodata" id="rubrica-sub-'.$v['id'].'" style="padding-left:'.(10*$v['level']).'px;"><% lang:datanotfound %></div>';
					}
					$previd = $v['id'];
					$level = $v['level'];
					endforeach;?>
					<?=str_repeat('</div> <!-- END SUB -->',$level)?>
			<?php else:?>
			<div class="alert"><% lang:catalog:rubrics.notfound %></div>
			<?php endif;?>
		</td>
		<td class="data" id="catalog-items-cont">
			<h3><% lang:catalog:rubric.left.select %></h3>
		</td>
	</tr>
</table>
