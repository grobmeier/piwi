<?php
DEFINE('PIWI_LIB', 'piwi/lib');

$classloader = null;

function __autoload($class) {
	global $classloader;
	if ($classloader == null) {
    require_once(PIWI_LIB . '/piwi/classloader/ClassLoader.class.php');
		$classloader = new ClassLoader();
	}

  $result = $classloader->loadClass(PIWI_LIB, $class);
  if ($result == true) {
    return;
  }
}
?>