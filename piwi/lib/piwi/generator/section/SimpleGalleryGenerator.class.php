<?php

class SimpleGalleryGenerator implements SectionGenerator {
	
	private $pathtomedia = "";
	private $pathtoupload = ""; 
    	
    public function __construct() {
    }
   
    public function generate() {

		$plugin = new MediaCenter($this->pathtomedia, $this->pathtoupload);
		$albums = $plugin->view($_GET['album']);

		$result = $albums;
		$new = array();
		foreach($result as $r) {
			$new[$r->getCreatedAt()] = $r;
		}
		ksort($new);

		$piwixml = '<?xml version="1.0"?>';
		$piwixml .= '<div>';

		foreach($new as $album) {
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
								<a href="'.$this->pathtomedia.'/'.$folder.'/originals/'.$file.'">
								<img src="'.$this->pathtomedia.'/'.$folder.'/thumbs/'.$file.'" />
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
    	if($key == "pathtomedia") {
    		$this->pathtomedia = $value;
    	} else if($key == "pathtoupload") {
    		$this->pathtoupload = $value;
    	}
    }
}
?>