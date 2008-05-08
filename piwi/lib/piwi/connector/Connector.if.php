<?php

interface Connector {
	function connect();
	function setProperty($key,$value);
}
?>