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
			font-family: Helvetica, Arial, sans-serif;
		}
	</style>
	<title><?php echo $config['site_name']; ?></title>
</head>
<body>
	<h1>PID Demonstrator</h1>
	
	<h2>Step 1</h2>
	
	<p>
		<a onclick="alert('Please drag this link to your bookmarks bar.'); return false;" href="javascript:(function(a){%20a=document.createElement('script');a.type='text/javascript';a.src='<?php echo $config['web_server'] . $config['web_root']; ?>js/script.js?x='+Date.now();document.getElementsByTagName('body')[0].appendChild(a);})();">Annotate It!</a>	
		<span>← Drag this to your bookmarks bar</span>
	</p>	
	
	<h2>Step 2</h2>
	
	<p>Vist some sites that the PID demonstrator knows about, click on the "Annotatie It!" bookmark and see the links.</p>

	<ul>
		<li><a href="https://doi.org/10.4000/cve.6886" target="_new">Proust’s Ruskin: From Illustration to Illumination</a></li>
		<li><a href="http://access.bl.uk/item/viewer/ark:/81055/vdc_100024135011.0x000001" target="_new">Italy, a poem (British Library)</a></li>
		<li><a href="https://data.rbge.org.uk/herb/E00179300" target="_new"><i>Begonia albo-coccinea</i> E00179300 (Royal Botanic Gardens, Edinburgh)</a></li>
		<li><a href="https://doi.org/10.1017/S0960428619000349" target="_new">A NEW SECTION (BEGONIA SECT. FLOCCIFERAE SECT. NOV.) AND TWO NEW SPECIES IN BEGONIACEAE FROM THE WESTERN GHATS OF INDIA</a></li>
		<li><a href="https://data.nhm.ac.uk/object/adbba503-eef1-44de-b7d2-fddc8b4e6275" target="_new"><i>Begonia floccifera</i> BM000944668 (Natural History Museum)</a></li>
		<li><a href="https://www.biodiversitylibrary.org/page/56312118" target="_new">European Journal of Taxonomy in BHL</a></li>
	<ul>

</body>
</html>


