<!doctype html>
<html amp lang="<?=SYSTEM_LANGUAGE?>">
	<head>
		<meta charset="utf-8" />
		<script async src="https://cdn.ampproject.org/v0.js"></script>
		<title><% title %></title>
		<meta name="description" content="<% description %>" />
		<link rel="canonical" href="<% link %>" />
		<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1" />
		<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
		<script type="application/ld+json">
		{
			"@context": "http://schema.org",
			"@type": "<?=Elf::$_data['content_type']=='news'?'News':''?>Article",
			"mainEntityOfPage": {
					"@type": "WebPage",
					"id": "<% link %>"
				},
			"author": {
				"@type": "<% lang:og:orgtype %>",
				"name": "<% lang:og:orgname %>"
			},
			"headline": "<% title %>",
			"description": "<% description %>",
			"datePublished": "<?=date('c',Elf::$_data['tm'])?>",
			"dateModified": "<?=date('c',Elf::$_data['tm_edit'])?>",
			"image": {
				"@type": "ImageObject",
				"url": "<?=Elf::$_data['picture']?Elf::$_data['picture_image']:'/img/design/no-image-icon.jpg'?>", 
				"image": "<?=Elf::$_data['picture']?Elf::$_data['picture_image']:'/img/design/no-image-icon.jpg'?>",
				"width": "700",
				"height": "450"
			},
			"publisher": {
				"@type": "<% lang:og:orgtype %>",
				"logo": {
					"@type": "ImageObject",
					"url": "{site_url}img/design/no-image-icon.jpg",
					"width": "640",
					"height": "400"
				},
				"name": "<% lang:og:publisher.name %>",
				"telephone": "<% lang:og:publisher.telephone %>",
				"address": "<% lang:og:publisher.address %>"
			}
		}
		</script>
	</head>
	<body>
		<% header %>
		<article>
		<% content %>
		</article>
	</body>
</html>