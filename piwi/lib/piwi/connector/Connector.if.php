<?php

interface Connector {
	function connect();
	function execute($sql);
	function setProperty($key,$value);
}
?>