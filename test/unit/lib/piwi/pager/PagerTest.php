<?php
class PagerTest extends UnitTestCase {

	private $currentPage = 0;
	private $rows = 30;
	private $rowsPerPage = 20;
	
	private $pager = null;
	
	function before($message) {
		$this->pager = new Pager($this->currentPage, $this->rows, $this->rowsPerPage);
	}
	
	function testGetCurrentPage() {
		$this->assertEqual(1, $this->pager->getCurrentPage(), 'Page does not match.');
	}
	
	function testGetPreviousPage() {
		$this->assertEqual(2, $this->pager->getPreviousPage(), 'Page does not match.');
		$this->assertEqual(1, $this->pager->getPreviousPage(), 'Page does not match.');
		$this->assertEqual(2, $this->pager->getPreviousPage(), 'Page does not match.');
		$this->assertEqual(1, $this->pager->getPreviousPage(), 'Page does not match.');
		
		$this->assertEqual(1, $this->pager->getCurrentPage(), 'Page does not match.');
	}
	
	function testGetNextPage() {
		$this->assertEqual(2, $this->pager->getNextPage(), 'Page does not match.');
		$this->assertEqual(1, $this->pager->getNextPage(), 'Page does not match.');
		$this->assertEqual(2, $this->pager->getNextPage(), 'Page does not match.');
		$this->assertEqual(1, $this->pager->getNextPage(), 'Page does not match.');
		
		$this->assertEqual(1, $this->pager->getCurrentPage(), 'Page does not match.');
	}
	
	function testLimits() {
		$limits = $this->pager->getLimits();
		
		$this->assertEqual(0, $limits['start'], 'Limit does not match.');
		$this->assertEqual(20, $limits['end'], 'Limit does not match.');
		
		$this->pager->getNextPage();
		$limits = $this->pager->getLimits();
		
		$this->assertEqual(20, $limits['start'], 'Limit does not match.');
		$this->assertEqual(40, $limits['end'], 'Limit does not match.');
	}
}
?>