<?php

// Get RDF for ORCID and associated publications

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


$orcids=array(
"0000-0002-2983-9657",
"0000-0002-2636-414X",
"0000-0002-0518-5708",
"0000-0002-9601-855X",
);

$rdf_filename = dirname(__FILE__) . '/orcid.nt';


foreach ($orcids as $orcid)
{
	echo $orcid . "\n";

	$url = 'https://orcid.org/' . $orcid;
	
	//echo $url . "\n";
	
	$json = get($url, 'application/ld+json');	
	
	echo $json . "\n";
	
	$doc = jsonld_decode($json);
	
	//print_r($doc);
	
	// We can't handle the context as a URL do replace with an object
	unset($doc->{'@context'});
	
	$context = new stdclass;
	$context->{'@vocab'} = 'http://schema.org/';
	$doc->{'@context'} = $context ;
	
	//print_r($doc);
	
	$expanded = jsonld_expand($doc);
	
	//print_r($expanded);
	
	$normalized = jsonld_normalize($doc, array('algorithm' => 'URDNA2015', 'format' => 'application/nquads'));
 	
 	file_put_contents($rdf_filename, $normalized  . "\n", FILE_APPEND | LOCK_EX);


}


?>
