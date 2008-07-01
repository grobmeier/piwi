<?
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
// TODO: use of namespaces, when namespaces are not longer experimental
// http://www.php.net/manual/de/language.namespaces.definition.php

// ClassLoader
include("lib/piwi/classloader/ClassLoader.class.php");

// Default page serializer
include("lib/piwi/XMLPage.class.php");
include("lib/piwi/Site.class.php");

// Default Exception
include("lib/piwi/PiwiException.class.php");

// Connectors classes - replace with autoload
include("lib/piwi/connector/ConnectorFactory.class.php");
include("lib/piwi/connector/Connector.if.php");
include("lib/piwi/connector/SQLiteConnector.class.php");
include("lib/piwi/connector/MySQLConnector.class.php");

// Generator classes - replace with autoload
include("lib/piwi/generator/GeneratorFactory.class.php");
include("lib/piwi/generator/Generator.if.php");
include("lib/piwi/generator/GalleryGenerator.class.php");

// Navigation classes - replace with autoload
include("lib/piwi/navigation/Navigation.if.php");
include("lib/piwi/navigation/SimpleTextNavigation.class.php");

// *** Configuration
// Instance Name (Name of the folders where your content is placed)
$instanceName = "default";

// AUTOLOAD - Classloader. Variable is outside to avoid multiple instances.
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
	if($classloader == null) {
		$classloader = new ClassLoader();
	}
	
	$directorys = array(
             			'custom/lib/piwi'
    				);
       
    foreach($directorys as $directory) {
       $result = $classloader->loadClass($directory,$class);
       if($result == true) {
			return;       	
       }
    }
}

// scripts
/*
if($_REQUEST['p'] == "google") {
	include("scripts/googleintegration.php");
	$_REQUEST['p'] = $redirectTo;
	echo $redirectTo;
}
*/

// TODO: globals are evil :-)
$connectors = new ConnectorFactory('custom/content/'.$instanceName.'/connectors.xml');
// the following var is used in the generatorfactory class - should be
// not such an important variable. 
$generators = new GeneratorFactory('custom/content/'.$instanceName.'/generators.xml');


// Path to the current template
$pathToTemplate = 'templates/'.$instanceName.'/';

		
$id = "default";
if($_REQUEST['p'] != null) {
	$id = $_REQUEST['p'];
}

$site = new Site('custom/content/'.$instanceName.'/site.xml');
$ext = $site->extension($id);
if($ext == "xml") {
	$page = $site->read($id);
	$content = $page->transform();
}

// TODO: navigation builder not dynamic
$nav = $site->navigation($id); 
$navBuilder = new SimpleTextNavigation('custom/content/'.$instanceName.'/xml/');
$htmlNav = $navBuilder->build($nav);

// Include your template here
if($page->getTemplate() != "") {
	include($pathToTemplate.'/'.$page->getTemplate());
} else {
	include($pathToTemplate.'/index.php');
}
?>