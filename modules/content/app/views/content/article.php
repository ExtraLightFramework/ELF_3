<% breadcrumbsline %>
<section class="news" itemscope itemtype="https://schema.org/Article" rel="nofollow">
	<meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="{site_url}<?=!empty(ELF::$_data['paliases'])?implode('/',ELF::$_data['paliases']).'/':''?><% alias %>" content="" />
	<h1 itemprop="headline" class="article-tlt"><?=Elf::$_data['title']?></h1>
	<article itemprop="articleBody">
		<?php if(!empty(Elf::$_data['picture'])):?>
			<div class="img img-<?=Elf::$_data['picture_ornt']?>">
				<img src="<?=Elf::$_data['picture_image']?>" alt="<?=Elf::$_data['picture_alt']?Elf::$_data['picture_alt']:Elf::$_data['title']?>" />
			</div>
		<?php else:?>
			<div class="img-invisible">
				<img src="/img/design/no-image.png" alt="<?=Elf::$_data['picture_alt']?Elf::$_data['picture_alt']:Elf::$_data['title']?>" />
			</div>
		<?php endif;?>
	
		<div class="content-first-p">
		<?=html_entity_decode(Elf::$_data['first_p'])?>
		</div>
		<?=html_entity_decode(Elf::$_data['text'])?>
	</article>
	<span itemprop="author" itemscope itemtype="https://schema.org/Person" rel="nofollow">
		<meta itemprop="name" content="<% lang:og:author %>" />
	</span>
	<meta itemprop="datePublished" content="<?=date('c',Elf::$_data['tm'])?>" />
	<meta itemprop="dateModified" content="<?=date('c',Elf::$_data['tm_edit'])?>" />
	<meta itemprop="description" content="<?=strip_tags(html_entity_decode(Elf::$_data['description']))?>" />
	<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject" rel="nofollow">
		<span itemprop="url" content="<?=Elf::$_data['picture']?Elf::$_data['picture_image']:'/img/design/no-image.png'?>"></span> 
		<span itemprop="image" content="<?=Elf::$_data['picture']?Elf::$_data['picture_image']:'/img/design/no-image.png'?>"></span>
		<meta itemprop="width" content="<?=Elf::$_data['picture']?Elf::$_data['picture_w']:'640'?>" />
		<meta itemprop="height" content="<?=Elf::$_data['picture']?Elf::$_data['picture_h']:'480'?>" />
	</div>
	
	<div itemprop="publisher" rel="nofollow" itemscope itemtype="https://schema.org/Organization">
		<div itemprop="logo" rel="nofollow" itemscope itemtype="https://schema.org/ImageObject">
			<img itemprop="url image" src="/img/design/no-image.png" style="display:none;" alt="<% lang:og:imagelogo.tlt %>" />
			<meta itemprop="width" content="211" />
			<meta itemprop="height" content="198" />
		</div>
		<meta itemprop="name" content="<% lang:og:publisher.name %>" />
		<meta itemprop="telephone" content="<% lang:og:publisher.telephone %>" />
		<meta itemprop="address" content="<% lang:og:publisher.address %>" />
		<meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="{site_url}<?=!empty(ELF::$_data['paliases'])?implode('/',ELF::$_data['paliases']).'/':''?><% alias %>" content="" />
	</div>
</section>
