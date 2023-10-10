<?php if (!Elf::get_data('admin')):?>
		<ul itemscope itemtype="http://schema.org/BreadcrumbList" class="bread-crumbs-line"><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="{site_url}"><% lang:content:breadcrumbs.root %></a><i class="fas fa-chevron-right"></i></li>
<?php else:?>
		<ul itemscope itemtype="http://schema.org/BreadcrumbList" class="bread-crumbs-line" id="bread-crumbs-line-admin">
<?php endif;?>
<?php if (Elf::get_data('paliases')):
		$alias = '';
		foreach (Elf::get_data('paliases') as $k=>$v):
			$alias .= ($alias?'/':'').$v;?>
			<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
		<?php if (!Elf::get_data('admin')):?>
			<a itemprop="item" href="/<?=$alias?>/"><?=$k?></a><i class="fas fa-chevron-right"></i>
		<?php else:?>
			<a itemprop="item" href="/content/index/<?=$v?>/"><?=$k?></a><i class="fas fa-chevron-right"></i>
		<?php endif;?>
			</li>
<?php	endforeach;
	endif;?>
	<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><% title %></li></ul>
