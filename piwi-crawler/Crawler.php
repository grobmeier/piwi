<?php
/**
 * $argv[1]: The adress of the server. E.g. 'http://127.0.0.1:80/'
 * $argv[2]: The url where the crawling should be started. E.g. 'default.html'
 * $argv[3]: The languages that, apart from 'default', should be crawled. E.g 'en,fr'
 * $argv[4]: The directory where the crawled site should be placed. E.g. 'target'
 * $argcv[5]: The formats that, apart from 'html', should be crawled. E.g. 'pdf,xml'
 */
if (isset($argv[1]) && isset($argv[2]) && isset($argv[3]) && isset($argv[4])) {
	require_once ("classes/Crawler.class.php");
	
	if (!endsWith($argv[1], '/')) {
		$argv[1] .= '/';
	}
	
	// Determinate desired languages
	$languages = array();	
	foreach (explode(',', str_replace(' ', '', $argv[3])) as $language) {
		if ($language != 'default') {
			$languages[] = $language;
		}
	}
	
	// Determniate desired formats
	$formats = array();
	if (isset($argv[5])) {
		foreach (explode(',', str_replace(' ', '', $argv[5])) as $format) {
			if ($format != 'html') {
				$formats[] = $format;
			}
		}
	}
	
	$crawler = new Crawler($argv[1], $argv[2], $languages, $argv[4], $formats);
	$crawler->startCrawling();
	exit(0);
} else {
	echo "Error:   Illegal arguments.\n";
	echo "Syntax:  Crawler SERVER URL LANGUAGES TARGET_DIRECTORY [FORMATS]\n";
	echo "Example: Crawler.php http://127.0.0.1:80/ default.html en,fr target pdf,xml\n";
	exit(1);
}

/**
 * Determinates whether a text ends with a given string or not.
 *
 * @param string $haystack The text to check.
 * @param string $needle The text to search.
 * @return boolean True if $haystack ends with $needle, otherwise false.
 */
function endsWith($haystack, $needle){
    return strrpos($haystack, $needle) === strlen($haystack)-strlen($needle);
}
?>