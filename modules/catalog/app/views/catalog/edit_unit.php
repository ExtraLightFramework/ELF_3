<?php
	$cat = new \Elf\App\Models\Catalog_units;
	if ((int)Elf::get_data('unid'))
		$rec = $cat->get_by_id((int)Elf::$_data['unid']);
?>
<form action="/catalog/edit_unit" method="post">
	<input type="hidden" name="id" value="<?=!empty($rec)?$rec['id']:0?>" />
	<table>
		<tr>
			<th width="25%"><% lang:catalog:name %></th>
			<td>
				<input type="text" name="name" value="<?=!empty($rec)?$rec['name']:''?>" />
				<div class="mini-note">
					<div title="<% lang:catalog:alias %>"><b><?=!empty($rec['alias'])?$rec['alias']:'-'?></b></div>
					<% lang:catalog:unit.name.help %>
				</div>
			</td>
		</tr>
		<tr>
			<th><% lang:catalog:short.name %></th>
			<td>
				<input type="text" name="short_name" value="<?=!empty($rec)?$rec['short_name']:''?>" />
				<div class="mini-note">
					<% lang:catalog:unit.short_name.help %>
				</div>
			</td>
		</tr>
		<tr>
			<th><% lang:catalog:type %></th>
			<td><?=$cat->create_select('type',!empty($rec)?$rec['type']:'','','catalog')?></td>
		</tr>
		<tr>
			<th><% lang:catalog:desc %></th>
			<td>
				<textarea name="desc" rows="2"><?=!empty($rec)?$rec['desc']:''?></textarea>
				<div class="mini-note">
					<% lang:catalog:unit.desc.help %>
				</div>
			</td>
		</tr>
			<tr>
			<th><% lang:catalog:values %> <span title="<% lang:catalog:values.help %>">[?]</span></th>
			<td>
				<textarea name="prevalues" rows="4"><?=!empty($rec)?$rec['prevalues']:''?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="cntr">
				<input type="submit" class="cntrl sbmt" value="<% lang:save %>" /> <input type="button" class="cntrl cncl" value="<% lang:cancel %>" onclick="hideDialog(<% wid %>)" />
			</td>
		</tr>
	</table>
</form>