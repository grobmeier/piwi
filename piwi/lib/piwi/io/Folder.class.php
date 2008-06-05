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
   	        return -1;	    }
	    $dir = opendir ($path);	    	    while ($entry = readdir($dir)) {
	        if ($entry == '.' || $entry == '..') {
	        	continue;
	        }	        if (is_dir ($path.'/'.$entry)) {	            // rufe mich selbst auf
	            $tempFolder = new Folder($path, $entry);
	            $tempFolder->delete();	        } else if (is_file ($path.'/'.$entry) || is_link ($path.'/'.$entry)) {	            $res = unlink ($path.'/'.$entry);	            if (!$res) {	                closedir ($dir); // Verzeichnis schliessen	                return -1; // melde ihn	            }	        } else {	            closedir ($dir); 	            return -1;
	        }	    }	    	    closedir ($dir);	    $res = rmdir ($path);
	    return 0;	}
}