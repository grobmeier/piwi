<?php
require_once ('test/PiwiWebTestCase.php');

class LocalizationTest extends PiwiWebTestCase {
	
	function testHeadersTranslatedInDataGrid() {
		$this->get(self :: $HOST . 'datagrid.html');
		
		// check if headers are translated
		$this->assertWantedText('Name', 'Header invalid.');
		$this->assertWantedText('Adress', 'Header invalid.');
		$this->assertWantedText('City', 'Header invalid.');
		$this->assertWantedText('Phone', 'Header invalid.');
		
		$this->assertNoText('NAME', 'Header invalid.');
		$this->assertNoText('ADRESS', 'Header invalid.');
		$this->assertNoText('CITY', 'Header invalid.');
		$this->assertNoText('PHONE', 'Header invalid.');
	}
	
	function testHeaderInSiteMap() {
		$this->get(self :: $HOST . 'sitemap.html');
		
		// check if header is translated
		$this->assertWantedText('Contents of this Website', 'Header invalid.');
		$this->assertNoText('CONTENTS_OF_THIS_WEBSITE', 'Header invalid.');
	}
	
	function testSwitchingLanguage() {
		$this->get(self :: $HOST . 'sitemap.html');
		
		// check if translation is ok
		$this->assertWantedText('Contents of this Website', 'Translation invalid.');
		
		$this->get(self :: $HOST . 'sitemap.html?language=de');
		$this->assertWantedText('Inhalte dieser Webseite', 'Translation invalid.');
		
		$this->get(self :: $HOST . 'sitemap.html?language=default');
		$this->assertWantedText('Contents of this Website', 'Translation invalid.');
	}
}
?>