<?php

// tests and demos

require_once (dirname(__FILE__) . '/hypothesis.php');
require_once (dirname(__FILE__) . '/triplestore.php');




if (0)
{
	$annotation_id = 'YEez2qEAEeqgNWc0aIiyEg';
	
	// get annotation from hypothes.is
	$obj = hypothesis_get_annotation($annotation_id);

	// convert to RDF
	$rdf = hypothesis_annotation_to_rdf($obj);
}


if (1)
{
	// get annotation from triple store
	$uri = 'https://hypothes.is/a/BJKGEIPbEeqUhY9L5jQFJA';
	

	$jsonld = sparql_construct($config['sparql_endpoint'], $uri);
	echo $jsonld;


}



?>


