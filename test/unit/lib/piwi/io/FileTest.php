<?php
class FileTest extends UnitTestCase {

	private $dir = null;
	private	$name = 'test.txt';
	
	function setUp() {
		$this->dir = dirname(__FILE__);
	}
	
	function testProperties() {
		$file = new File($this->dir, $this->name);
		
		$this->assertEqual($this->dir, $file->getPath(), 'Path does not match.');
		$this->assertEqual($this->name, $file->getName(), 'Name does not match.');
	}
	
	function testDelete() {
		$fpread = fopen($this->dir . '/' . $this->name, "w");
		fwrite($fpread, 'Test');
		fclose($fpread);
		$this->assertTrue(file_exists($this->dir . '/' . $this->name));
		
		$file = new File($this->dir, $this->name);
		$this->assertTrue($file->delete());
		
		$this->assertFalse(file_exists($this->dir . '/' . $this->name));
		$this->assertFalse($file->delete());
	}
}
?>