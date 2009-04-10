<?php
class ConfigurationManagerTest extends UnitTestCase {
	private $site = null;
	
	function before($message) {
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_cache.xml');
	}
	
	function testGetCacheTime() {
		$this->assertEqual(10, ConfigurationManager::getInstance()->getCacheTime(), 'CacheTime does not match.');
				
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertEqual(0, ConfigurationManager::getInstance()->getCacheTime(), 'CacheTime does not match.');
	}
	
	function testGetSerializer() {		
		$this->assertIsA(ConfigurationManager::getInstance()->getSerializer('pdf'), Serializer, 'Type does not match.');
		$this->assertNull(ConfigurationManager::getInstance()->getSerializer('doc'), 'Serializer is not null.');
		$this->assertNull(ConfigurationManager::getInstance()->getSerializer('xls'), 'Serializer is not null.');
			
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull(ConfigurationManager::getInstance()->getSerializer('html'), 'Serializer is not null.');
	}
	
	function testGetHTMLNavigations() {
		Site::setInstance(new XMLSite(dirname(__FILE__) . '/data', 'templates', 'site.xml'));
		
		$navigation = ConfigurationManager::getInstance()->getHTMLNavigations();
		$this->assertEqual(2, sizeof($navigation), 'HTMLNavigations have incorrect size.');
		
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_empty.xml');
		$navigation = ConfigurationManager::getInstance()->getHTMLNavigations();
		$this->assertEqual(0, sizeof($navigation), 'HTMLNavigations have incorrect size.');
	}
	
	function testGetCustomLabelsPath() {
		$this->assertEqual('test', ConfigurationManager::getInstance()->getCustomLabelsPath(), 'CustomLabelsPath does not match.');
		
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull(ConfigurationManager::getInstance()->getCustomLabelsPath(), 'CustomLabelsPath is not null.');
	}
	
	function testGetCustomXSLTStylesheetPath() {
		$this->assertEqual('test', ConfigurationManager::getInstance()->getCustomXSLTStylesheetPath(), 'XSLTStylesheetPath does not match.');
		
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull(ConfigurationManager::getInstance()->getCustomXSLTStylesheetPath(), 'XSLTStylesheetPath is not null.');
	}
	
	function testIsAuthenticationEnabled() {
		$this->assertTrue(ConfigurationManager::getInstance()->isAuthenticationEnabled(), 'Authentication is not enabled.');
		
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertFalse(ConfigurationManager::getInstance()->isAuthenticationEnabled(), 'Authentication is not disabled.');
	}	
	
	function testGetRoleProvider() {
		$this->assertIsA(ConfigurationManager::getInstance()->getRoleProvider(), TestRoleProvider, 'Type does not match.');
		
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_empty.xml');
		$this->expectException(ReflectionException, 'RoleProvider should not be specified.');
		ConfigurationManager::getInstance()->getRoleProvider();
	}
	
	function testGetLoginPageId() {
		$this->assertEqual('test1', ConfigurationManager::getInstance()->getLoginPageId(), 'LoginPageId does not match.');
		
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull(ConfigurationManager::getInstance()->getLoginPageId(), 'LoginPageId is not null..');
	}
}
?>