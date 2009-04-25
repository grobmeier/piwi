<?php
class XMLPageTest extends UnitTestCase {
	
	private $page = null;
	
	function before($message) {		
		BeanFactory :: initialize(dirname(__FILE__) . '/data/context.xml');
		Request::setPageId('default');
		$this->page = BeanFactory :: getBeanById('xmlPage');
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config.xml');			
	}
	
	function testGenerateContent() {
		Request::setPageId('hidden');
		$this->page->generateContent();
		$this->assertEqual('test1', Request::getPageId(), 'PageId does not match.');
	}

	function testGenerateContentFromCache() {
		BeanFactory :: initialize(dirname(__FILE__) . '/data/context.xml');
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_cache.xml');
		$this->page->generateContent();
		$this->page->generateContent();
	}
	
//	function testGenerateContentWithIllegalPageId() {
//		Request::setPageId('666');
//		$this->expectException(PiwiException, 'PageId should be illegal.');
//		$this->page->generateContent();
//	}
//	
//	function testGenerateContentWithNonExistingContentFile() {
//		$site = new XMLPage(dirname(__FILE__) . '/data', 'templates', 'site_illegal.xml');	
//
//		$this->expectException(PiwiException, 'Page should not exist.');
//		$errorlevel = error_reporting();
//		error_reporting(0);
//		$site->generateContent();
//		error_reporting($errorlevel);
//	}
//	
//	function testGenerateContentWithDoublePageId() {
//		$site = new XMLPage(dirname(__FILE__) . '/data', 'templates', 'site_illegal.xml');	
//		Request::setPageId('test');
//		
//		$this->expectException(PiwiException, 'PageId should exist twice.');
//		$errorlevel = error_reporting();
//		error_reporting(0);
//		$site->generateContent();
//		error_reporting($errorlevel);
//	}
//	
//	function testGetTemplate() {
//		$this->page->generateContent();
//		$this->assertEqual('templates/default.php', $this->page->getTemplate(), 'Template does not match.');
//		
//		Request::setPageId('test1');
//		$this->page->generateContent();
//		$this->assertEqual('templates/other.php', $this->page->getTemplate(), 'Template does not match.');		
//	}
//	
//	function testGetSupportedLanguages() {
//		$languages = $this->page->getSupportedLanguages();
//		
//		$this->assertEqual(2, sizeof($languages), 'Languages do not match.');
//		$this->assertTrue(in_array('default', $languages), 'Language is missing.');
//		$this->assertTrue(in_array('de', $languages), 'Language is missing.');
//	}
//	
//	function testGetSupportedLanguagesWithIllegalPath() {
//		$site = new XMLPage(dirname(__FILE__) . '/data', 'templates', '666.xml');
//		
//		$this->expectException(PiwiException, 'Path should be illegal.');
//		$site->getSupportedLanguages();
//	}
//	
//	function testGetAllowedRolesByPageId() {
//		$roles = $this->page->getAllowedRolesByPageId('hidden');
//		$this->assertEqual(1, sizeof($roles), 'Roles do not match.');
//		$this->assertTrue(in_array('admin', $roles), 'Roles do not match.');
//		
//		$roles = $this->page->getAllowedRolesByPageId('default');
//		$this->assertEqual(1, sizeof($roles), 'Roles do not match.');
//		$this->assertTrue(in_array('?', $roles), 'Roles do not match.');
//	}
}
?>