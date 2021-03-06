<?php

// Get RDF for RBGE specimens

error_reporting(E_ALL);
require_once (dirname(dirname(__FILE__)) . '/vendor/autoload.php');


//----------------------------------------------------------------------------------------
// get
function get($url, $format = 'application/ld+json')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   	
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
		array(
			"Accept: " . $format 
			)
		);

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


$ids = array(
"E00179300"

);


$rdf_filename = dirname(__FILE__) . '/rbge.nt';


foreach ($ids as $id)
{
	echo $id . "\n";

	$url = 'http://data.rbge.org.uk/herb/' . $id;
	
	echo $url . "\n";
	
	$rdf = get($url, 'application/rdf+xml');	
	
	
	echo $rdf . "\n";
	
	/*
	$doc = jsonld_decode($json);
	
	if ($doc)
	{
	
		$expanded = jsonld_expand($doc);
	
		print_r($expanded);
	
		$normalized = jsonld_normalize($doc, array('algorithm' => 'URDNA2015', 'format' => 'application/nquads'));
	
		file_put_contents($rdf_filename, $normalized  . "\n", FILE_APPEND | LOCK_EX);
	}
	
	*/

}


?>
