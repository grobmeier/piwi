<?php
require_once ('test/PiwiTestCase.php');

class XMLSiteTest extends PiwiTestCase {
	
	private $site = null;
	
	function before($message) {
		Request::setPageId('default');
		$this->site = new XMLSite(dirname(__FILE__) . '/data', 'templates', 'site.xml');	
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config.xml');			
	}
	
	function testGenerateContent() {
		Request::setPageId('hidden');
		$this->site->generateContent();
		$this->assertEqual('templates/other.php', $this->site->getTemplate(), 'Template does not match.');
		$this->assertEqual('test1', Request::getPageId(), 'PageId does not match.');
	}
	
	function testGenerateContentFromCache() {
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config_cache.xml');
		$this->site->generateContent();
		$this->site->generateContent();
	}
	
	function testGenerateContentWithIllegalPageId() {
		Request::setPageId('666');
		$this->expectException(PiwiException, 'PageId should be illegal.');
		$this->site->generateContent();
	}
	
	function testGenerateContentWithNonExistingContentFile() {
		$site = new XMLSite(dirname(__FILE__) . '/data', 'templates', 'site_illegal.xml');	

		$this->expectException(PiwiException, 'Page should not exist.');
		$errorlevel = error_reporting();
		error_reporting(0);
		$site->generateContent();
		error_reporting($errorlevel);
	}
	
	function testGenerateContentWithDoublePageId() {
		$site = new XMLSite(dirname(__FILE__) . '/data', 'templates', 'site_illegal.xml');	
		Request::setPageId('test');
		
		$this->expectException(PiwiException, 'PageId should exist twice.');
		$errorlevel = error_reporting();
		error_reporting(0);
		$site->generateContent();
		error_reporting($errorlevel);
	}
	
	function testGetTemplate() {
		$this->site->generateContent();
		$this->assertEqual('templates/default.php', $this->site->getTemplate(), 'Template does not match.');
		
		Request::setPageId('test1');
		$this->site->generateContent();
		$this->assertEqual('templates/other.php', $this->site->getTemplate(), 'Template does not match.');		
	}
	
	function testGetSupportedLanguages() {
		$languages = $this->site->getSupportedLanguages();
		
		$this->assertEqual(2, sizeof($languages), 'Languages do not match.');
		$this->assertTrue(in_array('default', $languages), 'Language is missing.');
		$this->assertTrue(in_array('de', $languages), 'Language is missing.');
	}
	
	function testGetSupportedLanguagesWithIllegalPath() {
		$site = new XMLSite(dirname(__FILE__) . '/data', 'templates', '666.xml');
		
		$this->expectException(PiwiException, 'Path should be illegal.');
		$site->getSupportedLanguages();
	}
	
	function testGetAllowedRolesByPageId() {
		$roles = $this->site->getAllowedRolesByPageId('hidden');
		$this->assertEqual(1, sizeof($roles), 'Roles do not match.');
		$this->assertTrue(in_array('admin', $roles), 'Roles do not match.');
		
		$roles = $this->site->getAllowedRolesByPageId('default');
		$this->assertEqual(1, sizeof($roles), 'Roles do not match.');
		$this->assertTrue(in_array('?', $roles), 'Roles do not match.');
	}
}
?>