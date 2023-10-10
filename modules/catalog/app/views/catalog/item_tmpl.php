<?php
	$v = Elf::$_data['item'];
?>
		<?php if (($v['type'] == 'rubric')
					|| ($v['price']!=0)):?>
		<div class="catalog-item">
			<div class="pics-cont" id="pics-cont<?=$v['id']?>">
				<div class="galery-items">
					<div class="arr l-arr" onclick="gal<?=$v['id']?>.shift('left')"></div>
					<div class="galery-cont">
					<?php //if (!empty($v['pictures'][0]['src_icon'])):?>
<!--					<img src="<?=$v['pictures'][0]['src_icon']?>" alt="<% lang:catalog:buyonline %> <?=$v['inner_name']?$v['inner_name']:$v['name']?>" class="nominal-img" />
-->					<?php //else:?>
<!--						<div class="galery-item">
							<img src="/img/galery/catalog/icons/no-photo.png" class="no-photo" alt="<?=$v['inner_name']?$v['inner_name']:$v['name']?>, <% lang:catalog:buyonline %>" />
						</div> -->
					<?php //endif;?>
					</div>
					<div class="arr r-arr" onclick="gal<?=$v['id']?>.shift('right')"></div>
				</div>
			</div>
			<div class="item-info">
				<a href="{site_url}<?=$v['uri']?>" title="<% lang:catalog:fullinfo %> <?=$v['inner_name']?$v['inner_name']:$v['name']?>">
					<h2><?=$v['inner_name']?$v['inner_name']:$v['name']?></h2>
				</a>
				<div>
				<?php if ($v['type'] == 'rubric'):?>
					<?=$v['desc']?>
				<?php else:?>
					<div class="price"><?=(int)$v['price']?> <% lang:catalog:currency.short %></div>
					<input class="in-cart" data-cid="<?=$v['id']?>" type="button" value="<% lang:catalog:incart %>" title="<% lang:catalog:incart.tlt %>" />
				<?php endif;?>
				</div>
			</div>
		</div>
		<?php endif;?>
<script>
var gal<?=$v['id']?> = new ELF_Galery(1,'pics-cont<?=$v['id']?>','catalog_pictures',{cid:<?=$v['clone_id']?$v['clone_id']:$v['id']?>});
gal<?=$v['id']?>.start('icon');
</script>
