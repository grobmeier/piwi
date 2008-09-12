<?
class MediaCenter {
	private $pathToImages;
	private $pathToUpload;
	
	public function __construct($pathToImages, $pathToUpload) {
		$this->pathToImages = $pathToImages;
		$this->pathToUpload = $pathToUpload;
	}
	
	public function getFolders() {
		$result = array();
		$verz = opendir($this->pathToImages);
		$i = 0;
		while($folder = readdir($verz)) {  
			if(	!is_file($folder) &&
				substr($folder, 0, 1) != ".") {
				$folder = new Folder($this->pathToImages, $folder);	
				$result[$i] = $folder;
				$i++;
			}
		}
		closedir($verz);
		return $result;
	}
	
	public function keyExists($arr, $intKey) {
		if(array_key_exists($intKey, $arr)) {
 			$intKey = $intKey+1;
 			return $this->keyExists($arr, $intKey);
		} else {
			return $intKey;
		}
	}
	
	public function view($albumName = "") {
		$verz = opendir($this->pathToImages);
		$result = array();
		$i = 0;
		while($folder = readdir($verz)) {  
			$info = @getimagesize($folder);  
			if(	$folder != "." && 
				$folder != ".." && 
				!is_dir($folder) &&
				substr($folder, 0, 1) != ".") {
					// if one wants a specific album
					if($albumName != "" && $albumName != $folder) {
						// ignore this album
					} else {
						$album = new Album($folder);
						$album->setCreatedAt(filectime($this->pathToImages."/".$folder));
						$result[$i] = $this->showImages($this->pathToImages, $folder, $album);
						$i++;
					}
			}
		}
		closedir($verz);
		return $result;
	}
	
	public function showImages($path, $folder, $album) {
		$verz = opendir($path."/".$folder."/thumbs/");
		while($file = readdir($verz)) {  
			$info = @getimagesize($file);  
			if(	$file != "." && 
				$file != ".." && 
				!is_dir($file) &&
				substr($file, 0, 1) != ".") {
					$album->addImage($file);
				}
		}
		closedir($verz);
		return $album;
	}
	
	public function upload() {
		$newfile = $this->pathToUpload . "/" . $_FILES['imgfile']['name'];
		
		if (is_uploaded_file($_FILES['imgfile']['tmp_name'])) {
		   if (!move_uploaded_file($_FILES['imgfile']['tmp_name'], $newfile)) {
		      print "Error Uploading File.";
		      exit();
		   }
		} else {
			echo "File not uploaded- did not come via post.";
		}
		chmod($newfile, 0777); 
		
		$image = new Image($this->pathToUpload."/", $_FILES['imgfile']['name']);
		return $image;
	}	
}
?>