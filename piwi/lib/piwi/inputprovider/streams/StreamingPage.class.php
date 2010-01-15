<?php
/**
 * Renders the requested page and creates the navigation based on 'site.xml'.
 */
class StreamingPage extends Page {
	
	private $streamingFilePath = null;
	
	/** Constructor. */
    public function __construct() {
    }
    
    /**
	 * TODO
	 */
	public function generateContent() {
		if (!$this->checkPermissions()) {
			Request :: setPageId(BeanFactory :: getBeanById('configurationManager')->getLoginPageId());
			Request :: setExtension('html');
			$xmlpage = BeanFactory :: getBeanById('xmlPage');
			$xmlpage->generateContent();
			$this->content = $xmlpage->getContent();
			return false;
		}
		
		$filePath = $this->site->getFilePath();

		$path = $this->getSite()->getFilePath();
		$pos = strrpos($path, ".");
		$id = substr($path, 0, $pos);
		
		$sm = new StreamManager();
		$sm->setStreamConfiguration($this->getStreamingFilePath());
		$info = $sm->getStreamInfo($id);
		
		
		$actions = $sm->getStreamActions($id);
		$this->_callActions($actions);
		
		// TODO: currently only file:// can be used
		// handle all other urischemes here
		
		
		$piwixmltop = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<document xmlns="http://piwi.googlecode.com/xsd/piwixml"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://piwi.googlecode.com/xsd/piwixml ../../../../resources/xsd/content.xsd">
	<head>
		<title>Streaming</title>
		<stream file="
EOF;
		
		$piwixmlmiddle = <<<EOF
" name="
EOF;

		$piwixmlbottom = <<<EOF
" />
	</head>
	<content />
</document>
EOF;
		
		$piwixml = $piwixmltop . $info->uri . $piwixmlmiddle . $info->name . $piwixmlbottom;

		Request::setExtension('stream');
		$dom = new DOMDocument();
		$dom->loadXml($piwixml);
		$this->content = $dom;
	}
	
	/**
	 * Calls actions configured for this stream
	 */
	private function _callActions($actions) {
		if ($actions != null) {
			foreach ($actions as $action) {
				$class = new ReflectionClass((string)$action);
				$preprocessor = $class->newInstance();
				
				if (!$preprocessor instanceof Preprocessor) {
					throw new PiwiException("The Class with id '" . $result .
							"' is not an instance of Preprocessor.", 
						PiwiException :: ERR_WRONG_TYPE);
				}
				$preprocessor->process();
			}
		}
	}
	
	/**
	 * Returns the reference to the Site.
	 * @return Site The reference to the Site.
	 */
	public function getStreamingFilePath() {
		return $this->streamingFilePath;
	}

	/**
	 * Sets the reference to the Site.
	 * @param Site $site The reference to the Site.
	 */
	public function setStreamingFilePath($streamingFilePath) {
		$this->streamingFilePath = $streamingFilePath;
	}
}
?>