<?php

class ConnectorFactory {
	
	private $arr = array();
	private $connectors = null;
	private $xml = null;
	private $dom = null;
	
	/**
	 * Construction with connectors xml file
	 */
    function ConnectorFactory($connectors) {
    	$this->connectors = $connectors;
    }
    
    /**
     * @returns an instance of type Connector
     */
    public function getInstance($id = "default") {
    	if($this->arr[$id] == null) {
    		$this->loadImplementation($id);
    	}
    	return $this->arr[$id];
    }
    
    /**
     * Constructs an implementation with arguments from the 
     * connectors xml file.
     */
    private function loadImplementation($id) {
        if (file_exists($this->connectors) && 
        	$this->xml == null && 
        	$this->dom == null) {
            $this->xml = simplexml_load_file($this->connectors);
        }
        
        $result = $this->xml->xpath("//connector[@id='".$id."']");
        if($result != null) {
        	// TODO
            
            echo ($result[0]->attributes()->id);
            echo "<br>";
            echo ($result[0]->children()->file);
            
        } else {
            throw new PiwiException(
				"Could not find connector definition in xml file", 
				PiwiException::ERR_NO_XML_DEFINITION);
        }
    }
}
?>