<?php
 
if (isset($argv[1]) && isset($argv[2]) && isset($argv[3]) && isset($argv[4])) {
	require_once ("classes/Crawler.class.php");
	
	$languages = array();	
	foreach (explode(',', str_replace(' ', '', $argv[3])) as $language) {
		if ($language != 'default') {
			$languages[] = $language;
		}
	}
	
	$crawler = new Crawler($argv[1], $argv[2], $languages, $argv[4]);
	$crawler->startCrawling();
	exit(0);
} else {
	echo "Error:   Illegal arguments.\n";
	echo "Syntax:  Crawler SERVER URL LANGUAGES TARGET_DIRECTORY\n";
	echo "Example: Crawler http://127.0.0.1:80/ default.html default,de target\n";
	exit(1);
}
?>