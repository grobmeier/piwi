<?php
class ClassLoader {
	public function loadClass($dir, $class) {
		if ($handle = opendir($dir)) {
		    while (false !== ($file = readdir($handle))) {
		    	if ($file != "." && $file != "..") {
		        	if(is_dir($dir.'/'.$file)) {
		        		if(substr($file, 0, 1) != ".") {
		        			$result = $this->loadClass($dir.'/'.$file,$class);
			        		if($result == true) {
			        			return true;
			        		}
		        		} 
		        	} else {
		            	if($file == $class.'.class.php') {
		            		require_once($dir.'/'.$class.'.class.php');
		            		return true;
		            	} else if($file == $class.'.if.php') {
		            		require_once($dir.'/'.$class.'.if.php');
		            		return true;
		            	}
		        	}
		        }
		    }
		    closedir($handle);
		}
		return false;
	}	
}
?>