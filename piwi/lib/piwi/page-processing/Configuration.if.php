<?php
/**
 * Interface that configurations have to implement.
 */
interface Configuration {
	 /**
      * Returns the cachetime (the time that may pass until the content of the page is regenerated).
      * @return integer The cachetime.
      */
	public function getCacheTime();
	
	/** 
     * Returns the Serializer for the given format or null if none is found.
     * @return Serializer The Serializer for the given format or null if none is found.
     */
	public function getSerializer($extension);
    
    /**
     * Returns an array containing all Navigations.
     * The keys in the array, are the names (specified in 'config.xml') which can be placed in the templates.
     * The values are the the generated menus as XHTML.
     * @return array Array containing all Navigations.
     */	
    public function getHTMLNavigations();
    
    /**
     * Returns the path of the a custom labels file or null if none is specified.
     * @return string The path of the a custom labels file or null if none is specified.
     */    
    public function getCustomLabelsPath();
    
    /**
     * Returns the path of the a custom XSLT stylesheet or null if none is specified.
     * @return string The path of the a custom XSLT stylesheet or null if none is specified.
     */    
    public function getCustomXSLTStylesheetPath();
 	
 	/**
     * Returns the path to the logging configuration file or null if none is specified.
     * @return string The path or null, if none is specified.
     */    
    public function getLoggingConfiguration();
	
	/**
	 * Returns true if authentication is enabled otherwise false.
	 * @return boolean True if authentication is enabled otherwise false.
	 */    
	public function isAuthenticationEnabled();
    
    /** 
     * Returns the RoleProvider which manages the authentication of users.
     * @return RoleProvider The RoleProvider which manages the authentication of users.
     */	
	public function getRoleProvider();
    
    /**
     * Returns the id of the login page.
     * @return string The id of the login page.
     */	
    public function getLoginPageId();
    
    /**
     * Sets the path of the file containing the configuration
     * @param string $configFilePath Path of the file containing the configuration.
     */    
    public function setConfigFilePath($configFilePath);
}
?>