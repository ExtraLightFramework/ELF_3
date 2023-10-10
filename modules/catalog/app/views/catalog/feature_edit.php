<?php
	$feats = new \Elf\App\Models\Catalog_features;
	$cat = new \Elf\App\Models\Catalog;
	if ((int)Elf::get_data('fid'))
		$rec = $feats->_get((int)Elf::get_data('fid'));
?>
<form action="/catalog/feature_edit" method="post" id="feature-edit-frm">
	<input type="hidden" name="id" value="<?=!empty($rec)?$rec['id']:0?>" />
	<table>
		<tr>
			<th><% lang:catalog:property %></th>
			<td><input type="text" name="name" required="required" id="feature-name" value="<?=!empty($rec)?$rec['name']:''?>" autocomplete="off" /></td>
		</tr>
		<tr>
			<th><% lang:catalog:desc %></th>
			<td><textarea name="desc" required="required" id="feature-desc" rows="3"><?=!empty($rec['desc'])?$rec['desc']:''?></textarea></td>
		</tr>
		<tr>
			<th><% lang:catalog:unit %></th>
			<td><?=$cat->units_selector(!empty($rec)?$rec['unit_id']:0)?></td>
		</tr>
		<tr>
			<th><% lang:catalog:group %></th>
			<td><?=$feats->groups_selector(!empty($rec)?$rec['group_id']:(Elf::get_data('gid')?Elf::get_data('gid'):0))?></td>
		</tr>
		<tr>
			<td colspan="2" class="cntr">
				<input type="submit" class="cntrl sbmt" value="<% lang:save %>" /> <input type="button" class="cntrl cncl" value="<% lang:cancel %>" onclick="hideDialog(<% wid %>)" />
			</td>
		</tr>
	</table>
</form>