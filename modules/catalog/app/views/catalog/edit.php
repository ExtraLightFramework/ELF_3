<?php
	$cat = new \Elf\App\Models\Catalog;
	if ((int)Elf::get_data('cid'))
		$rec = $cat->_get((int)Elf::$_data['cid']);
?>
<script>
var feature_add_plc = '';
var catalog_rec_type = '<?=!empty($rec)?$rec['type']:'rubric'?>';
$('#catalog-frm-edit input[name=cursel]').attr('value',catalog_rubric_sel);
function chk_req_fields(fid) {
	var _out = {};
	$("#"+fid+" input,#"+fid+" select,#"+fid+" textarea").each(function() {
		if ($(this).attr('required'))
			_out[$(this).attr('name')] = $(this).val();
	});
	$("#"+fid+" input[type=radio]:checked").each(function() {
		if ($(this).attr('required'))
			_out[$(this).attr('name')] = $(this).val();
	});
//	alert(JSON.stringify(_out));
	$.post("/catalog/chk_reg_fields",_out,function(data) {
		if (data == 'ok')
			$("#"+fid).submit();
		else
			alert(data);
	});
	return false;
}
function feature_add(obj, plc) {
	showDialog(obj);
	feature_add_plc = plc;
}
function feature_remove(cid, fid) {
	showWW();
	$('#catalog-feature-plc-'+fid).remove();
	if (!$('tr.catalog-feature-plc').length)
		$('#features-list-empty').show();
	$.post('/catalog/feature_remove',{cid:cid,fid:fid},function(data) {
		hideWW();
		showBaloon(data.alert);
	},'json');
}
function group_remove(cid, gid) {
	showWW();
	$('#catalog-feature-plc-'+gid+',#feature-add-plc-'+gid).remove();
	if (!$('tr.catalog-feature-plc').length)
		$('#features-list-empty').show();
	$.post('/catalog/group_remove',{cid:cid,gid:gid},function(data) {
		hideWW();
		showBaloon(data.alert);
	},'json');
}
function feature_share_to_childs(cid, fid) {
	showWW();
	$.post('/catalog/feature_share',{cid:cid,fid:fid},function(data) {
		hideWW();
		showBaloon(data.alert);
	},'json');
}
function sw_catalog_type_item(obj) {
	retype_uri();
	if (parseInt(obj.val())) {
		$('#catalog-type-item').removeProp('disabled');
	}
	else {
		$('#catalog-type-item').prop('disabled','disabled');
		$('#catalog-type-rubric').prop('checked','checked');
		sw_rubric_items_inputs(parseInt(obj.val()));
	}
}
function retype_uri() {
	let obj = $('select[name=parent_id]');
	$('#url-plc').html('<a href="{site_url}'+obj.find('option:checked').attr('data-paliases')+(obj.find('option:checked').attr('data-paliases')?'/':'')+$('#alias-plc').val()+'">{site_url}'+obj.find('option:checked').attr('data-paliases')+(obj.find('option:checked').attr('data-paliases')?'/':'')+$('#alias-plc').val()+'</a>');
}
function show_baloon_catype() {
	if ($('#catalog-type-item').prop('disabled')) {
		showBaloon('<% lang:catalog:cannotbeselected %>');
	}
}
function sw_rubric_items_inputs(sw) {
	if (sw) { // show item inputs
		$('.rubric-type-item-input').show();
		$('.rubric-type-rubric-input').hide();
	}
	else { // hide item inputs
		$('.rubric-type-item-input').hide();
		$('.rubric-type-rubric-input').show();
	}
}
$(function()  {
	$("i.group-items-show-hide").click(function() {
		$(this).toggleClass('fa-minus-square fa-plus-square');
		$(".catalog-feature-group-"+$(this).attr('data-gid')).toggle();
	});
	$("input.catalog-type-selector").click(function() {
		catalog_rec_type = $(this).val();
		if (catalog_rec_type == 'rubric') {
			sw_rubric_items_inputs(0);
			$(".feature-value").hide();
		}
		else {
			sw_rubric_items_inputs(1);
			$(".feature-value").show();
		}
	});
	let rubric_selector_sel = <?=!empty($rec)?$rec['parent_id']:(!empty(Elf::$_data['parent_id'])?Elf::$_data['parent_id']:0)?>;
	if (!rubric_selector_sel) {
		$('#catalog-type-item').prop('disabled','disabled');
	}
	if ($("input.catalog-type-selector:checked").val() == 'item')
		sw_rubric_items_inputs(1);
	else
		sw_rubric_items_inputs(0);
//	$(".catalog-feature-group-plc").each(function() {
//		$("#catalog-rec-features").append($(this)[0].outerHTML);
//		$(this).remove();
//	});
	$(".catalog-feature-plc").each(function() {
		$('#feature-add-plc-'+$(this).attr('data-gid')).append($(this)[0].outerHTML);
		$(this).remove();
	});
});

</script>
<?php if (!empty($rec) && ($rec['clone_id'])):?>
<div class="clone-alert"><?=Elf::lang('catalog')->item('rec.isclone',$rec['clone_id'])?></div>
<?php endif;?>
<form action="/catalog/edit" method="post" id="catalog-frm-edit">
	<input type="hidden" name="id" value="<?=!empty($rec)?($rec['clone_id']?$rec['clone_id']:$rec['id']):0?>" />
	<input type="hidden" name="cursel" value="0" />
	<table class="dialog">
		<tr>
			<td colspan="2" class="dialog-chapter-tlt"><% lang:catalog:struct.info %></td>
		</tr>
		<tr>
			<th><% lang:catalog:rubric.iscont %></th>
			<td><?=$cat->rubric_selector(!empty($rec)?$rec['parent_id']:(!empty(Elf::$_data['parent_id'])?Elf::$_data['parent_id']:0),!empty($rec)?$rec['id']:0,'required="required" onchange="sw_catalog_type_item($(this))"')?></td>
		</tr>
		<tr>
			<th><% lang:catalog:rec.type %></th>
			<td>
				<label><input class="nowide catalog-type-selector" required="required" type="radio" name="type" id="catalog-type-rubric" value="rubric" <?=empty($rec)||$rec['type']=='rubric'?'checked="checked"':''?> /> - <% lang:catalog:rt.rubric %></label>
				<label onclick="show_baloon_catype()"><input class="nowide catalog-type-selector" required="required" type="radio" name="type" id="catalog-type-item" value="item" <?=!empty($rec)&&$rec['type']=='item'?'checked="checked"':''?>/> - <% lang:catalog:rt.item %></label>
			</td>
		</tr>
		<tr class="rubric-type-item-input">
			<th><% lang:catalog:unit %></th>
			<td><?=$cat->units_selector(!empty($rec)?$rec['unit_id']:0)?></td>
		</tr>
		<tr>
			<td colspan="2" class="dialog-chapter-tlt"><% lang:catalog:rec.info %></td>
		</tr>
		<tr>
			<th><% lang:catalog:name %> <sup class="red">*</sup></th>
			<td id="catalog-alias-checker">
				<input type="text" name="name" value='<?=!empty($rec)?$rec['name']:''?>' required="required" onkeyup="catalog_alias_checker(this.value,<?=isset($rec['id'])?$rec['id']:0?>)" />
				<br /><strong title="<% lang:catalog:alias %>" class="catalog-alias"><?=isset($rec['alias'])?$rec['alias']:'-'?></strong>
				<input type="hidden" name="alias" id="alias-plc" value="<?=!empty($rec['alias'])?$rec['alias']:''?>" />
				<div class="mini-alert mini-alert-alias hide"><% lang:catalog:alias.notunique %></div>
				<br />URL: <span id="url-plc"><?=!empty($rec)?'<a href="'.Elf::site_url().$rec['uri'].'" target="_blank">'.Elf::site_url().$rec['uri'].'</a>':'-'?></span>
			</td>
		</tr>
		<tr>
			<th><% lang:catalog:innername %></th>
			<td><input type="text" name="inner_name" value='<?=!empty($rec)?$rec['inner_name']:''?>' /></td>
		</tr>
		<tr>
			<th><% lang:catalog:desc %></th>
			<td><textarea name="desc" rows="4"><?=!empty($rec)?$rec['desc']:''?></textarea></td>
		</tr>
		<tr class="rubric-type-item-input">
			<th><% lang:catalog:cost.price %></th>
			<td><input type="text" name="price" class="nowide" size="3" value="<?=!empty($rec)?$rec['price']:''?>" /></td>
		</tr>
		<tr class="rubric-type-item-input">
			<th><% lang:catalog:quantity %></th>
			<td><input type="text" name="quantity" class="nowide" size="3" value="<?=!empty($rec)?$rec['quantity']:'1'?>" /></td>
		</tr>
		<tr>
			<td colspan="2" class="dialog-chapter-tlt">
				<% lang:catalog:pictures %>
				<a href="javascript:;" onclick="upl_pictures._select_files()"><i title="<% lang:catalog:uploadimages %>" class="elf-uploader-unupload fas fa-plus-circle"></i></a>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="upl_pictures" class="elf-uploader-cont"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="dialog-chapter-tlt">
				<% lang:catalog:properties %>
				<?php
					Elf::session()->set('catalog_fids');
					if (!empty($rec['features'])) {
						//print_r($rec['features']);
						foreach ($rec['features'] as $f) {
							if ($f['type'] == 'feature')
							Elf::session()->set('catalog_fids',Elf::session()->get('catalog_fids').(Elf::session()->get('catalog_fids')?',':'').$f['feature_id']);
						}
					}
				?>
				<a href="javascript:;" data-params="dialog=catalog/feature_popup_menu;feature_plc=catalog-rec-features<?=!empty($rec)?';cid='.$rec['id']:''?>"
					onclick="showPopup($(this))" title="<% lang:catalog:addproperty %>"><i class="fas fa-plus-circle"></i></a>
			</td>
		</tr>
	</table>
	<table class="dialog" id="catalog-rec-features">
		<?php if (!empty($rec['features'])):?>
			<?php foreach ($rec['features'] as $f): //print_r($f);echo "<br /><br />";?>
				<?=Elf::load_template('catalog/feature_create',['cid'=>!empty($rec)?$rec['id']:0,'feat'=>json_encode($f),'feat_value'=>$f['value']])?>
			<?php endforeach;?>
		<?php endif;?>
			<td colspan="2" id="features-list-empty" <?=!empty($rec['features'])?'style="display:none;"':''?> class="cntr features-list-empty"><% lang:catalog:featuresnotfound %></td>
	</table>
	<table class="dialog">
		<tr>
			<td colspan="2" class="dialog-chapter-tlt"><% lang:catalog:seodata %></td>
		</tr>
		<tr>
			<th><% lang:catalog:desc %></th>
			<td><textarea name="description" rows="4"><?=!empty($rec)?$rec['description']:''?></textarea></td>
		</tr>
		<tr>
			<th><% lang:catalog:keywords %></th>
			<td><textarea name="keywords" rows="3"><?=!empty($rec)?$rec['keywords']:''?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" class="cntr">
				<input type="submit" class="cntrl sbmt" value="<% lang:save %>" onclick="return chk_req_fields('catalog-frm-edit')" /> <input type="button" class="cntrl cncl" value="<% lang:cancel %>" onclick="hideDialog(<% wid %>)" />
			</td>
		</tr>
	</table>
</form>
<script>
<?php
if (!empty($rec['pictures'])):
	$icons = array();
	foreach ($rec['pictures'] as $k=>$v) {
		$icons[] = $v['src_icon'];
	}
else:
	$icons = false;
endif;
?>
var upl_pictures = new ELF_Uploader("upl_pictures",'catalog_pictures','picture',<?=$icons?json_encode($icons):'false'?>,true,false,<?=json_encode(['cid'=>!empty($rec)?($rec['clone_id']?$rec['clone_id']:$rec['id']):0,'rem_func'=>'/catalog_pictures/remfile','upload_func'=>'/uploader/async_upload'])?>);
</script>