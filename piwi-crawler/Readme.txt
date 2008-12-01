To crawl a PIWI website invoke:
php Crawler.php SERVER URL LANGUAGES TARGET_DIRECTORY [FORMATS]

SERVER:           The adress of the server. E.g. 'http://127.0.0.1:80/'
URL:              The url where the crawling should be started. E.g. 'default.html'
LANGUAGES:        The languages that, apart from 'default', should be crawled. E.g 'en,fr'
TARGET_DIRECTORY: The directory where the crawled site should be placed. E.g. 'target'
FORMATS:          The formats that, apart from 'html', should be crawled. E.g. 'pdf,xml'

Example:          Crawler.php http://127.0.0.1:80/ default.html en,fr target pdf,xml