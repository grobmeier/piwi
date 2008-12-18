<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>PIWI - PHP Transformation Framework</title>
	<link href="custom/templates/default.css" rel="stylesheet" type="text/css" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="robots" content="index, follow" />
	<meta name="description" content="PIWI - PHP Transformation Framework" />
	<meta name="keywords" content="Piwi, piwi, framework, XML, XSLT, transformation, PHP, cocoon, forrest, apache, struts" />
	<script type="text/javascript" src="custom/templates/js/mootools-release-1.11.js"></script>
	<script type="text/javascript" src="custom/templates/js/rokslideshow.js"></script>
</head>

<body>

<div id="container">
	<div id="header">
	</div>
	<div id="menu">
		<?=$HTML_NAVIGATION?>		
		<span style="float: right">
			<a href="<?=Request::getPageId() . '.' . Request::getExtension() . '?language=de' ?>"><img src="custom/templates/images/flag_de.gif" alt="Deutsch" /></a>
			<a href="<?=Request::getPageId() . '.' . Request::getExtension() . '?language=default' ?>"><img src="custom/templates/images/flag_en.gif" alt="English" /></a>
		</span>
	</div>
	<div id="content">
		<div class="sitemappath">
			<?=$SITE_MAP_PATH?>
		</div>
		
		<?=$CONTENT['main']?>
	</div>
	<div id="footer">
		<div class="right">
			<a href="http://code.google.com/p/piwi/">Piwi @ Google Code</a>
		</div>
		Generated by Piwi | <a href="sitemap.html">SiteMap</a>
		<div style="clear: both">
		</div>
	</div>
</div>

</body>

</html>