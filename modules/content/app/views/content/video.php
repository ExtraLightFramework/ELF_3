<% breadcrumbsline %>
<section class="video" itemscope itemtype="http://schema.org/Movie" rel="nofollow">
	<meta itemprop="datePublished" content="<?=date('Y-m-d', Elf::get_data('tm'))?>">
	<meta itemprop="dateCreated" content="<?=date('Y-m-d', Elf::get_data('tm'))?>">
	<meta itemprop="inLanguage" content="<?=SYSTEM_LANGUAGE?>">
	<h1 itemprop="name" class="article-tlt"><% title %></h1>
	<meta itemprop="alternativeHeadline" content="<% alias %>">
	<iframe src="https://www.youtube.com/embed/<% picture %>" frameborder="0" allowfullscreen="1" class="video"></iframe>
	<div itemprop="description">
		<% text %>
	</div>
</section>
