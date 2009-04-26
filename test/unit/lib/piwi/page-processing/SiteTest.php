<?php
class SiteTest extends UnitTestCase {
	
	private $site = null;
	
	function before($message) {		
		BeanFactory :: initialize(dirname(__FILE__) . '/data/context.xml');
		Request::setPageId('default');
		$this->site = BeanFactory :: getBeanById('site');			
	}
	
	function testGetTemplate() {
		$this->assertEqual('templates/default.php', $this->site->getTemplate(), 'Template does not match.');
		
		Request::setPageId('test1');
		$this->assertEqual('templates/other.php', $this->site->getTemplate(), 'Template does not match.');		
	}
	
	function testGetSupportedLanguages() {
		$languages = $this->site->getSupportedLanguages();
		
		$this->assertEqual(2, sizeof($languages), 'Languages do not match.');
		$this->assertTrue(in_array('default', $languages), 'Language is missing.');
		$this->assertTrue(in_array('de', $languages), 'Language is missing.');
	}
	
	function testGetSupportedLanguagesWithIllegalPath() {
		BeanFactory :: initialize(dirname(__FILE__) . '/data/context666.xml');
		$this->site = BeanFactory :: getBeanById('site');	
		
		$this->expectException(PiwiException, 'Path should be illegal.');
		$this->site->getSupportedLanguages();
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