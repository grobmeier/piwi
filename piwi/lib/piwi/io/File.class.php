<?
/**
 * Represents a file.
 */
class File {
	/** The path to the file. */
	private $path = null;
	
	/** The name of the file. */
	private $name = null;
	
	/**
	 * Constructor.
	 * @param string $path The path to the file.
	 * @param string $name The name of the file.
	 */
	public function __construct($path, $name) {
		$this->path = $path;
		$this->name = $name;
	}
	
	/**
	 * Returns the path to the file.
	 * @return string The path to the file.
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * Returns the name of the file.
	 * @return string The name of the file.
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Deletes the file.
	 * @return boolean True if file has been deleted successfully otherwise false.
	 */
	public function delete() {
		$path = $this->path . "/" . $this->name;
		if (is_file ($path) || is_link ($path)) {
			return unlink($path);
	    }
	    return false;	}	
}