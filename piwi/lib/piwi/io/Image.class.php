<?
class Image {
	var $HEIGHT = 0;
	var $WIDTH = 1;
	
	var $pathToImage;
	var $name;
	
	function Image($_pathToImages, $_name) {
		$this->pathToImages = $_pathToImages;
		$this->name = $_name;
		$this->absolute = $_pathToImages."/".$_name;
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
	
	function resize_then_crop($filein,$targetPath, $targetName,
		    $fileout = $targetPath ."/".$targetName;
	
	function chmod($m = 0777) {
		chmod($this->absolute, $m); 
	}
	
		if (!copy($this->absolute, $pathToCopy."/".$this->name)) {
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


