<?
class Pager {
	var $rows;
	var $currentPage;
	var $rowsPerPage;
	
	function Pager($currentPage, $rows, $rowsPerPage = 20) {
		if($currentPage == "" || $currentPage == 0) {
			$currentPage = 1;
		}
		$this->currentPage = $currentPage;
		$this->rows = $rows;
		$this->rowsPerPage = $rowsPerPage;
	}
	
	function countPages() {
		$count = intval( $this->rows /  $this->rowsPerPage );
		if( $this->rows % $this->rowsPerPage ) {
					$count++;
		}
		return $count;
	}
	
	function getCurrentPage() {
		return $this->currentPage;
	}
	
	function getNextPage() {
		if(($this->currentPage+1) > $this->countPages()) {
			return 1;
		} else {
			return $this->currentPage+1;
		}
	}
	
	function getPreviousPage() {
		if(($this->currentPage-1) <= 0) {
			return $this->countPages();
		} else {
			return $this->currentPage-1;
		}
	}
	
	function mysqlLimits() {
		$limits['start'] = ($this->currentPage-1) * $this->rowsPerPage;
		$limits['end'] = ($this->currentPage-1) * $this->rowsPerPage + $this->rowsPerPage;
		return $limits;
	}
}
?>