<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Christian Grobmeier - Open Source, J2EE & more</title>
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div id="container">       
	<div id="menu">		 <?=$htmlNav?>	</div>
	<div id="logo"></div>	<div id="content">		<div id="subcontent">		</div>  		<div id="article">			<? 
				if($valid != "" || $valid != null) {
					echo "<font color='#ff0000'>".$valid."</font>";		
				}
			?>
			<?=$content['article']?>	    	</div>
    	<div id="article">
    		Manually added SQLite-Content:<br>
    		<?=$generators->getInstance("default-content")->generate(); ?>
    	</div>    <div id="footer"></div></div>

</body>
</html>
