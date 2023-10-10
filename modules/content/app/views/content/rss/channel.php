<title><% lang:title %></title>
<link>{site_url}</link>
<description><% lang:description %></description>
<language><?=SYSTEM_LANGUAGE?></language>
<?php if (defined('YANDEX_COUNTER'))?>
<turbo:analytics type="Yandex" id="<?=YANDEX_COUNTER?>"></turbo:analytics>
<?php endif;?>
