<?php


error_reporting(E_ALL);
require_once (dirname(dirname(__FILE__)) . '/vendor/autoload.php');


//----------------------------------------------------------------------------------------
// get
function get($url)
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   	

	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	curl_close($ch);
	
	return $response;
}

//----------------------------------------------------------------------------------------


$dois=array(
"10.1371/journal.pone.0046421"
);

$orcids = array();

foreach ($dois as $doi)
{
	echo $doi . "\n";

	$url = 'https://enchanting-bongo.glitch.me/search?q=' . urlencode($doi);
	
	$json = get($url);
	//echo $json;
	
	if ($json != '')
	{
		$obj = json_decode($json);
		
		foreach ($obj->orcid as $orcid)
		{
			$orcids[] = $orcid;
		}
	}
	
}

$orcids = array_unique($orcids);

print_r($orcids);

echo '$orcids=array(' . "\n";
foreach ($orcids as $orcid)
{
	echo '"' . $orcid . '",' . "\n";
}

echo ');' . "\n";

?>
