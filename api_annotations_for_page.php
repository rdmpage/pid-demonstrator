<?php

// Get annotations for this URI

$filename = 'map.json';

$json = file_get_contents($filename);

$map = json_decode($json);


$uri = 'https://doi.org/10.1017/S0960428620000013';
$uri = 'http://specimens.kew.org/herbarium/K000037101';
$uri = 'https://doi.org/10.3897/phytokeys.94.21753';


if (isset($_REQUEST['uri']))
{
	$uri = $_REQUEST['uri'];
}

$result = new stdclass;

if (isset($map->{$uri}))
{
	$result = $map->{$uri};
}



$callback = '';
if (isset($_GET['callback']))
{
	$callback = $_GET['callback'];
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header("Content-type: application/json");

if ($callback != '')
{
	echo $callback . '(';
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if ($callback != '')
{
	echo ')';
}


?>
