<?php
class SiteMapHelperTest extends UnitTestCase {

	function setUp() {
		BeanFactory :: clean();
		BeanFactory :: initialize(dirname(__FILE__) . '/data/siteMapHelperContext.xml');
	}
	
	function testGetCustomSiteMap() {
		$customSiteMap = SiteMapHelper::getCustomSiteMap(null, -1);		
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

		$customSiteMap = SiteMapHelper::getCustomSiteMap('default', -1);		
		$this->assertEqual(1, sizeof($customSiteMap), 'SiteMap is incorrect.');
		$this->validateSiteMap($customSiteMap);
		
		$customSiteMap = SiteMapHelper::getCustomSiteMap('test1', -1, false);		
		$this->assertEqual(1, sizeof($customSiteMap), 'SiteMap is incorrect.');	
		$this->validateSiteMap($customSiteMap);
		
		$customSiteMap = SiteMapHelper::getCustomSiteMap('test1', 0, false);		
		$this->assertEqual(1, sizeof($customSiteMap), 'SiteMap is incorrect.');	
		$this->validateSiteMap($customSiteMap);
		
		$customSiteMap = SiteMapHelper::getCustomSiteMap('test2', 1);		
		$this->assertEqual(1, sizeof($customSiteMap), 'SiteMap is incorrect.');
		$this->validateSiteMap($customSiteMap);
	}
	
	private function validateSiteMap($siteMap) {
		foreach ($siteMap as $siteMapItem) {
          $this->assertIsA($siteMapItem, 'SiteElement', 'Type does not match.');
		}
	}
}
?>