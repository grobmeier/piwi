<?php
/**
 * Renders the requested page and creates the navigation.
 */
abstract class Page {
	
	/** The content of the requested page as DOMDocument. */
	protected $content = null;

	/** Name of the folder where the content is placed. */
	protected $contentPath = null;

	/** Reference to the Site. */
	protected $site = null;
	
	/** The configuration */
	protected $configuration = null;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
	}
	
	/**
	 * Check if user authentication is activated and whether the requested page has restrictions.
	 * @return true, if the user is allowed to access, false otherwise
	 */
	protected function checkPermissions() {
		$allowedRoles = $this->site->getAllowedRolesByPageId(Request::getPageId());

		// If authorization is required check if user has authorization
		if ($this->configuration->isAuthenticationEnabled() 
			&& !in_array('?', $allowedRoles)) {
			$roleProvider = $this->configuration->getRoleProvider();

			// Check if user is already logged in
			if (!UserSessionManager :: isUserAuthenticated() && in_array('!', $allowedRoles)) {
				return true;
			} else if (UserSessionManager :: isUserAuthenticated(true)) {
				// Check whether user has required role
				if (!in_array('*', $allowedRoles) && !$roleProvider->
						isUserInRole(UserSessionManager :: getUserName(), $allowedRoles)) {
					throw new PiwiException("Permission denied.", PiwiException :: PERMISSION_DENIED);
				}				
			} else {
				// Since user is not logged in, show login page				
				Request :: setPageId($this->configuration->getLoginPageId());
				Request :: setExtension('html');
				return false;
			}
		}		
		return true;
	}
	
	/**
	 * Returns cache instance.
	 * @return Cache The cache instance.
	 */
	protected function getCache() {
		// Determine global cachetime, if page specific cachetime exists use this
		$cachetime = $this->configuration->getCacheTime();
		$specificCacheTime = $this->site->getCacheTime();
		if ($specificCacheTime != null) {
			$cachetime = $specificCacheTime;
		}

		return new Cache($cachetime);
	}
	
	/** 
	 * Returns the content of this page.
	 * @return the content of this page as piwi xml
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * Sets the name of the folder where the content is placed.
	 * @param string $contentPath Name of the folder where the content is placed.
	 */
	public function setContentPath($path) {
		$this->contentPath = $path;
	}	
	
	/**
	 * Sets the reference to the Site.
	 * Injected by dependency injection.
	 * @param Site $site The reference to the Site.
	 */
	public function setSite(Site $site) {
		$this->site = $site;
	}
	
	/**
	 * Sets the configuration
	 * Injected by dependency injection.
	 * @param Configuration $configuration The reference to the configuration.
	 */
	
	public function setConfiguration(Configuration $configuration) {
		$this->configuration = $configuration;
	}
	
	/**
	 * Processes the contents of the page.
	 * @return the generated content
	 */
	public abstract function generateContent();
}
?>