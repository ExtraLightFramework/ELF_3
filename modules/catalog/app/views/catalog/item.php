<?php
//	print_R(Elf::$_data);exit;?>

<?php if (!empty(Elf::$_data[1])):?>
<div class="linemenu">
	<?=Elf::$_data[1]?>
</div>
<?php endif;?>
<?php if (!empty(Elf::$_data[0])):
	$rec = Elf::$_data[0];
?>
<div id="catalog-big-item">
	<h1><?=$rec['inner_name']?$rec['inner_name']:$rec['name']?></h1>
	<table>
		<tr>
			<td class="imgs">
			<?php if (!empty($rec['pictures'][0])):?>
				<div class="big-img">
					<img src="<?=$rec['pictures'][0]['src_image']?>" alt="<?=$rec['inner_name']?$rec['inner_name']:$rec['name']?>, <% lang:catalog:buyonline %>" />
				</div>
				<div class="pics-cont pics-cont-add" id="pics-cont">
					<div class="galery-items">
						<div class="arr l-arr" onclick="gal.shift('left')"></div>
						<div class="galery-cont"></div>
						<div class="arr r-arr" onclick="gal.shift('right')"></div>
					</div>
				</div>
			<?php else:?>
				<div class="big-img">
					<img src="/img/galery/catalog/no-photo.png" alt="<?=$rec['inner_name']?$rec['inner_name']:$rec['name']?>, <% lang:catalog:buyonline %>" title="<% lang:catalog:picnotfound %>" />
				</div>
			<?php endif;?>
			</td>
			<td class="info">
				<div class="price"><strong><% lang:catalog:price %>:</strong> <?=(int)$rec['price']?> <% lang:catalog:currency.short %></div>
				<input class="in-cart" data-cid="<?=$rec['id']?>" type="button" value="<% lang:catalog:incart %>" />
				<?php if (!empty($rec['avails'])):?>
				<h3 title="<% lang:catalog:avails.tlt %>"><% lang:catalog:avails %></h3>
				<?php foreach ($rec['avails'] as $a):?>
					<a href="/<?=$a['parent_alias']?>"><?=$a['parent_name']?></a><br />
				<?php endforeach;?>
				<?php endif;?>
				<?php if (!empty($rec['features'])):?>
				<h3 title="<% lang:catalog:features.tlt %> <?=$rec['inner_name']?$rec['inner_name']:$rec['name']?>" class="ftrs"><% lang:catalog:features %></h3>
				<?php foreach ($rec['features'] as $f):?>
				<?=$f['name']?>: <?=$f['value']?>
				<?php endforeach;?>
				<?php endif;?>
			</td>
		</tr>
	</table>
</div>
<?php //if (!empty($rec['pictures'][0])):?>
<script>
var gal = new ELF_Galery(3,'pics-cont','catalog_pictures',{cid:<?=$rec['clone_id']?$rec['clone_id']:$rec['id']?>});
gal.start('icon',10,10);
</script>
<?php //endif;?>

<?php endif;?>