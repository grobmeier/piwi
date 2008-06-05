<?
class File {
	var $path;
	var $name;
	
	function File($_path, $_name) {
		$this->path = $_path;
		$this->name = $_name;
	}
	
	function getPath() {
		return $this->path;
	}
	
	function getName() {
		return $this->name;
	}
	
	
	/**
	 * Deletes a folder recursive 
	 */
	function delete() {
		$path = $this->path."/".$this->name;
	 //  	if (is_file ($path) || is_link ($path)) {
			$res = unlink ($path);			if (!$res) {	        	closedir ($dir); // Verzeichnis schliessen	            return -1; // melde ihn
	  //      }
	    }	}	
}