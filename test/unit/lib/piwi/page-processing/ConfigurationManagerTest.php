<?php
class ConfigurationManagerTest extends UnitTestCase {
	private $site = null;
	
	private $configurationManager;
	
	function setUp() {
	}
	
	function before($message) {
		BeanFactory :: clean();
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
		$configurationManager = new ConfigurationManager();
		$configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_cache.xml');
		$this->assertIsA($configurationManager->getSerializer('pdf'), 'Serializer', 'Type does not match.');
		$this->assertNull($configurationManager->getSerializer('doesnotexist'), 'Serializer is not null.');

		// Does only write to CLI in error case
		ob_start();
		$this->assertNull($configurationManager->getSerializer('xls'), 'Serializer is not null.');
		$cli = ob_get_contents();
		ob_end_clean();
		$this->assertEqual('The Class with name \'TestRoleProvider\' is not an instance of Serializer.', $cli, 'CLI output does not match expected value');
	}
	
	function testGetNullSerializer() {
		$configurationManager = new ConfigurationManager();
		$configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull($configurationManager->getSerializer('html'), 'Serializer is not null.');
	}

	function testGetHTMLNavigations() {
		$GLOBALS['testconfig'] = dirname(__FILE__) . '/data/config_cache.xml';
		BeanFactory :: initialize(dirname(__FILE__) . '/data/contextConfigurationManager.xml');
		
		// Does only write to CLI in error case
		ob_start();
		$navigation = $this->configurationManager->getHTMLNavigations();
		$cli = ob_get_contents();
		ob_end_clean();
		
		$this->assertEqual('The Class with name \'TestRoleProvider\' is not an instance of NavigationGenerator.Custom Navigation Generator not found: 666', $cli, 'CLI output does not match expected value');
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
		$this->assertIsA($this->configurationManager->getRoleProvider(), 'TestRoleProvider', 'Type does not match.');
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');

		try {
			$this->configurationManager->getRoleProvider();
			$this->assertFalse(true, 'RoleProvider should not be specified.');
		} catch(ReflectionException $e) {
			$this->assertIsA($e, 'ReflectionException', 'Exception of wrong type raised');
		}
	}
	
	function testGetLoginPageId() {
		$this->assertEqual('test1', $this->configurationManager->getLoginPageId(), 'LoginPageId does not match.');
		
		$this->configurationManager = new ConfigurationManager();
		$this->configurationManager->setConfigFilePath(dirname(__FILE__) . '/data/config_empty.xml');
		$this->assertNull($this->configurationManager->getLoginPageId(), 'LoginPageId is not null..');
	}
}
?>