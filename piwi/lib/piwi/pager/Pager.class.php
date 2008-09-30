<?
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
		if($currentPage == "" || $currentPage == 0) {
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
		if ($this->rows % $this->rowsPerPage) {
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
		if(($this->currentPage + 1) > $this->countPages()) {
			return 1;
		} else {
			return $this->currentPage + 1;
		}
	}

	/**
	 * Returns the number of the previous page.
	 * @return integer The number of the previous page.
	 */		
	public function getPreviousPage() {
		if(($this->currentPage - 1) <= 0) {
			return $this->countPages();
		} else {
			return $this->currentPage - 1;
		}
	}
	
	/**
	 * Returns an array containing the limits which can be used for querying a database.
	 * @return array An array containing the limits.
	 */	
	public function getLimits() {
		$limits['start'] = ($this->currentPage - 1) * $this->rowsPerPage;
		$limits['end'] = ($this->currentPage - 1) * $this->rowsPerPage + $this->rowsPerPage;
		return $limits;
	}
}
?>