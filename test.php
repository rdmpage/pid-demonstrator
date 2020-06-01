<?php

// tests and demos

require_once (dirname(__FILE__) . '/hypothesis.php');
require_once (dirname(__FILE__) . '/triplestore.php');




if (1)
{
	$annotation_id = 'YEez2qEAEeqgNWc0aIiyEg';
	$annotation_id = '9-LDiKP4EeqvhlsZn_ZpMQ';
	
	// get annotation from hypothes.is
	$obj = hypothesis_get_annotation($annotation_id);
	
	print_r($obj);

	// convert to RDF
	$rdf = hypothesis_annotation_to_rdf($obj);
	
	echo $rdf;
}


if (0)
{
	// get annotation from triple store
	$uri = 'https://hypothes.is/a/BJKGEIPbEeqUhY9L5jQFJA';
	

	$jsonld = sparql_construct($config['sparql_endpoint'], $uri);
	echo $jsonld;


}

if (0)
{
	// annotations for entity as data feed
	
	$source = "https://www.cambridge.org/core/journals/edinburgh-journal-of-botany/article/new-species-of-eriocaulon-eriocaulaceae-from-the-southern-western-ghats-of-kerala-india/AD5983CC30B0A9192BD08CF62BBAAC6C/core-reader";
	
	
	
	$query = 'PREFIX oa: <http://www.w3.org/ns/oa#>
SELECT * WHERE {
  ?target oa:hasSource <https://www.cambridge.org/core/journals/edinburgh-journal-of-botany/article/new-species-of-eriocaulon-eriocaulaceae-from-the-southern-western-ghats-of-kerala-india/AD5983CC30B0A9192BD08CF62BBAAC6C/core-reader> .
  ?annotation oa:hasTarget ?target .
}';


	$query = 'PREFIX schema: <http://schema.org/>
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	PREFIX oa: <http://www.w3.org/ns/oa#>
	CONSTRUCT 
	{
		<http://example.rss>
		rdf:type schema:DataFeed;
		schema:name "Annotations";
		schema:dataFeedElement ?item .

		?item 
			rdf:type schema:DataFeedItem;
			rdf:type ?item_type;
			
	}
	WHERE	
	{
		# source
  		VALUES ?source { <' . $source . '> }	
	
  		?target oa:hasSource ?source .
  		?item oa:hasTarget ?target .
  		?item rdf:type ?item_type .
	}';

	$jsonld = sparql_construct_stream($config['sparql_endpoint'], $query);
	echo $jsonld;



}



?>


