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
		if (BeanFactory :: getBeanById('configurationManager')->isAuthenticationEnabled() 
			&& !in_array('?', $allowedRoles)) {
			$roleProvider = BeanFactory :: getBeanById('configurationManager')->getRoleProvider();

			// Check if user is already logged in
			if (!UserSessionManager :: isUserAuthenticated() && in_array('anonymous', $allowedRoles)) {
				return true;
			} else if (UserSessionManager :: isUserAuthenticated(true)) {
				// Check whether user has required role
				if (!in_array('*', $allowedRoles) && !$roleProvider->
						isUserInRole(UserSessionManager :: getUserName(), $allowedRoles)) {
					throw new PiwiException("Permission denied.", PiwiException :: PERMISSION_DENIED);
				}
			} else {
				// Since user is not logged in, show login page				
				Request :: setPageId(BeanFactory :: getBeanById('configurationManager')->getLoginPageId());
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
		// Determinate global cachetime, if page specific cachetime exists use this
		$cachetime = BeanFactory :: getBeanById('configurationManager')->getCacheTime();
		$specificCacheTime = $this->site->getCacheTime();
		if ($specificCacheTime != null) {
			$cachetime = $specificCacheTime;
		}

		return new Cache($cachetime);
	}
	
	/**
	 * Excecutes the Serializer.
	 */
	public function serialize() {
		$extension = Request :: getExtension();

		$serializer = BeanFactory :: getBeanById('configurationManager')->getSerializer($extension);

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
	
	public function getContent() {
	    return $this->content;
	}
	
	/**
	 * @param string $contentPath Name of the folder where the content is placed.
	 */
	public function setContentPath($path) {
		$this->contentPath = $path;
	}	
	
	/**
	 * Returns the reference to the Site.
	 * @return Site The reference to the Site.
	 */
	public function getSite() {
		return $this->site;
	}

	/**
	 * Sets the reference to the Site.
	 * @param Site $site The reference to the Site.
	 */
	public function setSite(Site $site) {
		$this->site = $site;
	}
	
	/**
	 * Processes the contents of the page.
	 */
	public abstract function generateContent();
}
?>