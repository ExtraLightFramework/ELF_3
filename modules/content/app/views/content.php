<!DOCTYPE html>
<!-- content -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?=SYSTEM_LANGUAGE?>">
<head itemscope itemtype="http://schema.org/WPHeader">
<?php
	if (Elf::session()->get('title')) {
		Elf::$_data['title'] = Elf::session()->get('title');
		Elf::session()->set('title');
	}
	if (Elf::session()->get('description')) {
		Elf::$_data['description'] = Elf::session()->get('description');
		Elf::session()->set('description');
	}
	if (Elf::session()->get('canonical')) {
		Elf::$_data['canonical'] = Elf::session()->get('canonical');
		Elf::session()->set('canonical');
	}
?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title itemprop="headline"><?=Elf::get_data('title')?Elf::$_data['title']:Elf::lang()->item('title')?></title>
	<meta itemprop="description" name="description" content="<?=Elf::get_data('description')?Elf::$_data['description']:Elf::lang()->item('description')?>" />
	<meta itemprop="keywords" name="keywords" content="<?=Elf::get_data('keywords')?Elf::$_data['keywords']:Elf::lang()->item('keywords')?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php if (defined('YANDEX_VERIFICATION') && YANDEX_VERIFICATION):?>
	<meta name="yandex-verification" content="<?=YANDEX_VERIFICATION?>" />
	<?php endif;?>
	<?php if (defined('GOOGLE_VERIFICATION') && GOOGLE_VERIFICATION):?>
	<meta name="google-site-verification" content="<?=GOOGLE_VERIFICATION?>" />
	<?php endif;?>

	<?php if (Elf::routing()->method_to() == 'page'):?>
	<!-- OG Section -->
	<meta property="og:title" content="<% title %>" />
	<meta property="og:description" content="<?=strip_tags(html_entity_decode(Elf::$_data['description']))?>" />
	<meta property="og:image" content="{site_url}<?=Elf::$_data['picture']?substr(Elf::$_data['picture_image'],1):"img/content/no-image.jpg"?>" />
	<meta property="og:site_name" content="<% lang:og:sitename %>" />
	<meta property="og:locale" content="ru_RU" />
	<!-- End OG -->
	<!-- AMP meta -->
	<link rel="amphtml" href="{site_url}amp<?=!empty(ELF::$_data['paliases'])?'/'.implode('/',ELF::$_data['paliases']):''?>/<% alias %>.html" />
	<!-- AMP End -->
	<?php endif;?>
	<link href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" rel="stylesheet" />
	<link href="/css/elf.css" rel="stylesheet" />
	<!-- module css files -->

	<link href="/app/css/content.css" rel="stylesheet" />
	<link rel="shortcut icon" type="image/png" href="/favicon.png" />

	<?php if (Elf::get_data('canonical')):?>
	<link rel="canonical" href="{site_url}<% canonical %>" />
	<?php elseif (Elf::get_data('pagination.seo')):?>
	<% pagination.seo %>
	<?php else:?>
	<link rel="canonical" href="{site_url}<?=Elf::routing()->controller()?>/<?=Elf::routing()->method()?>/" />
	<?php endif;?>

	<script src="//code.jquery.com/jquery-latest.min.js"></script>
	<script src="/js/main.js"></script>
	<script src="/js/elf_pagination.js"></script>
	<script src="/app/js/content.js"></script>
	<!-- module js files -->

	<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
	<script src="//yastatic.net/share2/share.js"></script>

	<?php if (defined('YANDEX_COUNTER') && YANDEX_COUNTER):?>	
	<!-- Yandex.Metrika counter -->
	<script>
	   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
	   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
	   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

	   ym(<?=YANDEX_COUNTER?>, "init", {
			clickmap:true,
			trackLinks:true,
			accurateTrackBounce:true
	   });
	</script>
	<!-- /Yandex.Metrika counter -->
	<?php endif;?>

	<?php if (defined('GOOGLE_TAG_MANAGER') && GOOGLE_TAG_MANAGER):?>	
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','<?=GOOGLE_TAG_MANAGER?>');</script>
	<!-- End Google Tag Manager -->
	<?php endif;?>

</head>
<body class="content">
	<div id="modal"></div>
	<div id="wait-window"></div>
	<div class="main-content">
		<?=Elf::load_template('main/menu')?>
		<% cookie.agreement %>
		<% content %>
	</div>
<?php if (!empty(Elf::$_data['preloadialog'])) {
		echo Elf::$_data['preloadialog'];
		unset(Elf::$_data['preloadialog']);
		Elf::session()->set('flashdata');
	}
?>
</body>
</html>
