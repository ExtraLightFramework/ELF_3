<?=Elf::load_template('catalog/menu')?>
<h2><% lang:catalog:unit.title %></h2>
<a href="javascript:;" data-params="dialog=catalog/edit_unit;unid=0;caption=<% lang:catalog:newunit.dtitle %>" title="<% lang:catalog:newunit.tlt %>" onclick="showDialog(this)"><% lang:catalog:newunit %></a>
<?php if (Elf::$_data['units']):?>
<table class="edit">
	<tr class="top">
		<td>#</td>
		<td><% lang:catalog:name %></td>
		<td><% lang:catalog:desc %></td>
		<td><% lang:catalog:values %></td>
	</tr>
	<?php foreach (Elf::$_data['units'] as $v):?>
	<tr class="data">
		<td>
			<?=$v['id']?><br />
			<a href="javascript:;" data-params="dialog=catalog/edit_unit;unid=<?=$v['id']?>;caption=<% lang:catalog:newunit.dtitle %>" title="<% lang:catalog:editunit.tlt %>" onclick="showDialog(this)"><% lang:edt %></a>
		</td>
		<td><strong><?=str_replace('units.','',$v['name'])?></strong>
			<?=$v['short_name']?' ('.$v['short_name'].')':''?>
			<div class="sml-fnt"><i><% lang:catalog:type %></i>: <?=Elf::lang()->item($v['type'])?></div></td>
		<td><?=nl2br($v['desc'])?></td>
		<td><div class="shrt-h" title="<% lang:catalog:swlist %>
<?=$v['prevalues']?>"><?=nl2br($v['prevalues'])?></div></td>
	</tr>
	<?php endforeach;?>
</table>
<?php else:?>
<div class="alert"><% lang:datanotfound %></div>
<?php endif;?>