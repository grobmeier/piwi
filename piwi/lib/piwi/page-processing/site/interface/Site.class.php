<?php

/**
 * Renders the requested page and creates the navigation.
 */
abstract class Site {
	/** Singleton instance of the Site. */
	private static $siteInstance = null;

	/** Name of the folder where the content is placed. */
	protected $contentPath = null;

	/** Name of the folder where your templates are placed. */
	protected $templatesPath = null;

	/** The template of the requested page. */
	private $template = "default.php";

	/** The content of the requested page as DOMDocument. */
	private $content = null;

	/**
	 * Constructor.
	 * @param string $contentPath Name of the folder where the content is placed.
	 * @param string $templatesPath Name of the folder where your templates are placed.
	 */
	public function __construct($contentPath, $templatesPath) {
		$this->contentPath = $contentPath;
		$this->templatesPath = $templatesPath;
	}

	/**
	 * Reads the xml of the requested page and transforms the Generators to Piwi-XML.
	 */
	public function generateContent() {
		$allowedRoles = $this->getAllowedRoles();

		// If authorization is required check if user has authorization
		if (ConfigurationManager :: getInstance()->isAuthenticationEnabled() && !in_array('?', $allowedRoles)) {
			$roleProvider = ConfigurationManager :: getInstance()->getRoleProvider();

			// Check if user is already logged in
			if (UserSessionManager :: isUserAuthenticated(true)) {

				// Check whether user has required role
				if (!in_array('*', $allowedRoles) && !$roleProvider->isUserInRole(UserSessionManager :: getUserName(), $allowedRoles)) {
					throw new PiwiException("Permission denied.", PiwiException :: PERMISSION_DENIED);
				}
			} else {
				// Since user is not logged in, show login page				
				Request :: setPageId(ConfigurationManager :: getInstance()->getLoginPageId());
			}
		}

		// Determinate global cachetime, if page specific cachetime exists use this
		$cachetime = ConfigurationManager :: getInstance()->getCacheTime();
		$specificCacheTime = $this->getCacheTime();
		if ($specificCacheTime != null) {
			$cachetime = $specificCacheTime;
		}

		// Try to get contents from cache
		$cache = new Cache($cachetime);
		$content = $cache->getPage();
		if ($content != null) {
			// Page has been found in cache
			$this->content = $content;
		} else {
			// Page has not been found in cache, so load it and save it in the cache
			$filePath = $this->contentPath . '/' . $this->getFilePath();

			if (!file_exists($filePath)) {
				throw new PiwiException("Could not find the the requested page (Path: '" . $filePath . "').", PiwiException :: ERR_404);
			}

			$this->content = new DOMDocument;
			$this->content->load($filePath);

			// Configure the transformer
			$processor = new XSLTProcessor;
			$processor->registerPHPFunctions();
			$processor->importStyleSheet(DOMDocument :: load($GLOBALS['PIWI_ROOT'] . "resources/xslt/GeneratorTransformation.xsl"));

			// Transform the Generators
			$this->content = $processor->transformToDoc($this->content);

			// Save page in cache
			$cache->cachePage($this->content);
		}

		// Set template if specified
		$template = $this->getHTMLTemplatePath();
		if ($template != null) {
			$this->template = $template;
		}
	}

	/**
	 * Excecutes the Serializer
	 * Returns the Serializer for the given extension.
	 */
	public function serialize() {
		$extension = Request :: getExtension();

		$serializer = ConfigurationManager :: getInstance()->getSerializer($extension);

		if ($serializer == null) {
			if ($extension == "xml") {
				$serializer = new PiwiXMLSerializer();
			} else
				if ($extension == "pdf") {
					$serializer = new PDFSerializer();
				} else
					if ($extension == "xls") {
						$serializer = new ExcelSerializer();
					} else
						if ($extension == "doc") {
							$serializer = new WordSerializer();
						} else {
							$serializer = new HTMLSerializer();
						}
		}

		$serializer->serialize($this->content);
	}

	/**
	 * Returns the 'SiteMap' which is an array of NavigationElements representing the website structure.
	 * It is possible to retrieve only certain parts of the 'SiteMap'.
	 * @param string $id Returns only the NavigationElement with this id and its subitems. Set to 'null' if all NavigationElements should be returned.
	 * @param integer $depth Determinates the maximum number of subitems that will be returned. Set to '-1' for full depth, to '0' only for parent items. 
	 * @param boolean $includeParent If false only the children of the parent will be returned. This can only be used if $id is not 'null'.
	 * @return The 'SiteMap' which is an array of NavigationElements representing the website structure.
	 */
	public function getCustomSiteMap($id, $depth, $includeParent = true) {
		$siteMap = $this->getFullSiteMap();

		// Build menu
		// If id is specified show only this item and its subitems
		if ($id != null) {
			$navigationElements = array ();

			// retrieve the NavigationElement with the given id
			$element = $this->getSiteMapItemsById($siteMap, $id);

			// if parent should be not included show only its children otherwise the parent itself
			if ($element != null && $includeParent) {
				$navigationElements[0] = $element;
			} else
				if ($element != null && $element->getChildren() != null) {
					$index = 0;
					foreach ($element->getChildren() as $element) {
						$navigationElements[$index++] = $element;
					}
				}
		} else {
			$navigationElements = $siteMap;
		}

		// If depth is greater than -1, cut off subitems
		if ($depth >= 0) {
			foreach ($navigationElements as $element) {
				$this->cutOffSiteMap(0, $depth, $element);
			}
		}
		return $navigationElements;
	}

	/**
	 * Returns the NavigationElement with the given in id or null if no item is found.
	 * @param array $siteMap The array of NavigationElements to search in.
	 * @param string $id The id of the NavigationElement.
	 * @param NavigationElement $foundElement The NavigationElement with the given in id or null if no item has been found yet.
	 * @return NavigationElement The NavigationElement with the given in id or null if no item is found.
	 */
	private function getSiteMapItemsById($siteMap, $id, NavigationElement $foundElement = null) {
		foreach ($siteMap as $element) {
			if ($element->getId() == $id) {
				$foundElement = $element;
				break;
			} else
				if ($element->getChildren() != null) {
					$foundElement = $this->getSiteMapItemsById($element->getChildren(), $id, $foundElement);
				}
		}

		return $foundElement;
	}

	/**
	 * Removes all subitems in the given NavigationElement that exceed the given depth.
	 * @param integer $currentDepth The current depth.
	 * @param integer $depth description The desired depth.
	 * @param NavigationElement $navigationElement The NavigationElement whose children should be cut off.
	 */
	private function cutOffSiteMap($currentDepth, $depth, NavigationElement $navigationElement) {
		if ($currentDepth++ >= $depth) {
			$navigationElement->setChildren(null);
		} else {
			if ($navigationElement->getChildren() != null) {
				foreach ($navigationElement->getChildren() as $element) {
					$this->cutOffSiteMap($currentDepth, $depth, $element);
				}
			}
		}
	}

	/**
	 * Returns the template of the requested page.
	 * @return string The template of the requested page.
	 */
	public function getTemplate() {
		return $this->templatesPath . '/' . $this->template;
	}

	/**
	 * Sets the content of the page.
	 * @param string $content The content as xml.
	 */
	public function setContent($content) {
		$this->content = new DOMDocument;
		$this->content->loadXML($content);
	}

	/**
	 * Returns the singleton instance of the Site.
	 * @return Site The singleton instance of the Site.
	 */
	public static function getInstance() {
		return self :: $siteInstance;
	}

	/**
	 * Sets the singleton instance of the Site.
	 * @param Site $site The singleton instance of the Site.
	 */
	public static function setInstance(Site $site) {
		Site :: $siteInstance = $site;
	}

	/**
	 * ---------------------------------------------------------------------
	 * >>>>>>>>>>>>>>>>>>>>>>>>>> Abstract Methods <<<<<<<<<<<<<<<<<<<<<<<<<
	 * ---------------------------------------------------------------------
	 */

	/**
	 * Returns the path of the xml file containing the content of the requested page.
	 * @return string The path of the xml file containing the content of the requested page.
	 */
	protected abstract function getFilePath();

	/**
	 * Returns the template of the requested page or null if not specified.
	 * @return string The template of the requested page.
	 */
	protected abstract function getHTMLTemplatePath();

	/**
	 * Returns the 'SiteMap' which is an array of NavigationElements representing the whole website structure.
	 * @return array Array of NavigationElements representing the whole website structure.
	 */
	protected abstract function getFullSiteMap();

	/**
	 * Returns the possible roles a user needs to access the currently requested page.
	 * The possible roles are returned as array.
	 * The array contains only '?' if no authentication is required.
	 * The array contains only '*' if any authenticated user allowed to access the page.
	 * @return array The possible roles a user needs to access the currently requested page.
	 */
	protected abstract function getAllowedRoles();

	/**
	 * Returns a list of supported languages.
	 * @return array List of supported languages.
	 */
	public abstract function getSupportedLanguages();
	
	/**
	 * Returns the page specific cachetime (the time that may pass until the content of the page is regenerated) or null if none is specified.
     * @return integer The page specific cachetime or null if none is specified.
	 */
	protected abstract function getCacheTime();
}
?>