<?php
class ConfigurationManagerTest extends UnitTestCase {
	private $site = null;
	
	private $configurationManager;
	
	function before($message) {
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_cache.xml');
	}
	
	function testGetCacheTime() {
		$this->assertEqual(10, $this->configurationManager->getCacheTime(), 'CacheTime does not match.');
				
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertEqual(0, $this->configurationManager->getCacheTime(), 'CacheTime does not match.');
	}
	
	function testGetSerializer() {		
		$this->assertIsA($this->configurationManager->getSerializer('pdf'), Serializer, 'Type does not match.');
		$this->assertNull($this->configurationManager->getSerializer('doc'), 'Serializer is not null.');
		$this->assertNull($this->configurationManager->getSerializer('xls'), 'Serializer is not null.');
			
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull($this->configurationManager->getSerializer('html'), 'Serializer is not null.');
	}

	function testGetHTMLNavigations() {
		BeanFactory :: initialize(dirname(__FILE__) . '/data/contextConfigurationManager.xml');
		
		$navigation = $this->configurationManager->getHTMLNavigations();
		$this->assertEqual(2, sizeof($navigation), 'HTMLNavigations have incorrect size.');
		
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$navigation = $this->configurationManager->getHTMLNavigations();
		$this->assertEqual(0, sizeof($navigation), 'HTMLNavigations have incorrect size.');
	}
	
	function testGetCustomLabelsPath() {
		$this->assertEqual('test', $this->configurationManager->getCustomLabelsPath(), 'CustomLabelsPath does not match.');
		
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull($this->configurationManager->getCustomLabelsPath(), 'CustomLabelsPath is not null.');
	}
	
	function testGetCustomXSLTStylesheetPath() {
		$this->assertEqual('test', $this->configurationManager->getCustomXSLTStylesheetPath(), 'XSLTStylesheetPath does not match.');
		
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull($this->configurationManager->getCustomXSLTStylesheetPath(), 'XSLTStylesheetPath is not null.');
	}
	
	function testGetLoggingConfiguration() {
		$this->assertEqual('logging-config.xml', $this->configurationManager->getLoggingConfiguration(), 'LoggingConfiguration does not match.');
		
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull($this->configurationManager->getLoggingConfiguration(), 'LoggingConfiguration is not null.');
	}
	
	function testIsAuthenticationEnabled() {
		$this->assertTrue($this->configurationManager->isAuthenticationEnabled(), 'Authentication is not enabled.');
		
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertFalse($this->configurationManager->isAuthenticationEnabled(), 'Authentication is not disabled.');
	}	
	
	function testGetRoleProvider() {
		$this->assertIsA($this->configurationManager->getRoleProvider(), TestRoleProvider, 'Type does not match.');
		
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->expectException(ReflectionException, 'RoleProvider should not be specified.');
		$this->configurationManager->getRoleProvider();
	}
	
	function testGetLoginPageId() {
		$this->assertEqual('test1', $this->configurationManager->getLoginPageId(), 'LoginPageId does not match.');
		
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull($this->configurationManager->getLoginPageId(), 'LoginPageId is not null..');
	}
}
?>