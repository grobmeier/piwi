<?
/**
 * Used to handle Albums.
 */
class MediaCenter {
	/** The folder whose subfolders contain the albums. */
	private $pathToAlbums = null;
	
	/** The folder where uploaded files will be placed. */
	private $pathToUpload = null; 
	
	/** Constructor.
	 * @param string $pathToAlbums The folder whose subfolders contain the albums.
	 * @param string $pathToUpload The folder where uploaded files will be placed.
	 */
	public function __construct($pathToAlbums, $pathToUpload) {
		$this->pathToAlbums = $pathToAlbums;
		$this->pathToUpload = $pathToUpload;
	}
	
	/**
	 * Returns the albums sorted by date.
	 * @param string $albumName If only one specific album should be displayed specify its name, otherwise all albums will be returned.
	 * @return array The albums sorted by date as an array of Albums.
	 */
	public function getAlbums($albumName = null) {
		$albumFolder = opendir($this->pathToAlbums);
		$albums = array();
		$count = 0;
		while($folder = readdir($albumFolder)) {  
			if(	$folder != "." && 
				$folder != ".." && 
				!is_dir($folder) &&
				substr($folder, 0, 1) != ".") {
					// if one wants a specific album ignore all other albums					
					if ($albumName == null || $albumName == $folder) {
						$album = new Album($folder);
						$album->setCreatedAt(filectime($this->pathToAlbums . "/" . $folder));
						$albums[$album->getCreatedAt() . ($count++)] = $this->addImagesToAlbum($album, $this->pathToAlbums . "/" . $folder . "/thumbs/");
					}
			}
		}
		closedir($albumFolder);
		
		// Sort albums by date
		krsort($albums);		
		
		return $albums;
	}
	
	/**
	 * Retrieves all images of the given folder and adds them to the album.
	 * @param Album $album The album to which the images should be added.
	 * @param string $folder The folder whose images should be added to the album.
	 */
	private function addImagesToAlbum(Album $album, $folder) {		
		$imageFolder = opendir($folder);
		while ($file = readdir($imageFolder)) {  
			if(	$file != "." && 
				$file != ".." && 
				!is_dir($file) &&
				substr($file, 0, 1) != ".") {
					$album->addImage($file);
				}
		}
		closedir($imageFolder);
		return $album;
	}
}
?>