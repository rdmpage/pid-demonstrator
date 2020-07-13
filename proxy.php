<?php

$url = 'http://exeg5le.cloudimg.io/s/height/100/https://www.nhm.ac.uk/services/media-store/asset/6e7f351788deb6b5210261849f4cebeb20aa2959/contents/thumbnail';

if (isset($_GET['url']))
{
	$url = $_GET['url'];
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 

if ($accept != '')
{
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
	array(
		"Accept: " . $accept 
		)
	);
}
	
$response = curl_exec($ch);
if($response == FALSE) 
{
	$errorText = curl_error($ch);
	curl_close($ch);
	die($errorText);
}

curl_close($ch);

header("Content-Type: image/jpeg");
echo $response;

?>



