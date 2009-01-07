<?php
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
	 * @return boolean True if folder has been created and permission has been set, otherwise false.
	 */
	public function create($permissions = 0777) {
		$sucess = mkdir($this->path . "/" . $this->name, $permissions);
		if (!$sucess) {
			return false;
		}
		return chmod($this->path . "/" . $this->name, $permissions);
	}

	/**
	 * Deletes the folder recursivly.
	 * @return boolean True if folder has been deleted otherwise false.
	 */
	public function delete() {
		$path = $this->path . "/" . $this->name;
		if (!is_dir($path)) {
			return false;
		}

		$dir = opendir($path);

		while ($entry = readdir($dir)) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			if (is_dir($path . '/' . $entry)) {
				// Recurse
				$tempFolder = new Folder($path, $entry);
				$tempFolder->delete();
			} else if (is_file($path . '/' . $entry) || is_link($path . '/' . $entry)) {
				$result = unlink($path . '/' . $entry);
				if (!$result) {
					closedir($dir);
					return false;
				}
			} else {
				closedir($dir);
				return false;
			}
		}

		closedir($dir);
		$res = rmdir($path);
		return true;
	}
}