<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define ('ROOTPATH', str_replace("\\","/",realpath(dirname(__FILE__)).'/'));
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>ELF - installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta content="en" name="english" />
	<link href="/install/styles.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" type="image/png" href="/install/favicon.png" />
	<script src="/js/jquery.js"></script>
	<script src="/install/scripts.js"></script>
</head>
<body>
	<div id="frm-cont">                               
	<form action="javascript:_step(1)" method="post" id="elf-install-1">
		<h1>Extra Light Framework installer (Step 1)</h1>
		<table class="features">
			<tr>
				<td>
					<h2>Site Common Settings</h2>
					Site protocol:<br />
					<select name="protocol" required="required">
						<option value="">select the protocol</option>
						<option value="http">HTTP://</option>
						<option value="https">HTTPS://</option>
					</select><br /><br />
					Directory alias (is can empty):<br />
					<input type="text" name="diralias" /><br /><br />
					Site domain:<br />
					<input type="text" name="domain" required="required" /><br /><br />
					Email sender:<br />
					<input type="text" name="email_sender" required="required" /><br /><br />
					Service emails (comma ','):<br />
					<input type="text" name="service_emails" required="required" /><br /><br />
				</td>
				<td>
					<h2>DB Settings</h2>
					DB Host:<br />
					<input type="text" name="db_host" required="required" value="localhost" /><br /><br />
					DB Name:<br />
					<input type="text" name="db_name" required="required" /><br /><br />
					DB User:<br />
					<input type="text" name="db_user" required="required" /><br /><br />
					DB Password:<br />
					<input type="password" name="db_pass" required="required" /><br /><br />
					DB Prefix (recommended):<br />
					<input type="text" name="db_prefix" />
				</td>
			</tr>
		</table>
		<input type="submit" value="Next >>" />
	</form>
	<form action="javascript:_step(2)" method="post" id="elf-install-2">
		<h1>Extra Light Framework installer (Step 2)</h1>
		<table class="features">
			<tr>
				<td>
				<h2>Administrator Account</h2>
				Administrator Login:<br />
				<input type="text" name="admin_login" value="admin" /><br /><br />
				Administrator Password:<br />
				<input type="password" name="admin_passwd" /><br /><br />
				</td>
				<td>
				<h2>Common System Features</h2>
				Time Zone:<br />
				<select name="time_zone">
					<option value="Europe/London">Europe/London (+00:00)</option>
					<option value="Europe/Berlin">Europe/Berlin (+01:00)</option>
					<option value="Europe/Kaliningrad">Europe/Kaliningrad (+02:00)</option>
					<option value="Europe/Moscow" selected="selected">Europe/Moscow (+03:00)</option>
					<option value="Europe/Samara">Europe/Samara (+04:00)</option>
				</select><br /><br />
				Full System Language:<br />
				<select name="full_system_language">
					<option value="russian" selected="selected">Russian</option>
					<option value="english">English</option>
				</select><br /><br />
				System Language (short):<br />
				<select name="system_language">
					<option value="ru" selected="selected">RU</option>
					<option value="en">EN</option>
				</select><br /><br />
				<input type="checkbox" name="auto_login_enabled" checked="checked" /> System Auto Login<br /><br />
				<input type="checkbox" name="cookie_agreement_enabled" checked="checked" /> Cookie Use Agreement<br /><br />
				<input type="checkbox" name="debug_mode" /> Debug Mode<br /><br />
				Max Links in Paginator:<br />
				<input type="text" name="pagi_max_links" value="5" required="required" /><br /><br />
				Max Upload Image Size (in bytes):<br />
				<input type="text" name="max_image_size" value="5000000" required="required" /><br /><br />
				<input type="checkbox" name="real_send_mail" checked="checked" /> Real Send Mail<br /><br />
				</td>
			</tr>
		</table>
		<input type="submit" value="Next >>" />
	</form>
	<form action="javascript:_step(3)" method="post" id="elf-install-3">
		<h1>Extra Light Framework installer (Step 3)</h1>
		<h2>Extra Modules</h2>
<?php
	if (($xml = simplexml_load_file(__DIR__.'/modules.xml')) !== false):
?>
		<?php foreach ($xml as $module):?>
		<div class="module">
			<label><input type="checkbox" id="module-<?=(string)$module->name?>" class="chk-module" data-name="<?=(string)$module->name?>" name="module_<?=(string)$module->name?>" title="install the module" /> - <?=(string)$module->name?></label>
			<?php if (!empty($module->dependencies)):?>
				<?php foreach ($module->dependencies as $dep):
						if ((string)$dep->depend):?>
				<input type="hidden" class="dep-<?=(string)$module->name?>" value="<?=(string)$dep->depend?>" />
				<?php	endif; 
					endforeach;?>
			<?php endif;?>
		</div>
		<?php endforeach;?>
<?php else:?>
	<h3 class="alert">Modules not found</h3>
<?php endif;?>
		<input type="submit" value="Next >>" />
	</form>                                       
	<form action="javascript:void(0);" method="get" id="elf-install-4">
		<h1>Congratulation ELF has been installed!</h1>
		<h3>Now you can remove the "_install" directory from the site root path.<br />
		Do not forget! Set correct permissions rights and owner to all directories and files. Use `chown -R &gt;owner_name&lt;:&gt;owner_group&lt; <?=__DIR__?>/*`<br /><br />
		Thank you for using ELF!
		</h3>
		<input type="submit" value="Go to Main page" onclick="window.location.reload()" />
	</form>
	</div>
</body>
</html>