<?php
 
if (isset($argv[1])) {
	require_once ("classes/Crawler.class.php");
	
	$languages = array();	
	if (isset($argv[2])) {
		foreach (explode(',', str_replace(' ', '', $argv[2])) as $language) {
			if ($language != 'default') {
				$languages[] = $language;
			}
		}       
	}
	
	$targetDirectory = 'target';
	if (isset($argv[3])) {
		$targetDirectory = $argv[3];
	}	
	
	$crawler = new Crawler($argv[1], $languages, $targetDirectory);
	$crawler->startCrawling();
	exit(0);
} else {
	echo "Error:   No server is specified.\n";
	echo "Syntax:  Crawler SERVER [LANGUAGES] [TARGETDIRECTORY]\n";
	echo "Example: Crawler http://127.0.0.1:80 default,de target\n";
	exit(1);
}
?>