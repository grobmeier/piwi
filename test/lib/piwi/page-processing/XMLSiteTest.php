<?php
require_once ('test/PiwiTestCase.php');

class XMLSiteTest extends PiwiTestCase {

	private $currentPage = 0;
	private $rows = 30;
	private $rowsPerPage = 20;
	
	private $site = null;
	
	function setUp() {
		ConfigurationManager::initialize(dirname(__FILE__) . '/data/config.xml');
	}
	
	function before($message) {
		Request::setPageId('default');
		$this->site = new XMLSite(dirname(__FILE__) . '/data', 'templates', 'site.xml');				
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
	
	function testGetTemplate() {
		$this->site->generateContent();
		$this->assertEqual('templates/default.php', $this->site->getTemplate(), 'Template does not match.');
		
		Request::setPageId('test1');
		$this->site->generateContent();
		$this->assertEqual('templates/other.php', $this->site->getTemplate(), 'Template does not match.');		
	}
	
	function testGetCustomSiteMap() {
		$customSiteMap = $this->site->getCustomSiteMap(null, -1);		
		$this->assertEqual(3, sizeof($customSiteMap), 'SiteMap is incorrect.');
		$this->validateSiteMap($customSiteMap);
		$this->assertEqual('hidden', $customSiteMap[1]->getId(), 'Id does not match.');		
		$this->assertEqual('Hidden', $customSiteMap[1]->getLabel(), 'Label does not match.');
		$this->assertEqual('hidden.xml', $customSiteMap[1]->getFilePath(), 'FilePath does not match.');
		$this->assertTrue($customSiteMap[1]->isHiddenInNavigation(), 'HiddenInNavigation is not true.');
		$this->assertTrue($customSiteMap[1]->isHiddenInSiteMap(), 'HiddenInSiteMap is not true.');
		$this->assertFalse($customSiteMap[1]->isOpen(), 'Open is not false.');
		$this->assertFalse($customSiteMap[1]->isSelected(), 'Selected is not false.');
		$this->assertNull($customSiteMap[1]->getParent(), 'Parent is not null.');
		$children = $customSiteMap[2]->getChildren();
		$this->validateSiteMap($children);
		$this->assertEqual(1, sizeof($children), 'SiteMap is incorrect.');
		
		$customSiteMap = $this->site->getCustomSiteMap('default', -1);		
		$this->assertEqual(1, sizeof($customSiteMap), 'SiteMap is incorrect.');
		$this->validateSiteMap($customSiteMap);
		
		$customSiteMap = $this->site->getCustomSiteMap('test1', -1, false);		
		$this->assertEqual(1, sizeof($customSiteMap), 'SiteMap is incorrect.');	
		$this->validateSiteMap($customSiteMap);
		
		$customSiteMap = $this->site->getCustomSiteMap('test1', 0, false);		
		$this->assertEqual(1, sizeof($customSiteMap), 'SiteMap is incorrect.');	
		$this->validateSiteMap($customSiteMap);
		
		$customSiteMap = $this->site->getCustomSiteMap('test2', 1);		
		$this->assertEqual(1, sizeof($customSiteMap), 'SiteMap is incorrect.');
		$this->validateSiteMap($customSiteMap);
	}
	
	function testGetSupportedLanguages() {
		$languages = $this->site->getSupportedLanguages();
		
		$this->assertEqual(2, sizeof($languages), 'Languages do not match.');
		$this->assertTrue(in_array('default', $languages), 'Language is missing.');
		$this->assertTrue(in_array('de', $languages), 'Language is missing.');
	}
	
	function testGetSupportedLanguagesWithIllegalPath() {
		$site = new XMLSite(dirname(__FILE__) . '/data', 'templates', '666.xml');
		
		$this->expectException(PiwiException, 'Path should be illegat.');
		$site->getSupportedLanguages();		
	}
	
	private function validateSiteMap($siteMap) {
		foreach ($siteMap as $siteMapItem) {
       		$this->assertIsA($siteMapItem, NavigationElement, 'Type does not match.');
		}
	}
}
?>