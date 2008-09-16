<?
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

/**
 * -------------------------------------------------------------------------
 * >>>>>>>>>>>>>>>>>>>>>>>>>> Custom Configuration <<<<<<<<<<<<<<<<<<<<<<<<<
 * -------------------------------------------------------------------------
 */
 
// Error reporting
error_reporting(0); // hidde all errors
//error_reporting(E_ALL); // show all errors

/** Name of the folder where your content is placed. */
DEFINE('CONTENT_PATH', 'custom/content');

/** Name of the folder where your templates are placed. */
DEFINE('TEMPLATES_PATH', 'custom/templates');

/** Name of the folder where your custom classes are placed. */
DEFINE('CUSTOM_CLASSES_PATH', 'custom/lib/piwi');

/**
 * -------------------------------------------------------------------------
 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Class Loading <<<<<<<<<<<<<<<<<<<<<<<<<<<<
 * -------------------------------------------------------------------------
 */ 
 
/** ClassLoader which makes other includes dispensable. */
require ("lib/piwi/classloader/ClassLoader.class.php");

/** Instance of the Classloader. */
$classloader = null;

/**
 * Autoload uses the ClassLoader class tu recursivly look up
 * needed classes. Use one file per class/interface and name it: 
 * 	- yourclass.class.php
 * 	- yourinterface.if.php
 * Currently, only custom classes should be lookuped. When the classloader
 * cache has been implemented, the lookup of coreclasses must be evaluated.
 */
function __autoload($class) {
	global $classloader;
	if ($classloader == null) {
		$classloader = new ClassLoader(dirname(__FILE__) . '/cache/classloader.cache.xml');
	}

	$directorys = array (		
		'lib/piwi', 
		CUSTOM_CLASSES_PATH
	);

	foreach ($directorys as $directory) {
		$result = $classloader->loadClass($directory, $class);
		if ($result == true) {
			return;
		}
	}
}

/**
 * -------------------------------------------------------------------------
 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>> Page Processing <<<<<<<<<<<<<<<<<<<<<<<<<<<
 * -------------------------------------------------------------------------
 */ 

// Initialize the singleton factories for managing Generators and Connectors
GeneratorFactory::initialize(CONTENT_PATH . '/generators.xml');
ConnectorFactory::initialize(CONTENT_PATH . '/connectors.xml');

// Determinate the requested page
$pageId = "default";
if (isset($_REQUEST['page'])) {
	$pageId = $_REQUEST['page'];
}

$site = new XMLSite($pageId, CONTENT_PATH, 'site.xml');
	
try {
	// Generate page
	$site->readContent();
	$CONTENT = $site->transform();
} catch( Exception $exception ) {
	// Show a page displaying the error
	$exceptionPageGenerator = new ExceptionPageGenerator($exception);
	$site->setContent($exceptionPageGenerator->generate());
	$CONTENT = $site->transform();
}		

// Generate navigation
$HTML_NAVIGATION = $site->generateNavigation();			

// Show generated page
include (TEMPLATES_PATH . '/' . $site->getTemplate());
?>