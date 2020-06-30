<?php

// Get stream of annotations for this URI


require_once(dirname(__FILE__) . '/triplestore.php');


$uri = 'https://doi.org/10.1017/S0960428620000013';
$uri = 'http://data.rbge.org.uk/herb/E00785221';


if (isset($_REQUEST['uri']))
{
	$uri = $_REQUEST['uri'];
}

$query = 'PREFIX schema: <http://schema.org/>
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	PREFIX oa: <http://www.w3.org/ns/oa#>
	PREFIX dwc: <http://rs.tdwg.org/dwc/terms/>
CONSTRUCT 
{
	<http://example.rss>
	rdf:type schema:DataFeed;
	schema:name "Annotations";
	
	schema:dataFeedElement ?annotation .

	?annotation rdf:type schema:DataFeedItem .
	?annotation rdf:type ?item_type .

	?annotation oa:motivatedBy ?motivation .

	?annotation oa:hasBody ?body .
	?body a ?body_type .
	?body rdf:value ?body_value .   


	?annotation oa:hasTarget ?target .
	?target oa:hasSource ?source .
	?target oa:canonical ?canonical .
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
	
	# display
	?body schema:name ?body_name .
	?target schema:name ?target_name .
	?target schema:creator ?creator .
	?creator schema:name ?creator_name .
	
}
WHERE 
{
	VALUES ?thing { <' . $uri . '> }

	{
		?target oa:canonical ?thing .
		?annotation oa:hasTarget ?target .

	}
	UNION
	{
		?annotation oa:hasBody ?thing .  
	}

	# details about annotation
	?annotation oa:hasTarget ?target .
	?annotation rdf:type ?item_type .
	
	OPTIONAL {
		?annotation oa:motivatedBy ?motivation .
	} 	
	
	
	OPTIONAL {
		?target oa:hasSource ?source .
	}   	

	OPTIONAL {
		?annotation oa:hasBody ?body .
		OPTIONAL {
			?body a ?body_type .
		}
		OPTIONAL {
			?body rdf:value ?body_value .
		}
	}      		  		

	?annotation oa:hasTarget ?target .
	
	OPTIONAL {
		?target oa:canonical ?canonical .
	} 	

	OPTIONAL {
		?target oa:hasSelector ?selector .  

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
	}
	
	# display
	OPTIONAL {
		?body dwc:catalogNumber ?body_name .
	} 		
	
	OPTIONAL {
		?canonical schema:name ?target_name .
		
		OPTIONAL {
			?canonical schema:creator ?creator .
  			?creator schema:givenName ?givenName .
  			?creator schema:familyName ?familyName .
  			BIND(CONCAT(?givenName, " ", ?familyName) AS ?creator_name)
  		}
	} 	
	
}
';


$callback = '';
if (isset($_GET['callback']))
{
	$callback = $_GET['callback'];
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

header("Content-type: application/json");

if ($callback != '')
{
	echo $callback . '(';
}
echo sparql_construct_stream($config['sparql_endpoint'], $query);
if ($callback != '')
{
	echo ')';
}


?>
