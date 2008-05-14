<?php

class GeneratorFactory {
	
	private $generators = array();
	private $generatorsXML = null;
	private $xml = null;
	private $dom = null;
	
	/**
	 * Construction with generator xml file
	 */
    function GeneratorFactory($generatorsXML) {
    	$this->generatorsXML = $generatorsXML;
    }
    
    /**
     * @returns an instance of type Generator
     */
    public function getInstance($id = "default") {
    	if($this->generators[$id] == null) {
    		$this->loadImplementation($id);
    	}
    	return $this->generators[$id];
    }
    
    /**
     * Constructs an implementation with arguments from the 
     * connectors xml file.
     */
    private function loadImplementation($id) {
    	if (file_exists($this->generatorsXML) && 
        	$this->xml == null && 
        	$this->dom == null) {
            $this->xml = simplexml_load_file($this->generatorsXML);
        }
        
        $result = $this->xml->xpath("//generator[@id='".$id."']");
        if($result != null) {
            $class = new ReflectionClass(
						(string)$result[0]->attributes()->class);
			$generator = $class->newInstance();
			
			foreach ($result[0]->children() as $att) {
				$generator->setProperty(
								(string)$att->getName(),
								(string)$att);
			}
			
			$id = (string)$result[0]->attributes()->id;
			$this->generators[$id] = $generator;
        } else {
            throw new PiwiException(
				"Could not find generators definition in xml file", 
				PiwiException::ERR_NO_XML_DEFINITION);
        }
    }
}
?>