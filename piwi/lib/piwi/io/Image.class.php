<?
/**
 * Represents a folder.
 */
class Image {
	private $HEIGHT = 0;
	private $WIDTH = 1;
	
	private $pathToImage = null;
	private $name = null;
	private $absolute = null;
	
	function __construct($pathToImages, $name) {
		$this->pathToImages = $pathToImages;
		$this->name = $name;
		$this->absolute = $pathToImages."/".$name;
	}
	
	function getDimensions($maxWidth, $maxHeight) {
		list($width, $height) = getimagesize($this->absolute);
		if($width > $maxWidth) {
			$factor = $width / $maxWidth;
			$result[0] = $width / $factor;
			$result[1] = $height / $factor;
		} else if ($height > $maxHeight) {
			$factor = $height / $maxWidth;
			$result[0] = $width / $factor;
			$result[1] = $height / $factor;
		} else {
			$result[0] = $width;
			$result[1] = $height;
		}
		return $result;
	}
	
	// $thumb = $image->resizeTo(75, "../upload/thumbs");
	function resizeTo($targetPath, $width, $height = "0") {
		if($height == "0") {
			$height = $width;
		}
		return $this->resize_then_crop($this->absolute,  $targetPath, $this->name, $width, $height, "255","255","255");
	}
	
	function resize_then_crop($filein,$targetPath, $targetName,	$imagethumbsize_w,$imagethumbsize_h,$red,$green,$blue) {
		    $fileout = $targetPath ."/".$targetName;	    // Get new dimensions	    list($width, $height) = getimagesize($filein);	    $new_width = $width * $percent;	    $new_height = $height * $percent;	    	    if (preg_match("/.jpg/i", "$filein")) {	        $format = 'image/jpeg';	    }	    if (preg_match("/.gif/i", "$filein")) {	        $format = 'image/gif';	    }	    if (preg_match("/.png/i", "$filein")) {	        $format = 'image/png';	    }	    	    switch ($format) {	    case 'image/jpeg':	        $image = imagecreatefromjpeg($filein);	        break;	    case 'image/gif';	        $image = imagecreatefromgif ($filein);	        break;	    case 'image/png':	        $image = imagecreatefrompng($filein);	        break;	    }	    	    $width = $imagethumbsize_w ;	    $height = $imagethumbsize_h ;	    list($width_orig, $height_orig) = getimagesize($filein);	    	    if ($width_orig < $height_orig) {	        $height = ($imagethumbsize_w / $width_orig) * $height_orig;	    } else {	        $width = ($imagethumbsize_h / $height_orig) * $width_orig;	    }	    	    if($width < $imagethumbsize_w)	    //if the width is smaller than supplied thumbnail size	    {	        $width = $imagethumbsize_w;	        $height = ($imagethumbsize_w/ $width_orig) * $height_orig;	    }	    	    if($height < $imagethumbsize_h)	    //if the height is smaller than supplied thumbnail size	    {	        $height = $imagethumbsize_h;	        $width = ($imagethumbsize_h / $height_orig) * $width_orig;	    }	    	    $thumb = imagecreatetruecolor($width , $height);	    $bgcolor = imagecolorallocate($thumb, $red, $green, $blue);	    ImageFilledRectangle($thumb, 0, 0, $width, $height, $bgcolor);	    imagealphablending($thumb, true);	    	    imagecopyresampled($thumb, $image, 0, 0, 0, 0,	    $width, $height, $width_orig, $height_orig);	    $thumb2 = imagecreatetruecolor($imagethumbsize_w , $imagethumbsize_h);	    // true color for best quality	    $bgcolor = imagecolorallocate($thumb2, $red, $green, $blue);	    ImageFilledRectangle($thumb2, 0, 0,	    $imagethumbsize_w , $imagethumbsize_h , $white);	    imagealphablending($thumb2, true);	    	    $w1 =($width/2) - ($imagethumbsize_w/2);	    $h1 = ($height/2) - ($imagethumbsize_h/2);	    	    imagecopyresampled($thumb2, $thumb, 0,0, $w1, $h1,	    $imagethumbsize_w , $imagethumbsize_h ,$imagethumbsize_w, $imagethumbsize_h);	    	    // Output	    //header('Content-type: image/gif');	    //imagegif($thumb); //output to browser first image when testing	    	    if ($fileout !="") {	        imagegif($thumb2, $fileout);	    }	    //write to file	    $result = new Image($targetPath, $targetName);	    $result->chmod(0777);	    return $result;	}
	
	function chmod($m = 0777) {
		chmod($this->absolute, $m); 
	}
		function copyTo($pathToCopy) {
		if (!copy($this->absolute, $pathToCopy."/".$this->name)) {    		echo("failed to copy file...<br>\n");		}
	}
	
	function delete() {
		// TODO
	}
	
	/**
	 * @author Sandy Lewanscheck
	 */	
	function image_type_to_mime_type($imagetype) {
       $image_type_to_mime_type = array(
           1  => 'image/gif',                    // IMAGETYPE_GIF
           2  => 'image/jpeg',                    // IMAGETYPE_JPEG
           3  => 'image/png',                    // IMAGETYPE_PNG
           4  => 'application/x-shockwave-flash', // IMAGETYPE_SWF
           5  => 'image/psd',                    // IMAGETYPE_PSD
           6  => 'image/bmp',                    // IMAGETYPE_BMP
           7  => 'image/tiff',                    // IMAGETYPE_TIFF_II (intel byte order)
           8  => 'image/tiff',                    // IMAGETYPE_TIFF_MM (motorola byte order)
           9  => 'application/octet-stream',      // IMAGETYPE_JPC
           10 => 'image/jp2',                    // IMAGETYPE_JP2
           11 => 'application/octet-stream',      // IMAGETYPE_JPX
           12 => 'application/octet-stream',      // IMAGETYPE_JB2
           13 => 'application/x-shockwave-flash', // IMAGETYPE_SWC
           14 => 'image/iff',                    // IMAGETYPE_IFF
           15 => 'image/vnd.wap.wbmp',            // IMAGETYPE_WBMP
           16 => 'image/xbm');                    // IMAGETYPE_XBM
       return (isset($image_type_to_mime_type[$imagetype]) ? $image_type_to_mime_type[$imagetype] : false);
   }

	function getName() {
		return $this->name;
	} 
}
?>



