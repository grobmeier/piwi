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

/** since 5.1 php generates a warning if no default timeszone has been set */
if(ini_get('date.timezone') == '') {
	date_default_timezone_set('Europe/Berlin');
}

/**
 * -------------------------------------------------------------------------
 * >>>>>>>>>>>>>>>>>>>>>>>>>>> Error reporting  <<<<<<<<<<<<<<<<<<<<<<<<<<<<
 * -------------------------------------------------------------------------
 */

//error_reporting(0); // hide all errors
//error_reporting(E_ERROR); // show only errors
error_reporting(E_ALL); // show all errors

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
require_once ("lib/zetacomponents/Base/src/base.php");
require_once ('lib/swiftmailer/classes/Swift.php');

spl_autoload_register( array( 'ezcBase', 'autoload' ) );
spl_autoload_register( array( 'ClassLoader', 'autoload' ) );
spl_autoload_register( array('SwiftWrapper', 'autoload') );

//Create the message
$message = Swift_Message::newInstance();
$message->setSubject('My subject');

/* Enable logging */
$logger = Logger :: getLogger('index.php');

function exception_handler($exception) {
	GLOBAL $logger;
	$logger->error('An unhandled runtime exception occurred: ' . $exception->getMessage());
	echo "An unhandled error occurred: " . $exception->getMessage();
}

set_exception_handler('exception_handler');

/**
 * Initialize the BeanFactory
 */
BeanFactory :: initialize($GLOBALS['PIWI_ROOT'] . '/resources/beans/context.xml');
$userContext = BeanFactory :: getBeanById('configurationManager')->getUserContext();
if($userContext != null) {
	BeanFactory :: addContext($userContext['path'], $userContext['overwrite']);
}

/**
 * Configure Logging and Exception Handler
 */
$logConfig = BeanFactory :: getBeanById('configurationManager')->getLoggingConfiguration();
if ($logConfig != null) {
	define('LOG4PHP_CONFIGURATION', $logConfig);
} else {
	define('LOG4PHP_CONFIGURATION', 'resources/logging/default-logging.xml');
}

/**
 * Standard security checks
 */
if (ezcInputForm::hasGetData()) {
	$definition = array(
	    'page' => new ezcInputFormDefinitionElement(
	        ezcInputFormDefinitionElement::REQUIRED, 'string'
	    ),
	    'extension' => new ezcInputFormDefinitionElement(
	        ezcInputFormDefinitionElement::REQUIRED, 'string'
	    )
	);
	$form = new ezcInputForm( INPUT_GET, $definition );

    foreach ( $definition as $name => $dummy ) {
        $propertyName = "property_$name";
        if ($form->hasValidData($name)) {
            $$propertyName = $form->$name;
        } else {
        	$$propertyName = htmlspecialchars( $form->getUnsafeRawData( $name ) );
            echo("A security problem occurred. Script execution stopped because # $name # contained the invalid data: ".$$propertyName);
            $logger->fatal("A security problem occurred. Script execution stopped because parameter # $name # contained the invalid data: ".$$propertyName);
            die();
        }
    }
}

/**
 * -------------------------------------------------------------------------
 * >>>>>>>>>>>>>>>>>>>>>>>>>>>>> Page Processing <<<<<<<<<<<<<<<<<<<<<<<<<<<
 * -------------------------------------------------------------------------
 */
$logger->info('Starting page processing');

session_start();

// Init site
$pipeline = BeanFactory :: getBeanById('pipeline');
$logger->debug('Pipeline initialized successfully');

try {
	$logger->debug('Generate full pipeline');
	$pipeline->generate();
	$logger->debug("Input -> Output succeded");
} catch (Exception $exception) {
	// Show a page displaying the error
	$expage = new ExceptionPageGenerator($exception);
	$pipeline->input('xml');
	$pipeline->getPage()->setContent($expage->generate());
	$pipeline->getPage()->generateContent(true);
	$pipeline->output();
	
	$logger->error('Site generation failed with exception: ' . $exception->getMessage());
}

// Close down all appenders - safely
$logger->debug("Page processing ended - Logger shutdown, end of request.");
Logger :: shutdown();
?>