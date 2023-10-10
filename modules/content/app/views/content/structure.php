<h2 class="adm-tlt"><% lang:content:structure.title %></h2>
<?=Elf::load_template('content/menu')?>
<a href="javascript:;" data-params="dialog=content/rubric_edit;rid=0;caption=<% lang:content:rubric.edit.dtitle %>" title="<% lang:content:rubric.edit.tlt %>" onclick="showDialog(this)"><% lang:content:rubric.new %></a>
<?php if (Elf::$_data['structure']):?>
		<div id="structure-0" class="content-structure">	
	<?php
		$plevel = 0;
		foreach (Elf::$_data['structure'] as $v):
			//if ($v['level']) {
				if ($v['level'] > $plevel) {
					echo '<div data-level="'.$v['level'].'" id="structure-'.$v['parent_id'].'" class="content-structure" style="margin-left:'.($v['level']*10).'px;">';
				}
				elseif ($v['level'] < $plevel) {
					echo str_repeat('</div>',$plevel-$v['level']);
				}
			//}
	?>
		<div class="structure-item">
			<?php if ($v['childs_cnt']):?>
			<i class="far fa-minus-square sw-item sw-item-open" onclick="$(this).toggleClass('fa-minus-square fa-plus-square');if($(this).hasClass('fa-minus-square')){$('#structure-<?=$v['id']?>').show();}else{$('#structure-<?=$v['id']?>').hide();}"></i>
			<?php else:?>
			&nbsp;&nbsp;&nbsp;
			<?php endif;?>
			<a href="/content/index/<?=$v['alias']?>" class="content-struct-rubric-lnk" title="<% lang:content:gotorubric %>"><?=$v['title']?></a>
			<i class="far fa-edit" data-params="dialog=content/rubric_edit;rid=<?=$v['id']?>;caption=<% lang:content:rubric.edit.dtitle %>" title="<% lang:content:rubric.edit.tlt %>" onclick="showDialog(this)"></i>
			<i class="far fa-trash-alt" title="<% lang:content:rubric.del.tlt %>" onclick="if(confirm('<% lang:content:confirm.del.rubric %>')) location.href='/content/rubric_del/<?=$v['id']?>'"></i>
			<a href="/<?=$v['alias']?>" target="_blank" title="<% lang:content:openinnewtab %>"><i class="fas fa-external-link-alt"></i></a>
		</div>
	<?php
//		echo $v['level'].' - '.$plevel;
		$plevel = $v['level'];
		endforeach;
		echo str_repeat('</div>',$plevel);
		?>
	</div>
<?php else:?>
<div class="alert"><% lang:datanotfound %></div>
<?php endif;?>
