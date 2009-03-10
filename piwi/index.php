<?php
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
 * >>>>>>>>>>>>>>>>>>>>>>>>>>> Error reporting  <<<<<<<<<<<<<<<<<<<<<<<<<<<<
 * -------------------------------------------------------------------------
 */

error_reporting(0); // hidde all errors
//error_reporting(E_ERROR); // show only errors
//error_reporting(E_ALL); // show all errors

/**
 * -------------------------------------------------------------------------
 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>> Directories  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
 * -------------------------------------------------------------------------
 */
 
// ATTENTION: Changing the directory structure is not recommended.
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
 
/** The root path of PIWI */
$GLOBALS['PIWI_ROOT'] = dirname(__FILE__) . '/';
 
/** ClassLoader which makes other includes dispensable. */
require_once ("lib/piwi/classloader/ClassLoader.class.php");

/** Instance of the Classloader. */
$classloader = null;

/**
 * Autoload uses the ClassLoader class tu recursivly look up
 * needed classes. Use one file per class/interface and name it: 
 * 	- yourclass.class.php
 * 	- yourinterface.if.php
 * @param string $class Name of the of the class or interface.
 */
function __autoload($class) {
	global $classloader;
	if ($classloader == null) {
		$classloader = new ClassLoader($GLOBALS['PIWI_ROOT'] . 'cache/classloader.cache.xml');
	}

	$directorys = array ('lib', CUSTOM_CLASSES_PATH);

	foreach ($directorys as $directory) {
		$result = $classloader->loadClass($directory, $class);
		if ($result == true) {
			return;
		}
	}
}

/**
 * Initialize the singleton factories for managing Generators and Connectors
 */
GeneratorFactory::initialize($GLOBALS['PIWI_ROOT'] . CONTENT_PATH . '/generators.xml');
ConnectorFactory::initialize($GLOBALS['PIWI_ROOT'] . CONTENT_PATH . '/connectors.xml');
FormFactory::initialize($GLOBALS['PIWI_ROOT'] . CONTENT_PATH . '/forms.xml');
ConfigurationManager::initialize($GLOBALS['PIWI_ROOT'] . CONTENT_PATH . '/config.xml');

/**
 * Configure Logging and Exception Handler
 */
$logConfig = ConfigurationManager::getInstance()->getLoggingConfiguration();
if($logConfig != null) {
	define('LOG4PHP_CONFIGURATION', $logConfig);	
} else {
	define('LOG4PHP_CONFIGURATION', 'resources/logging/default-logging.xml');
}

$logger =& LoggerManager::getLogger('Index.php');

function exception_handler($exception) {
	GLOBAL $logger;
  	$logger->error('A uncatched runtime exception occured: '.$exception->getMessage());
  	echo "An uncatched error occurd: ".$exception->getMessage();
}

set_exception_handler('exception_handler');

/**
 * -------------------------------------------------------------------------
 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>> Page Processing <<<<<<<<<<<<<<<<<<<<<<<<<<<
 * -------------------------------------------------------------------------
 */ 
$logger->info('Starting page processing');

session_start();

// Init site (manual dependency injection)
Site::setInstance(new XMLSite($GLOBALS['PIWI_ROOT'] . CONTENT_PATH, 
	$GLOBALS['PIWI_ROOT'] . TEMPLATES_PATH, 'site.xml'));
$logger->debug('XML Site initialized successfully');

try {
	// Generate page
	$logger->debug('Site generating content');
	Site::getInstance()->generateContent();
} catch(Exception $exception) {
	// Show a page displaying the error
	$exceptionPageGenerator = new ExceptionPageGenerator($exception);
	Site::getInstance()->setContent($exceptionPageGenerator->generate());
	$logger->error('Site generation failed with exception: '.$exception->getMessage());
}

// Call Serializer
$logger->debug("Beginning serialization");
Site::getInstance()->serialize();

// Close down all appenders - safely
$logger->debug("Page processing ended - Logger shutdown, end of request.");
LoggerManager::shutdown();
?>