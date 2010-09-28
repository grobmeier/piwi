<?php

class MyProcessor implements Preprocessor {
    public function process() {
        echo "PREPROCESSOR CALLED";
    }
}

class MySecondProcessor implements Preprocessor {
    public function process() {
        echo "SECOND PREPROCESSOR CALLED";
    }
}


class StreamManagerTest extends UnitTestCase {

	function testProperties() {
		$sm = new StreamManager();
		$sm->setStreamConfiguration(dirname(__FILE__) . '/streams.xml');
		$a = $sm->getStreamInfo('image1');
		
		$this->assertEqual('custom/files/gallery/Nature/originals/1.jpg', $a->uri, 'URI does not match.');
		$this->assertEqual('1.jpg', $a->name, 'Name does not match.');
		
		$actions = $sm->getStreamActions('image1');
		
		$this->assertTrue(in_array('MyProcessor', $actions));
		$this->assertTrue(in_array('MySecondProcessor', $actions));
	}
	
}
?>