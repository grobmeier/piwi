<?php
require_once("./lib/piwi/media/MediaCenter.class.php");
require_once("./lib/piwi/media/Album.class.php");
require_once("./lib/piwi/io/Image.class.php");
require_once("./lib/piwi/io/Folder.class.php");
require_once("./lib/piwi/io/File.class.php");

class GalleryGenerator implements Generator {
	
	var $pathtomedia = "";
	var $pathtoupload = ""; 
    	
    function GalleryGenerator() {
    }
   
    function generate($output = "html") {

		$plugin = new MediaCenter($this->pathtomedia, $this->pathtoupload);
		$albums = $plugin->view($_GET['album']);

		$result = $albums;
		$new = array();
		foreach($result as $r) {
			$new[$r->getCreatedAt()] = $r;
		}
		krsort($new);

		$piwixml = "<?xml version='1.0'?>";
		$piwixml .= "<!DOCTYPE document PUBLIC \"-//PIWI//DTD Documentation V1.0//EN\" \"dtd/document-v10.dtd\">";
		$piwixml .= "<document>";

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
			
			if($_GET['album'] != "") {
				$piwixml .= "<p><a href=\"./index.php?p=gallery\">Get me back to galleries!</a></p>";
			}
			
			$piwixml .= "<p>";
			$piwixml .= "<table>";
			foreach($images as $file) {
				if($count == 0) {
					$piwixml .= "<tr>";
				}
				
				$piwixml .= "<td>
								<a href=\"".$this->pathtomedia."/".$folder."/originals/".$file."\">
								<image path=\"".$this->pathtomedia."/".$folder."/thumbs/".$file."\">$file</image>
								</a>
							</td>";
					
				if($count == $cols) {
					$piwixml .= "</tr>";
					$count = 0;
				} else {
					$count++;
				}
		
				if($_GET['album'] == "") {
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
					$piwixml .= "<td>&nbsp;</td>";
					$count++;
				}	
				$piwixml .= "</tr>";
			}
	
			$t = $cols+1;
			if($moreMessage != 0) {
				$t = $cols+1;
			$piwixml .= "<tr><td colspan=\"".$t."\">
							<a href=\"./index.php?p=gallery&amp;album=".urlencode($folder)."\">More in ".$folder."</a>
							</td>
						</tr>";
			}
			
			if($_GET['album'] != "") {
				$piwixml .= "<tr><td colspan=\"".$t."\"><br/><a href=\"./index.php?p=gallery\">Get me back to galleries!</a></td></tr>";
			}

			$piwixml .= "</table>";
			$piwixml .= "</p>";
			$piwixml .= "</section>";
		}
		$piwixml .= "</document>";
		
		if($output == "html") {
			return XMLPage::transformPart(
						"resources/xslt/document-v1.0.xsl", 
						$piwixml);
		} else if ($output == "xml") {
			return $piwixml;
		}
	}
	
    function setProperty($key,$value) {
    	if($key == "pathtomedia") {
    		$this->pathtomedia = $value;
    	} else if($key == "pathtoupload") {
    		$this->pathtoupload = $value;
    	}
    }
}
?>