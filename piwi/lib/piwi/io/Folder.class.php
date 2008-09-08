<?
class Folder {
	var $path;
	var $name;
	
	function Folder($_path, $_name) {
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
	 * Creates a new directory with given permission
	 */
	function create($permissions = 0777) {
		mkdir($this->path."/".$this->name, $permissions);
		chmod($this->path."/".$this->name, $permissions);
	}
	
	/**
	 * Deletes a folder recursive 
	 */
	function delete() {
		$path = $this->path."/".$this->name;
	    if (!is_dir ($path)) {
   	        return -1;
	    $dir = opendir ($path);
	        if ($entry == '.' || $entry == '..') {
	        	continue;
	        }
	            $tempFolder = new Folder($path, $entry);
	            $tempFolder->delete();
	        }
	    return 0;
}