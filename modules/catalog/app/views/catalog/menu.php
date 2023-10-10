<div class="admin-sub-mnu" id="admin-sub-mnu">
	<a href="/catalog/admin" class="admin-submnu-lnk<?=Elf::routing()->method()=='admin'?' selected':''?>"><% lang:catalog:catalog.title %></a>
	<a href="/catalog/features" class="admin-submnu-lnk<?=Elf::routing()->method()=='features'?' selected':''?>"><% lang:catalog:features.title %></a>
	<a href="/catalog/units" class="admin-submnu-lnk<?=Elf::routing()->method()=='units'?' selected':''?>"><% lang:catalog:unit.title %></a>
</div>
