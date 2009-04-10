<?php
class FolderTest extends UnitTestCase {

	private $dir = null;
	private	$name = 'test';
	private	$name2 = 'test2';
	private $fileName = 'test.txt';
	
	function setUp() {
		$this->dir = dirname(__FILE__);
	}
	
	function testProperties() {
		$folder = new Folder($this->dir, $this->name);
		
		$this->assertEqual($this->dir, $folder->getPath(), 'Path does not match.');
		$this->assertEqual($this->name, $folder->getName(), 'Name does not match.');
	}
	
	function testCreate() {
		$folder = new Folder($this->dir, $this->name);
		$this->assertTrue($folder->create());
		
		$this->assertTrue(is_dir($this->dir . '/' . $this->name));
		
		rmdir($this->dir . '/' . $this->name);
	}
	
	function testDelete() {
		mkdir($this->dir . '/' . $this->name);
		mkdir($this->dir . '/' . $this->name . '/' . $this->name2);
		
		$fpread = fopen($this->dir . '/' . $this->name . '/' . $this->fileName, "w");
		fwrite($fpread, 'Test');
		fclose($fpread);
		
		$this->assertTrue(file_exists($this->dir . '/' . $this->name));
		$this->assertTrue(file_exists($this->dir . '/' . $this->name . '/' . $this->name2));
		$this->assertTrue(file_exists($this->dir . '/' . $this->name . '/' . $this->fileName));		
		
		$folder = new Folder($this->dir, $this->name);
		$this->assertTrue($folder->delete());
		
		$this->assertFalse($folder->delete());
	}
}
?>