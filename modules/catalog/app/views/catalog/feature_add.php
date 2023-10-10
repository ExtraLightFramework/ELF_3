<script>
// var feature_add_plc - init in views/catalog/edit.php
function feature_create_and_load(cid, fid, gid) {
	showWW();
	$.post('/elf/loadtemplate',{template:'catalog/feature_create',cid:cid,fid:fid},function(data) {
		$.post('/catalog/feature_add',{cid:cid,fid:fid},function(dt) {
			if (dt)
				alert(dt);
		});
		hideWW();
		$('#features-list-empty').hide();
		$('#feature-item-'+fid).remove();
		if (!$('div.feature-item').length)
			hideDialog(<% wid %>);
		if ($('#feature-add-plc-'+gid)[0]) {
			$('#feature-add-plc-'+gid).append(data);
		}
		else {
			showWW();
			$.post('/catalog/group_info',{gid:gid,cid:cid},function(gdata) {
				hideWW();
				$('#'+feature_add_plc).append(gdata);
				$('#feature-add-plc-'+gid).append(data);
				if (catalog_rec_type == 'rubric') {
					$(document).find(".feature-value").hide();
				}
				else {
					$(document).find(".rubric-type-rubric-input").hide();
				}
			});
		}
		if (catalog_rec_type == 'rubric') {
			$(document).find(".feature-value").hide();
		}
		else {
			$(document).find(".rubric-type-rubric-input").hide();
		}
		showBaloon('<% lang:catalog:featureaddinlist %>');
	});
}
function group_create_and_load(cid, gid) {
	showWW();
	$.post('/catalog/add_group_with_features',{cid:cid,gid:gid},function(data) {
		hideWW();
		$('#features-list-empty').hide();
		$(".feature-group-"+gid).remove();
		if ($('#feature-add-plc-'+gid)[0]) {
			$('#feature-add-plc-'+gid).append(data);
		}
		else {
			showWW();
			$.post('/catalog/group_info',{gid:gid,cid:cid},function(gdata) {
				hideWW();
				$('#'+feature_add_plc).append(gdata);
				$('#feature-add-plc-'+gid).append(data);
				if (catalog_rec_type == 'rubric') {
					$(document).find(".feature-value").hide();
				}
				else {
					$(document).find(".rubric-type-rubric-input").hide();
				}
			});
		}
		if (catalog_rec_type == 'rubric') {
			$(document).find(".feature-value").hide();
		}
		else {
			$(document).find(".rubric-type-rubric-input").hide();
		}
		showBaloon('<% lang:catalog:featureaddinlist %>');
	});
}
</script>
<?php
	$feat = new \Elf\App\Models\Catalog_features;
?>

<?php if ($feats = $feat->_data(0,Elf::session()->get('catalog_fids'))):?>
<div id="features-add-list" class="features-list">
	<?php foreach ($feats as $k=>$v):?>
	<?php if ($v['type'] == 'feature'):?>
	<div id="feature-item-<?=$v['id']?>" class="feature-item feature-group-<?=$v['group_id']?>" title="<% lang:catalog:add.feature.tlt %>" onclick="feature_create_and_load(<?=(int)Elf::get_data('cid')?>,<?=$v['id']?>,<?=$v['group_id']?>)">
		<b><?=$v['name']?><?=$v['short_name']?' ('.$v['short_name'].')':''?></b> <?=Elf::show_words($v['desc'],20)?>
	</div>
	<?php elseif ($v['type'] == 'group'):?>
		<?php if ($k):?>
			</div>
		<?php endif;?>
	<div id="feature-item-<?=$v['id']?>" class="feature-group feature-group-<?=$v['id']?>" title="<% lang:catalog:add.group.tlt %>" onclick="group_create_and_load(<?=(int)Elf::get_data('cid')?>,<?=$v['id']?>)">
		<b><% lang:catalog:group %> "<?=$v['name']?>"</b><br /><?=$v['desc']?>
	</div>
	<div class="feature-group-cont">
	<?php endif;?>
	<?php endforeach;?>
	</div>
</div>
<?php else:?>
<div class="alert"><% lang:catalog:feature.list.empty %></div>
<?php endif;?>