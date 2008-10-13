<?php
/**
 * Generates a picture gallery.
 * The pictures from the each subfolder of a given folder will be presented as a gallery.
 */
class SimpleGalleryGenerator implements SectionGenerator {
	/** The folder whose subfolders contain the albums. */
	private $pathToAlbums = null;
	
	/** The folder where uploaded files will be placed. */
	private $pathToUpload = null; 
    
    /**
     * Constructor.
     */
    public function __construct() {
    }
   
	/**
	 * Generates the sections that will be placed as content.
	 * @return string The xml output as string.
	 */
    public function generate() {
		// Retrieve the all albums or one specific album if attribute is set
		$mediaCenter = new MediaCenter($this->pathToAlbums, $this->pathToUpload);
		if (isset($_REQUEST['album'])) {
			$albums = $mediaCenter->getAlbums($_GET['album']);
		} else {
			$albums = $mediaCenter->getAlbums();
		}

		// Generate the xml output
		$piwixml = '<div>';


		foreach($albums as $album) {
			$folder = $album->getName();
			$images = $album->getImages();	
	
			$count = 0; 
			$maxImage = 5; // maximum number of pictures per album
			
			$piwixml .= "<section>";
			$piwixml .= "<title>";
			$piwixml .= $folder;
			$piwixml .= "</title>";
			
			// if no album is selected show 5 images of each otherwise show the full album
			$albumSelected = isset($_GET['album']);
			
			if ($albumSelected && $_GET['album'] != $folder){
				// if album is selected show only images of this album
				continue;
			}
				
			foreach($images as $file) {				
				if (!$albumSelected && $count == $maxImage) {
					// stop after the specified number of pictures
					break;
				}
				$piwixml .= '<a href="'.$this->pathToAlbums.'/'.$folder.'/originals/'.$file.'">
						<img src="'.$this->pathToAlbums.'/'.$folder.'/thumbs/'.$file.'" alt="' . $file . '" />
						</a>';
				$count++;
			}
			
			if ($albumSelected){
				$piwixml .= '<p><a href="./' . Site::getPageId() . '.' . Site::getExtension() . '">Get me back to galleries</a></p>';
			} else {
				$piwixml .= '<p><a href="./' . Site::getPageId() . '.' . Site::getExtension() . '?album='.urlencode($folder).'">More in '.$folder.'</a></p>';
			}
			$piwixml .= '</section>';
		}
		$piwixml .= '</div>';
		
		return $piwixml;
	}
	
	/** Used to pass parameters to the Generator.
	 * @param string $key The name of the parameter.
	 * @param object $value The value of the parameter.
	 */
    public function setProperty($key, $value) {
    	if($key == "pathtoalbums") {
    		$this->pathToAlbums = $value;
    	} else if($key == "pathtoupload") {
    		$this->pathToUpload = $value;
    	}
    }
}
?>