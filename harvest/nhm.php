<?php

// Get RDF for NHM specimens

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
"8449d6f3-af9d-418b-8538-671664bf9536",
"8c5f9a32-a707-461b-a4c1-e3e74a74576c",
"6e8be646-486e-4193-ac46-e13e23c5daef",
"038e765f-1a4a-4e9d-9c3e-86f13c07ab18",
"3d60a244-dd7f-44b6-a823-977f4f3286ab",
"1a995d3f-d4c9-4ee3-a0a3-8301233f2e10",
"b59e3747-3322-4153-9277-46cd7da0adaf",
);

$rdf_filename = dirname(__FILE__) . '/nhm.nt';


foreach ($ids as $id)
{
	echo $id . "\n";

	$url = 'https://data.nhm.ac.uk/object/' . $id  . '.jsonld';
	
	//echo $url . "\n";
	
	$json = get($url, 'application/ld+json');	
	
	echo $json . "\n";
	
	$doc = jsonld_decode($json);
	
	/*
	//print_r($doc);
	
	// We can't handle the context as a URL do replace with an object
	unset($doc->{'@context'});
	
	$context = new stdclass;
	$context->{'@vocab'} = 'http://schema.org/';
	$doc->{'@context'} = $context ;
	*/
	//print_r($doc);
	
	$expanded = jsonld_expand($doc);
	
	print_r($expanded);
	
	$normalized = jsonld_normalize($doc, array('algorithm' => 'URDNA2015', 'format' => 'application/nquads'));
 	
 	file_put_contents($rdf_filename, $normalized  . "\n", FILE_APPEND | LOCK_EX);


}


?>
