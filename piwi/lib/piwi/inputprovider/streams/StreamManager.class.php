<?php
/**
 * Manages the settings configured in 'streams.xml'.
 */
class StreamManager {
	/** The simple xml of the 'streams.xml'. */
    private $xml = null;
    
    /** Path of the file containing the configuration. */
    private $streamConfiguration = null;
		
	/** Constructor. */
	public function __construct() {		
	}
	
    /** 
     * Returns the path of an stream for a given ID
     * @return The path of a stream
     */
    public function getStreamInfo($id) {
    	if ($this->xml == null) {
    		$this->_loadConfig();
    	}
    	
    	$streams = $this->xml->xpath("//streams:stream[@id='".$id."']");
    	
    	$uri = $streams[0]->attributes()->uri;
    	$name = $streams[0]->attributes()->name;
    	if($uri !== '' && $name != '') {
    		return $streams[0]->attributes();
    	}
    	throw new PiwiException(
				"Error while processing stream definition file.", 
				PiwiException :: INVALID_XML_DEFINITION);
    } 
    
    /**
     * Sets the path of the file containing the configuration
     * @param string $configFilePath Path of the file containing the configuration.
     */
    public function setStreamConfiguration($streamConfiguration) {
		$this->streamConfiguration = $streamConfiguration;
    }

    /**
     * Loads the 'config.xml'.
     */
    private function _loadConfig() {
    	if (!file_exists($this->streamConfiguration)) {
			throw new PiwiException(
				"Could not find the stream definition file with path: ".$this->streamConfiguration, 
				PiwiException :: ERR_NO_XML_DEFINITION);
    	}
    	
    	$this->xml = simplexml_load_file($this->streamConfiguration);
		$this->xml->registerXPathNamespace('streams', 'http://piwi.googlecode.com/xsd/streams');
    }
}
?>