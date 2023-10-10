<?=Elf::load_template('catalog/menu')?>
<h2><% lang:catalog:features.title %></h2>

<a href="javascript:;" data-params="dialog=catalog/feature_edit;fid=0;modal=yes;caption=<% lang:catalog:add.feat.dtitle %>" onclick="showDialog(this)" title="<% lang:catalog:add.feat.inlist %>"><% lang:catalog:add.feat %></a> |
<a href="javascript:;" data-params="dialog=catalog/feature_group_edit;gid=0;modal=yes;caption=<% lang:catalog:add.group.dtitle %>" onclick="showDialog(this)" title="<% lang:catalog:add.group.inlist %>"><% lang:catalog:add.group %></a>

<?php if (Elf::get_data('feats')):?>
<script>
$(function(){
	$("i.features-show-hide").click(function() {
		$(this).toggleClass('fa-minus-square fa-plus-square');
		if ($(this).hasClass('fa-minus-square')) {
			$('#feature-group-'+$(this).attr('data-gid')).show();
		}
		else {
			$('#feature-group-'+$(this).attr('data-gid')).hide();
		}
	});
});
</script>
<table class="edit" id="catalog-features-list">
	<?php
		$gid = -1;
		$gid0_open = false;
		foreach (Elf::$_data['feats'] as $v):
			if (($v['type'] == 'group') && ($v['id'] != $gid)):
				if ($gid != -1)
					echo '</tbody>';
				echo '<tr class="data group">
								<td colspan="2" title="'.$v['desc'].'">
									<i class="features-show-hide far fa-minus-square" data-gid="'.$v['id'].'"></i>
									'.$v['name'].'
									'.($v['id']?'<a href="javascript:;" data-params="dialog=catalog/feature_group_edit;gid='.$v['id'].';modal=yes;caption=<% lang:catalog:edt.group.dtitle %>" onclick="showDialog(this)" title="<% lang:catalog:edt.group %>"><i class="fas fa-edit"></i></a>
												<a href="javascript:;" data-params="dialog=catalog/feature_edit;fid=0;gid='.$v['id'].';modal=yes;caption=<% lang:catalog:edt.feat.dtitle %>" onclick="showDialog(this)" title="<% lang:catalog:edt.feat.in.group %>"><i class="fas fa-file-download"></i></a>':'').
								'</td>
							</tr>
							<tbody id="feature-group-'.$v['id'].'">';
				$gid = $v['id'];
			elseif ($v['type'] == 'feature'):
	?>
	<tr class="data <?=$v['type']?>">
		<td width="30">
			<?=$v['id']?><br />
			<a href="javascript:;" data-params="dialog=catalog/feature_edit;fid=<?=$v['id']?>;modal=yes;caption=<% lang:catalog:edt.feat.dtitle %>" onclick="showDialog(this)" title="<% lang:catalog:edt.feat %>"><% lang:edt %></a>
		</td>
		<td><h3 title="<% lang:catalog:name %>"><?=$v['name']?></h3>
			<i><% lang:catalog:type %></i>: <?=Elf::lang('catalog')->item($v['unit_type'])?>
			<?php if ($v['prevalues']):?>
			<b title="<% lang:catalog:prevalues %>">[<?=str_replace("\r\n",", ",$v['prevalues'])?>]</b>
			<?php endif;?>
			<br /><br />
			<i><% lang:catalog:desc %></i>: <?=Elf::show_words($v['desc'],30)?> ...
		</td>
	</tr>
		<?php endif;?>
	<?php endforeach;?>
	</tbody>
</table>
<?php else:?>
<div class="alert"><% lang:catalog:features.empty %></div>
<?php endif;?>