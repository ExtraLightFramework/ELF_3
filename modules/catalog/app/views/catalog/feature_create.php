<?php 
	$feats = new \Elf\App\Models\Catalog_features;
	if (((int)Elf::get_data('fid')
			&& ($feat = $feats->_get((int)Elf::get_data('fid'))))
		|| (Elf::get_data('feat') && ($feat = (array)json_decode(Elf::get_data('feat'))))):?>
	<?php if ($feat['type'] == 'feature'):?>
	<tr id="catalog-feature-plc-<?=$feat['id']?>" data-fid="<?=$feat['id']?>" data-gid="<?=$feat['group_id']?>" class="catalog-feature-plc catalog-feature-group-<?=$feat['group_id']?>">
		<th><b><?=$feat['name']?></b><br /><?=Elf::show_words($feat['desc'], 10)?></th>
		<td><div class="feature-blck" id="feature-blck-<?=$feat['id']?>">
			<?php switch ($feat['unit_type']):
				case 'simple':?>
				<span class="rubric-type-rubric-input"><% lang:catalog:<?=$feat['unit_type']?> %> <b title="<% lang:catalog:prevalues %>">[<?=$feat['prevalues']?$feat['prevalues']:'-'?>]</b></span>
				<input type="text" class="feature-value rubric-type-item-input" name="feature[<?=$feat['id']?>]" value="<?=isset(Elf::$_data['feat_value'])?Elf::$_data['feat_value']:$feat['prevalues']?>" /> <span title="<?=$feat['desc']?>"><?=$feat['short_name']?></span>
			<?php break;
				case 'select':?>
				<span class="rubric-type-rubric-input"><% lang:catalog:<?=$feat['unit_type']?> %> <b title="<% lang:catalog:prevalues %>">[<?=$feat['prevalues']?str_replace("\r\n",", ",$feat['prevalues']):''?>]</b></span>
				<select class="feature-value rubric-type-item-input" name="feature[<?=$feat['id']?>]">
					<?php foreach (explode("\n",$feat['prevalues']) as $v):
							$v = trim($v);
					?>
					<option value="<?=$v?>" <?=isset(Elf::$_data['feat_value'])&&(Elf::$_data['feat_value']==$v)?'selected="selected"':''?>><?=$v?></option>
					<?php endforeach;?>
				</select>
			<?php break;
				case 'radio':?>
					<span class="rubric-type-rubric-input"><% lang:catalog:<?=$feat['unit_type']?> %> <b title="<% lang:catalog:prevalues %>">[<?=$feat['prevalues']?str_replace("\r\n",", ",$feat['prevalues']):''?>]</b></span>
					<div class="feature-value rubric-type-item-input">
					<?php foreach (explode("\n",$feat['prevalues']) as $v):?>
					<input type="radio" class="feature-value nowide" name="feature[<?=$feat['id']?>]" value="<?=$v?>" <?=isset(Elf::$_data['feat_value'])&&(Elf::$_data['feat_value']==$v)?'checked="checked"':''?>> - <?=$v?>&nbsp;
					<?php endforeach;?>
					</div>
			<?php break;
				case 'checkbox':?>
				<span class="rubric-type-rubric-input"><% lang:catalog:<?=$feat['unit_type']?> %></span>
				<input type="checkbox" class="feature-value nowide rubric-type-item-input" name="unfeat[<?=$feat['id']?>]" onclick="$(this).prop('checked')?$('#unfeat-val-<?=$feat['id']?>').attr('value',1):$('#unfeat-val-<?=$feat['id']?>').attr('value',0)" <?=!empty(Elf::$_data['feat_value'])?'checked="checked"':''?> />
				<input type="hidden" name="feature[<?=$feat['id']?>]" id="unfeat-val-<?=$feat['id']?>" value="<?=!empty(Elf::$_data['feat_value'])?1:0?>" />
			<?php break; 
				endswitch;?>
			<div class="feature-create-cntrl">
				<?php if (Elf::get_data('cid')):?>
				<i class="fas fa-share-alt rubric-type-rubric-input" onclick="feature_share_to_childs(<?=Elf::get_data('cid')?>,<?=$feat['id']?>)" title="<% lang:catalog:feature.share %>"></i>
				<?php endif;?>
				<i class="far fa-times-circle" onclick="feature_remove(<?=(int)Elf::get_data('cid')?>,<?=$feat['id']?>)" title="<% lang:catalog:feature.remove %>"></i>
			</div>
		</div></td>
	</tr>
	<?php elseif ($feat['type'] == 'group'):?>
	<tbody id="feature-add-plc-<?=$feat['id']?>">
	<tr id="catalog-feature-plc-<?=$feat['id']?>" class="catalog-feature-group-plc">
		<th colspan="2">
			<i class="group-items-show-hide far fa-minus-square" data-gid="<?=$feat['id']?>"></i>
			<b><?=$feat['name']?></b><br /><?=$feat['desc']?>
			<?php //if ($feat['id']):?>
			<div class="feature-create-cntrl feature-group-create-cntrl">
				<i class="far fa-times-circle" onclick="group_remove(<?=(int)Elf::get_data('cid')?>,<?=$feat['id']?>)" title="<% lang:catalog:group.remove %>"></i>
			</div>
			<?php //endif;?>
		</th>
	</tr>
	</tbody>
	<?php endif;?>
<?php endif;?>