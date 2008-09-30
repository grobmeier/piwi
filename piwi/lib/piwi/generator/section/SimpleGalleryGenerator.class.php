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
	
			$cols = 4;
			$count = 0;
			$countMaxImg = 0;
			$maxImage = 4;
			$moreMessage = 0;
	
			$piwixml .= "<section>";
			$piwixml .= "<title>";
			$piwixml .= $folder;
			$piwixml .= "</title>";
			
			if(isset($_GET['album'])) {
				$piwixml .= '<p><a href="./gallery.html">Get me back to galleries!</a></p>';
			}
			
			$piwixml .= "<table>";
			foreach($images as $file) {
				if($count == 0) {
					$piwixml .= "<tr>";
				}
				
				$piwixml .= '<td>
								<a href="'.$this->pathToAlbums.'/'.$folder.'/originals/'.$file.'">
								<img src="'.$this->pathToAlbums.'/'.$folder.'/thumbs/'.$file.'" />
								</a>
							</td>';
					
				if($count == $cols) {
					$piwixml .= "</tr>";
					$count = 0;
				} else {
					$count++;
				}
		
				if(!isset($_GET['album'])) {
					if($countMaxImg == $maxImage) {
						$countMaxImg = 0;
						$moreMessage = 1;
						break;
					} else {
						$countMaxImg++;
						$moreMessage = 0;
					}
				}
			}	
	
			if($count != 0) {
				while($count <= $cols) {
					$piwixml .= "<td></td>";
					$count++;
				}	
				$piwixml .= "</tr>";
			}
	
			$t = $cols+1;
			if($moreMessage != 0) {
				$t = $cols+1;
			$piwixml .= '<tr><td colspan="'.$t.'">
							<a href="./gallery.html?album='.urlencode($folder).'">More in '.$folder.'</a>
							</td>
						</tr>';
			}
			
			if(isset($_GET['album'])) {
				$piwixml .= '<tr><td colspan="'.$t.'"><br/><a href="./gallery.html">Get me back to galleries!</a></td></tr>';
			}

			$piwixml .= '</table>';
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