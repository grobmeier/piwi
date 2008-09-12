<?
/**
 * Represents a folder.
 */
class Folder {
	/** The path to the folder. */
	private $path = null;
	
	/** The name of the folder. */
	private $name = null;
	
	/**
	 * Constructor.
	 * @param string $path The path to the folder.
	 * @param string $name The name of the folder.
	 */
	public function __construct($path, $name) {
		$this->path = $path;
		$this->name = $name;
	}
	
	/**
	 * Returns the path to the folder.
	 * @return string The path to the folder.
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * Returns the name of the folder.
	 * @return string The name of the folder.
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Creates the folder with the given permission.
	 * @param integer $permissions The permissions for the created folder.
	 */
	public function create($permissions = 0777) {
		mkdir($this->path."/".$this->name, $permissions);
		chmod($this->path."/".$this->name, $permissions);
	}
	
	/**
	 * Deletes the folder recursivly.
	 * @return integer 0 if folder has been deleted or -1 if folder does not exist.
	 */
	public function delete() {
		$path = $this->path."/".$this->name;
	    if (!is_dir($path)) {
   	        return -1;	    }
	    $dir = opendir ($path);	    	    while ($entry = readdir($dir)) {
	        if ($entry == '.' || $entry == '..') {
	        	continue;
	        }	        if (is_dir ($path.'/'.$entry)) {	            // Recurse
	            $tempFolder = new Folder($path, $entry);
	            $tempFolder->delete();	        } else if (is_file ($path.'/'.$entry) || is_link ($path.'/'.$entry)) {	            $result = unlink ($path.'/'.$entry);	            if (!$result) {	                closedir ($dir);	                return -1;	            }	        } else {	            closedir ($dir); 	            return -1;
	        }	    }	    	    closedir ($dir);	    $res = rmdir ($path);
	    return 0;	}
}