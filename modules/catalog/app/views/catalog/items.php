<h2><?=Elf::lang('catalog')->item('rubric.items',Elf::$_data['title'])?></h2>
<?=Elf::$_data[1]?Elf::$_data[1]:''?>
<?php if (!empty(Elf::$_data[0])):?>
	<?php foreach (Elf::$_data[0] as $k=>$v):?>
		<div class="catalog-item" id="catalog-item-<?=$v['id']?>">
			<?php if ((int)Elf::session()->get('uid') && (Elf::session()->get('group')&GROUP_ADMIN)):?>
				<?php if ($v['type'] == 'rubric'):?>
				<div class="cat-amd-ctrl redtitem" title="<% lang:catalog:add.item.in.the.rubric %>" data-params="dialog=catalog/edit;cid=0;parent_id=<?=$v['id']?>;caption=<% lang:catalog:edt.item.dtitle %>" onclick="showDialog(this)"></div>
				<?php endif;?>
			<div class="cat-amd-ctrl redt" title="<% lang:catalog:edt.item %>" data-params="dialog=catalog/edit;cid=<?=$v['id']?>;caption=<% lang:catalog:edt.item.dtitle %>" onclick="showDialog(this)"></div>
			<div class="cat-amd-ctrl rdel" title="<% lang:catalog:rem.item %>" data-params="dialog=catalog/del_<?=$v['type']?>;cid=<?=$v['id']?>;caption=<% lang:catalog:rem.item.dtitle %>" onclick="showDialog(this)"></div>
			<?php if ((int)$v['clone_id']):?>
				<div class="is-clone" title="<?=Elf::lang('catalog')->item('rec.clone',$v['clone_id'])?>"><% lang:catalog:clone %> #<?=$v['clone_id']?></div>
			<?php endif;?>
			<?php endif;?>
			<div class="pics-cont" id="pics-cont<?=$k?>">
				<div class="galery-items">
					<i class="arr l-arr fas fa-chevron-circle-left" onclick="gal<?=$k?>.shift('left')"></i>
					<i class="arr r-arr fas fa-chevron-circle-right" onclick="gal<?=$k?>.shift('right')"></i>
				</div>
			</div>
			<div class="catalog-num">#<?=$v['id']?></div>
			<?php if ($v['type'] == 'rubric'):?>
			<div class="rubric-note"><% lang:catalog:rubric %></div>
			<?php endif;?>
			<div class="item-info">
				<h2>
					<a href="{site_url}<?=$v['uri']?>" title="<% lang:catalog:open.in.new.tab %>" target="_blank">
						<?=$v['type'] == 'rubric'?'<% lang:catalog:rubric %> «':''?><?=$v['name']?><?=$v['type'] == 'rubric'?'»':''?>
					</a>
				</h2>
				<div><?=$v['desc']?></div>
			</div>
		</div>
	<?php endforeach;?>
<script>
	<?php foreach (Elf::$_data[0] as $k=>$v):?>
	var gal<?=$k?> = new ELF_Galery(1,'pics-cont<?=$k?>','catalog_pictures',{cid:<?=$v['clone_id']?$v['clone_id']:$v['id']?>});
	gal<?=$k?>.start('icon');
	<?php endforeach;?>
</script>
<?php else:?>
<div class="alert"><% lang:datanotfound %></div>
<?php endif;?>