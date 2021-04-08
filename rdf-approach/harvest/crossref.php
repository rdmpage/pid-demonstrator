<?php

// Get RDF for CrossRef

error_reporting(E_ALL);
require_once (dirname(dirname(__FILE__)) . '/vendor/autoload.php');


//----------------------------------------------------------------------------------------
// get
function get($url, $format = 'application/json')
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


$dois=array(
//"10.1371/journal.pone.0041767",
//"10.1371/journal.pone.0046421",

"10.1371/journal.pone.0066957",
"10.1371/journal.pone.0053873",
"10.1371/journal.pone.0048145",
"10.1371/journal.pone.0053712",

"10.3897/phytokeys.94.21753",

"10.1017/S0960428619000349",

"10.5852/ejt.2015.126",
);

$rdf_filename = dirname(__FILE__) . '/crossref.nt';


foreach ($dois as $doi)
{
	$url = 'https://api.crossref.org/v1/works/http://dx.doi.org/' . $doi;
	
	$json = get($url);
	
	if ($json != '')
	{
		$obj = json_decode($json);
		
		$nt = '';
		
		print_r($obj);
		
		$title = '';
		
		if (is_array($obj->message->title))
		{
			$title = $obj->message->title[0];		
		}
		else
		{
			$title = $obj->message->title;				
		}
		
		$nt .= '<https://doi.org/' . $doi . '> <http://schema.org/name> "' . addcslashes($title, '"') . '" . ' . "\n";


  		file_put_contents($rdf_filename, $nt  . "\n", FILE_APPEND | LOCK_EX);


	}
}



?>
