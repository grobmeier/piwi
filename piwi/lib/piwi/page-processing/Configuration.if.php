<?php
/**
 * Interface that configurations have to implement.
 */
interface Configuration {
	public function getCacheTime();
    public function getSerializer($extension);
    public function getHTMLNavigations();
    public function getCustomLabelsPath();
    public function getCustomXSLTStylesheetPath();
    public function getLoggingConfiguration();
	public function isAuthenticationEnabled();
	public function getRoleProvider();
    public function getLoginPageId();
    public function setConfigFilePath($configFilePath) ;
}
?>