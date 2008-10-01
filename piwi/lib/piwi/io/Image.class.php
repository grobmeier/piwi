<?
/**
 * Represents an image.
 */
class Image {
	/** The folder of the image. */
	private $pathToImage = null;
	
	/** The name of the image. */
	private $name = null;
	
	/** The absolutePath of the image. */
	private $absolute = null;
	
	/**
	 * Constructor.
	 * @param string $pathToImages The folder of the image.
	 * @param string $name The name of the image.
	 */
	public function __construct($pathToImages, $name) {
		$this->pathToImages = $pathToImages;
		$this->name = $name;
		$this->absolute = $pathToImages . "/" . $name;
	}
	
	/**
	 * Returns the name of the image.
	 * @return string The name of the image.
	 */
	public function getName() {
		return $this->name;
	}
}
?>