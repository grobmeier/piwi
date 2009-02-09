<?php
DEFINE('PIWI_LIB', 'piwi/lib');

$classloader = null;

function __autoload($class) {
	global $classloader;
	if ($classloader == null) {
    require_once(PIWI_LIB . '/piwi/classloader/ClassLoader.class.php');
		$classloader = new ClassLoader();
	}

	$directorys = array (		
		'test/unit/lib', 
		PIWI_LIB
	);

	foreach ($directorys as $directory) {
		$result = $classloader->loadClass($directory, $class);
		if ($result) {
			return;
		}
	}	
}
?>