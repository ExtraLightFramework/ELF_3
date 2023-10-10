<div class="linemenu">
	<a href="/" class="linemeny-lnk"><% lang:catalog:linemenumain %></a>
	<?php if (Elf::get_data('paliases')):
			$uri = '';
			foreach (Elf::get_data('paliases') as $k=>$v):
				$uri .= $v.'/';?>
			<i class="fas fa-chevron-right"></i>
			<a href="{site_url}<?=$uri?>" class="linemeny-lnk"><?=$k?></a>
		<?php endforeach;?>
	<?php endif;?>
</div>
