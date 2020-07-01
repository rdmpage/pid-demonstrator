<?php

require_once (dirname(__FILE__) . '/config.inc.php');

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<style>
		body {
			margin: 0px;
			padding:20px;
			font-family: sans-serif;
		}
	</style>
	<title><?php echo $config['site_name']; ?></title>
</head>
<body>
	<h1>PID Demonstrator</h1>
	
	<p>
		<a onclick="alert('Please drag this link to your bookmarks bar.'); return false;" href="javascript:(function(a){%20a=document.createElement('script');a.type='text/javascript';a.src='<?php echo $config['web_server'] . $config['web_root']; ?>js/script.js?x='+Date.now();document.getElementsByTagName('body')[0].appendChild(a);})();">Annotate It!</a>	
		<span>← Drag this to your bookmarks bar</span>
	</p>	

</body>
</html>


