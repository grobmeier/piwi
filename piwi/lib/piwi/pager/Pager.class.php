<?php
/**
 * Helper class to page contents of a database query.
 */
class Pager {
	/** The number of the current page. */
	private $currentPage;	
	
	/** The total number of rows. */
	private $rows;
	
	/** The number of rows per page. */
	private $rowsPerPage;
	
	/**
	 * Constructor.
	 * @param integer $currentPage The number of the current page.
	 * @param integer $rows The total number of rows.
	 * @param integer $rowsPerPage The number of rows per page.
	 * 
	 */
	public function __construct($currentPage, $rows, $rowsPerPage = 20) {
		if ($currentPage == "" || $currentPage == 0) {
			$currentPage = 1;
		}
		$this->currentPage = $currentPage;
		$this->rows = $rows;
		$this->rowsPerPage = $rowsPerPage;
	}
	
	/**
	 * Returns the total number of pages.
	 * @return integer The total number of pages.
	 */
	public function countPages() {
		$count = intval($this->rows / $this->rowsPerPage);

		if ($this->rows % $this->rowsPerPage > 0) {
			$count++;
		}
		return $count;
	}
	
	/**
	 * Returns the number of the current page.
	 * @return integer The number of the current page.
	 */	
	public function getCurrentPage() {
		return $this->currentPage;
	}
	
	/**
	 * Returns the number of the next page.
	 * @return integer The number of the next page.
	 */	
	public function getNextPage() {
		if (($this->currentPage + 1) > $this->countPages()) {
			$this->currentPage = 1;
			return $this->currentPage;
		} else {
			return ++$this->currentPage;
		}
	}

	/**
	 * Returns the number of the previous page.
	 * @return integer The number of the previous page.
	 */		
	public function getPreviousPage() {
		if (($this->currentPage - 1) <= 0) {
			$this->currentPage = $this->countPages();
			return $this->currentPage;
		} else {
			return --$this->currentPage;
		}
	}
	
	/**
	 * Returns an array containing the limits which can be used for querying a database.
	 * @return array An array containing the limits.
	 */	
	public function getLimits() {
		$limits['start'] = ($this->currentPage - 1) * $this->rowsPerPage;
		$limits['end'] = $this->currentPage * $this->rowsPerPage;
		return $limits;
	}
}
?>