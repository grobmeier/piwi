<?php

class ConnectorFactory {
	
	private $connectors = array();
	private $connectorsXML = null;
	private $xml = null;
	private $dom = null;
	
	/**
	 * Construction with connectors xml file
	 */
    function ConnectorFactory($connectorsXML) {
    	$this->connectorsXML = $connectorsXML;
    }
    
    /**
     * @returns an instance of type Connector
     */
    public function getInstance($id = "default") {
    	if($this->connectors[$id] == null) {
    		$this->loadImplementation($id);
    	}
    	return $this->connectors[$id];
    }
    
    /**
     * Constructs an implementation with arguments from the 
     * connectors xml file.
     */
    private function loadImplementation($id) {
    	if (file_exists($this->connectorsXML) && 
        	$this->xml == null && 
        	$this->dom == null) {
            $this->xml = simplexml_load_file($this->connectorsXML);
        }
        
        $result = $this->xml->xpath("//connector[@id='".$id."']");
        if($result != null) {
            $class = new ReflectionClass(
						(string)$result[0]->attributes()->class);
			$connector = $class->newInstance();
			
			foreach ($result[0]->children() as $att) {
				$connector->setProperty(
								(string)$att->getName(),
								(string)$att);
			}
			
			$id = (string)$result[0]->attributes()->id;
			$this->connectors[$id] = $connector;
        } else {
            throw new PiwiException(
				"Could not find connector definition in xml file.", 
				PiwiException::ERR_NO_XML_DEFINITION);
        }
    }
}
?>