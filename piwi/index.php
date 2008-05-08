<?
// Default page view
include("lib/piwi/XMLPage.class.php");
include("lib/piwi/Site.class.php");

// Default Exception
include("lib/piwi/PiwiException.class.php");

// Connectors classes - replace with autoload
include("lib/piwi/connector/ConnectorFactory.class.php");
include("lib/piwi/connector/Connector.if.php");
include("lib/piwi/connector/SQLiteConnector.class.php");
include("lib/piwi/connector/MySQLConnector.class.php");


// scripts
/*
if($_REQUEST['p'] == "google") {
	include("scripts/googleintegration.php");
	$_REQUEST['p'] = $redirectTo;
	echo $redirectTo;
}
*/

$connectors = new ConnectorFactory('content/default/connectors.xml');
$connectors->getInstance("default-connection");

// Path to the current template
$pathToTemplate = 'templates/default/';

		
$id = "default";
if($_REQUEST['p'] != null) {
	$id = $_REQUEST['p'];
}

$site = new Site('content/default/site.xml');
$ext = $site->extension($id);
if($ext == "xml") {
	$page = $site->read($id);
	$content = $page->transform();
}

$nav = $site->navigation($id); 


$topNav = "";
$navString = "";

foreach($nav as $parent) {
	$linkid = $parent['id'];
	
	$topLink = "";
   	// $topLink .= "<tr>";
   	$topLink .= "<a class=\"links\" href=\"".$linkid.".html\">".$parent['label']."</a>&nbsp;&nbsp;";
   
	
	if($parent['childs'] != null) {
		foreach($parent['childs'] as $entry) {
			if(strpos($entry['href'], "content/default/xml/") !== false) {
		    	$link = str_replace("content/default/xml/", "", $entry['href']);
		    }
    		        	
		    $link = str_replace(".xml", ".html", $link);
		    $navString .= "<a href=\"".$entry['id'].".html\">".$entry['label']."</a><br>";
		    $target = "";
		}
	}
	
	//$topLink .= "</tr>";
    $topNav .= $topLink;
	$topNav .= "\n";
}

// Include your template here
include('templates/default/index.php');
?>