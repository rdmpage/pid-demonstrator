<?php

error_reporting(E_ALL);
//error_reporting(0); // there is an unexplained error in json-ld php

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/vendor/autoload.php');

// SPARQL API wrapper

//----------------------------------------------------------------------------------------
// get
function sparql_get($url, $format = 'application/ld+json')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
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
// post
function sparql_post($url, $format = 'application/ld+json', $data =  null)
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
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
// Upload a file of triples
// $triples_filename is the full path to a file of triples
// $graph_key_name for fuseki is 'graph', for blazegraph is 'context-uri'
function upload_from_file($sparql_endpoint, $triples_filename, $graph_key_name = 'context-uri', $graph_uri = '')
{
	$url = $sparql_endpoint;
	
	if ($graph_uri == '')
	{
	}
	else
	{
		$url .= '?' . $graph_key_name . '=' . $graph_uri;
	}
	
	// text/x-nquads is US-ASCII WTF!?
	//$command = "curl $url -H 'Content-Type: text/x-nquads' --data-binary '@$triples_filename'";

	// text/rdf+n3 is compatible with NT and is UTF-8
	// see https://wiki.blazegraph.com/wiki/index.php/REST_API#RDF_data
	$command = "curl $url -H 'Content-Type: text/rdf+n3' --data-binary '@$triples_filename'";

	echo $command . "\n";
	
	$lastline = system($command, $retval);
	
	//echo "   Last line: $lastline\n";
	//echo "Return value: $retval\n";	
	
	if (preg_match('/data modified="0"/', $lastline)) 
	{
		echo "\nError: no data added\n";
		exit();
	}
}


//----------------------------------------------------------------------------------------
// DESCRIBE a resource, by default return as JSON-LD
// Fuseki and Blazegraph both recognise application/ld+json but for quads
// Fuseki uses application/n-quads whereas Blazegraph uses text/x-nquads
function sparql_describe($sparql_endpoint, $uri, $format='application/ld+json')
{
	$url = $sparql_endpoint;
		
	// Query is string
	$data = 'query=' . urlencode('DESCRIBE <' . $uri . '>');
	
	$response = sparql_get($url, $format);
	
	// Fuseki returns nicely formatted JSON-LD, Blazegraph returns array of horrible JSON-LD
	// as first element of an array
	
	$obj = json_decode($response);
	if (is_array($obj))
	{
		$doc = $obj[0];
		
		$context = (object)array(
			'@vocab' => 'http://www.w3.org/ns/oa#'				
		);
	
		$compacted = jsonld_compact($doc, $context);
		
		$response = json_encode($compacted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);		
	}
	

	return $response;
}

//----------------------------------------------------------------------------------------
// CONSTRUCT a resource, by default return as JSON-LD
function sparql_construct($sparql_endpoint, $uri, $format='application/ld+json')
{
	global $context;
	
	$url = $sparql_endpoint;
	
	// encode things that may break SPARQL, e.g. SICI entities
	$uri = str_replace('<', '%3C', $uri);
	$uri = str_replace('>', '%3E', $uri);
	
	// Query is string
	$query = 'CONSTRUCT {
   ?thing ?p ?o .
}
WHERE {
  VALUES ?thing { <' . $uri . '> }
   ?thing ?p ?o .
}';	

$query = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX oa: <http://www.w3.org/ns/oa#>
CONSTRUCT {
   ?thing ?p ?o .
   
    ?thing oa:hasTarget ?target .
    ?target oa:hasSource ?source .
    ?target oa:hasSelector ?selector .
     ?selector a ?selectortype .
     
  	 ?selector oa:start ?start .
  	 ?selector oa:end ?end .
  	 
	 ?selector oa:exact ?exact .
  	 ?selector oa:prefix ?prefix .
  	 ?selector oa:suffix ?suffix .  	
  	 
  	 ?selector oa:hasStartSelector ?hasStartSelector . 
  	 ?hasStartSelector a ?start_selector_type .
  	 ?hasStartSelector rdf:value ?start_selector_value .
  	 
  	 ?selector oa:hasEndSelector ?hasEndSelector .
  	 ?hasEndSelector a ?end_selector_type .  	
  	 ?hasEndSelector rdf:value ?send_selector_value . 
     
}
WHERE {
  VALUES ?thing { <' . $uri . '> }
   ?thing ?p ?o .
   
   ?thing oa:hasTarget ?target .
   ?target oa:hasSelector ?selector .
   ?target oa:hasSource ?source .
   
   ?selector a ?selectortype .
   
   OPTIONAL {
  	 ?selector oa:start ?start .
  	 ?selector oa:end ?end .
  	}
  	
   OPTIONAL {
  	 ?selector oa:exact ?exact .
  	 ?selector oa:prefix ?prefix .
  	 ?selector oa:suffix ?suffix .
  	}  	
  	
   OPTIONAL {
  	 ?selector oa:hasStartSelector ?hasStartSelector .
  	 ?hasStartSelector a ?start_selector_type .
  	 ?hasStartSelector rdf:value ?start_selector_value .
  	}  	 
  	
   OPTIONAL {
  	 ?selector oa:hasEndSelector ?hasEndSelector .
  	 ?hasEndSelector a ?end_selector_type .
  	 ?hasEndSelector rdf:value ?send_selector_value .
  	}    	 	
}';	


	$data = 'query=' . urlencode($query);
	
	$response = sparql_post($url, $format, $data);
	

	
	
	// Fuseki returns nicely formatted JSON-LD, Blazegraph returns array of horrible JSON-LD
	// as first element of an array
	
	$obj = json_decode($response);
	if (is_array($obj))
	{
	
		$doc = $obj[0];
		
		$doc = $obj;
		
	
		if (0)
		{
			$data = jsonld_compact($doc, $context);
		}
		else
		{
			$n = count($doc);
			$type = '';
			$i = 0;
			while ($i < $n && $type == '')
			{
				if ($doc[$i]->{'@id'} == $uri)
				{
					$type = $doc[$i]->{'@type'};
				}
				$i++;
			}
		
		
			$frame = (object)array(
					'@context' => $context,
					'@type' => $type
				);
				
			$data = jsonld_frame($doc, $frame);
				
		}
		
		$response = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);		
	}
	

	return $response;
}

//----------------------------------------------------------------------------------------
// QUERY, by default return as JSON
function sparql_query($sparql_endpoint, $query, $format='application/json')
{
	$url = $sparql_endpoint . '?query=' . urlencode($query);
	
	$response = sparql_get($url, $format);

	return $response;
}

//----------------------------------------------------------------------------------------
// CONSTRUCT a stream, by default return as JSON-LD
function sparql_construct_stream($sparql_endpoint, $query, $format='application/ld+json')
{
	if (1)
	{
		$response = sparql_get(
			$sparql_endpoint . '?query=' . urlencode($query), 
			$format,
			'query=' . $query
		);
	}
	else
	{
		$response = sparql_post(
			$sparql_endpoint, 
			$format,
			'query=' . $query
		);
	}
		
	$obj = json_decode($response);
	if (is_array($obj))
	{
		$doc = $obj;
		
		//echo '<pre>' . print_r($obj) . '<pre>';
		
		
		$context = (object)array(
			'@vocab' => 'http://schema.org/'
		);
		
		// dataFeedElement is always an array
		$dataFeedElement = new stdclass;
		$dataFeedElement->{'@id'} = "dataFeedElement";
		$dataFeedElement->{'@container'} = "@set";
		
		$context->{'dataFeedElement'} = $dataFeedElement;	
	
		$frame = (object)array(
			'@context' => $context,
			'@type' => 'http://schema.org/DataFeed'
		);
			
		$data = jsonld_frame($doc, $frame);
			
		$response = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);		
	}
	

	return $response;
}




?>
