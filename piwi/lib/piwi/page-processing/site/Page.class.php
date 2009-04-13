<?php

/**
 * Renders the requested page and creates the navigation.
 */
abstract class Page {
	
	/** The content of the requested page as DOMDocument. */
	protected $content = null;

	/** Name of the folder where the content is placed. */
	protected $contentPath = null;

	protected $site = null;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	protected function checkPermissions() {
		$allowedRoles = $this->site->getAllowedRolesByPageId(Request::getPageId());

		// If authorization is required check if user has authorization
		if (ConfigurationManager :: getInstance()->isAuthenticationEnabled() && !in_array('?', $allowedRoles)) {
			$roleProvider = ConfigurationManager :: getInstance()->getRoleProvider();

			// Check if user is already logged in
			if (UserSessionManager :: isUserAuthenticated(true)) {
				// Check whether user has required role
				if (!in_array('*', $allowedRoles) && !$roleProvider->
						isUserInRole(UserSessionManager :: getUserName(), $allowedRoles)) {
					throw new PiwiException("Permission denied.", PiwiException :: PERMISSION_DENIED);
				}
			} else {
				// Since user is not logged in, show login page				
				Request :: setPageId(ConfigurationManager :: getInstance()->getLoginPageId());
			}
		}
	}
	
	protected function loadPageFromCache() {
		// Determinate global cachetime, if page specific cachetime exists use this
		$cachetime = ConfigurationManager :: getInstance()->getCacheTime();
		$specificCacheTime = $this->site->getCacheTime();
		if ($specificCacheTime != null) {
			$cachetime = $specificCacheTime;
		}

		// Try to get contents from cache
		$cache = new Cache($cachetime);
		return $cache;
	}
	
	/**
	 * Excecutes the Serializer.
	 */
	public function serialize() {
		$extension = Request :: getExtension();

		$serializer = ConfigurationManager :: getInstance()->getSerializer($extension);

		if ($serializer == null) {
			$serializer = new HTMLSerializer();
		}
		$serializer->serialize($this->content);
	}
	
	/**
	 * Sets the content of the page.
	 * @param string $content The content as xml.
	 */
	public function setContent($content) {
		$this->content = new DOMDocument;
		$this->content->loadXML($content);
	}

	public abstract function generateContent();
	
	/**
	 * @param string $contentPath Name of the folder where the content is placed.
	 */
	public function setContentPath($path) {
		$this->contentPath = $path;
	}
	
	public function setSite($site) {
		$this->site = $site;
	}
	
	public function getSite() {
		return $this->site;
	}
}
?>