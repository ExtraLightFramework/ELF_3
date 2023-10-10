<?php
	$feats = new \Elf\App\Models\Catalog_features;
	if ((int)Elf::get_data('gid'))
		$rec = $feats->__get_group((int)Elf::get_data('gid'));
	else
		$rec = $feats->get_features_for_group();
?>
<form action="/catalog/feature_group_edit" method="post" id="feature-group-edit-frm">
	<input type="hidden" name="id" value="<?=!empty($rec['id'])?$rec['id']:0?>" />
	<table>
		<tr>
			<th><% lang:catalog:group %></th>
			<td><input type="text" name="name" required="required" id="feature-name" value="<?=!empty($rec['name'])?$rec['name']:''?>" autocomplete="off" /></td>
		</tr>
		<tr>
			<th><% lang:catalog:desc %></th>
			<td><textarea name="desc" required="required" id="feature-desc" rows="3"><?=!empty($rec['desc'])?$rec['desc']:''?></textarea></td>
		</tr>
		<?php if (!empty($rec['features'])):?>
		<tr>
			<td colspan="2">
				<div id="features-group-list" class="features-list">
				<?php foreach ($rec['features'] as $v):?>
					<div>
						<label>
							<input type="checkbox" class="nowide" name="feature[<?=$v['id']?>]" <?=in_array($v['id'],$rec['fids'])?'checked="checked"':''?> title="<% lang:catalog:add.rem.feature.in.group %>" />
							<span title="<?=$v['desc']?>"><?=$v['name']?><?=$v['short_name']?' ('.$v['short_name'].')':''?></span>
							<?=$v['group_id']?'<span class="for-group" title="'.Elf::lang('catalog')->item('ingroup',$v['group_name']).'">['.$v['group_name'].']</span>':''?>
						</label>
					</div>
				<?php endforeach;?>
				</div>
			</td>
		</tr>
		<?php endif;?>
		<tr>
			<td colspan="2" class="cntr">
				<input type="submit" class="cntrl sbmt" value="<% lang:save %>" /> <input type="button" class="cntrl cncl" value="<% lang:cancel %>" onclick="hideDialog(<% wid %>)" />
			</td>
		</tr>
	</table>
</form>