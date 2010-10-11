<?php
class XMLPageTest extends UnitTestCase {
	
	private $page = null;
	
	function before($message) {
		BeanFactory :: clean();
		BeanFactory :: initialize(dirname(__FILE__) . '/data/context.xml');
		Request::setPageId('default');
		$this->page = BeanFactory :: getBeanById('xmlPage');			
	}
	
	function testGenerateContent() {
		Request::setPageId('hidden');
		$this->page->generateContent();
		$this->assertEqual('test1', Request::getPageId(), 'PageId does not match.');
	}

	function testGenerateContentFromCache() {
		BeanFactory :: initialize(dirname(__FILE__) . '/data/contextCache.xml');
		$this->page = BeanFactory :: getBeanById('xmlPage');
		
		$this->page->generateContent();
		$this->page->generateContent();
	}
	
	function testGenerateContentWithIllegalPageId() {
		Request::setPageId('666');
		$this->expectException('PiwiException', 'PageId should be illegal.');
		$this->page->generateContent();
	}
	
	function testGenerateContentWithNonExistingContentFile() {
		BeanFactory :: initialize(dirname(__FILE__) . '/data/contextIllegal.xml');
		$this->page = BeanFactory :: getBeanById('xmlPage');

		$this->expectException('PiwiException', 'Page should not exist.');
		$this->page->generateContent();
	}
	
	function testGenerateContentWithDoublePageId() {
		BeanFactory :: initialize(dirname(__FILE__) . '/data/contextIllegal.xml');
		$this->page = BeanFactory :: getBeanById('xmlPage');
		Request::setPageId('test');
		
		$this->expectException('PiwiException', 'PageId should exist twice.');
		$this->page->generateContent();
	}
}
?>