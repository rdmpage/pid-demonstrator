<?php

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/vendor/autoload.php');

require_once (dirname(__FILE__) . '/context.php');


$hypothesis_api_url = 'https://api.hypothes.is/api';


//----------------------------------------------------------------------------------------
function hypothesis_get($url)
{	
	$data = null;

	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE
	);
	
	$opts[CURLOPT_HTTPHEADER] = 
		array(
			"Accept: application/vnd.hypothesis.v1+json" 
		);
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);
	
	return $data;
}

//----------------------------------------------------------------------------------------
function hypothesis_post($url, $data)
{	
	global $config;

	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE,
	  CURLOPT_POST			 => TRUE,
	  CURLOPT_POSTFIELDS	 => $data
	);
	
	$opts[CURLOPT_HTTPHEADER] = 
		array(
			"Accept: application/vnd.hypothesis.v1+json",
			"Content-type: application/json",
			"Authorization: Bearer " . $config['hypothesis-api-key']
		);
		
	//print_r($opts);
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);
	
	return $data;
}


//----------------------------------------------------------------------------------------
function hypothesis_get_annotation($id, $cache = false)
{
	global $config;
	global $hypothesis_api_url;
	
	$obj = null;
	
	$url = $hypothesis_api_url . '/annotations/' . $id;

	$json = hypothesis_get($url);
	if ($json != '')
	{
		$obj = json_decode($json);
		
		if ($cache)
		{
			file_put_contents($config['cache'] . '/' . $id . '.json', $json);
		}
	}
	
	return $obj;
}

//----------------------------------------------------------------------------------------
function hypothesis_create_annotation($annotation)
{
	global $hypothesis_api_url;
	
	$obj = null;
	
	$url = $hypothesis_api_url . '/annotations';

	$json = hypothesis_post($url, json_encode($annotation));
	
	// echo $json;	
	
	if ($json != '')
	{
		$obj = json_decode($json);
	}
	
	return $obj;
}


//----------------------------------------------------------------------------------------
function hypothesis_search_uri($uri)
{
	global $hypothesis_api_url;
	
	$obj = null;
	
	$parameters = array(
		'limit' => 20,
		'uri' => $uri
	);
	
	$url = $hypothesis_api_url . '/search?' . http_build_query($parameters);

	$json = hypothesis_get($url);
	
	// echo $json;	
	
	if ($json != '')
	{
		$obj = json_decode($json);
	}
	
	return $obj;
}

//----------------------------------------------------------------------------------------
function hypothesis_search_doi($doi)
{
	return hypothesis_search_uri('doi:' . $doi);

}

//----------------------------------------------------------------------------------------
// Convert hypothes.is annotaton object to RDF
function hypothesis_annotation_to_rdf($obj, $format='jsonld')
{
	global $context;

	// generate triples as array of strings
	
	$bnode_counter = 1;
	
	$subject_id = '<https://hypothes.is/a/' . $obj->id . '>';
	
	$triples[] = $subject_id . ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/ns/oa#Annotation> . ';
	
	foreach($obj->target as $target)
	{	
		$target_id = '_:b' . $bnode_counter++;		
		$triples[] = $subject_id . ' <http://www.w3.org/ns/oa#hasTarget> ' . 	$target_id . ' . ';	
		
		// source
		$triples[] = $target_id . ' <http://www.w3.org/ns/oa#hasSource> <' . 	$target->source . '> . ';
		
		// target and selectors		
		foreach ($target->selector as $selector)
		{
			$selector_id = '_:b' . $bnode_counter++;
			
			$triples[] = $target_id . ' <http://www.w3.org/ns/oa#hasSelector> ' . $selector_id  . ' . ';
			
			$triples[] = $selector_id . ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/ns/oa#' . $selector->type . '> . ';

			foreach ($selector as $k => $v)
			{
				switch ($k)
				{
					case 'end':
					case 'exact':
					case 'prefix':
					case 'start':
					case 'suffix':
						$triples[] = $selector_id . ' <http://www.w3.org/ns/oa#' . $k . '> "'. addcslashes($v, "\"\n\r") . '" . ';
						break;		
						
					case 'startContainer':
						$xpath_id =  '_:b' . $bnode_counter++;
						$triples[] = $selector_id . ' <http://www.w3.org/ns/oa#hasStartSelector> ' . $xpath_id . ' . ';
						$triples[] = $xpath_id . ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/ns/oa#XPathSelector> . ';
						$triples[] = $xpath_id . ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "'. addcslashes($v, '"') . '" . ';
						break;
											
					case 'endContainer':
						$xpath_id =  '_:b' . $bnode_counter++;
						$triples[] = $selector_id . ' <http://www.w3.org/ns/oa#hasEndSelector> ' . $xpath_id . ' . ';
						$triples[] = $xpath_id . ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/ns/oa#XPathSelector> . ';
						$triples[] = $xpath_id . ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "'. addcslashes($v, '"') . '" . ';
						break;
				
					default:
						break;
				}			
			}
		}
	}

	// print_r($triples);
	
	$nt = join("\n", $triples) . "\n\n";	
	
	if ($format == 'jsonld')
	{
		$doc = jsonld_from_rdf($nt, array('format' => 'application/nquads'));
		
		$frame = new stdclass;
		$frame->{'@context'} = $context;
		$frame->{'@type'} = 'http://www.w3.org/ns/oa#Annotation';		
	
		$framed = jsonld_frame($doc, $frame);
	
		$rdf = json_encode($framed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);	
	}
	else
	{
		$rdf = $nt;
	}

	return $rdf;

}



//----------------------------------------------------------------------------------------
// Annotation class expected by hypothesis
class Annotation
{
	var $data;
	
	//------------------------------------------------------------------------------------
	function __construct($uri)
	{
		$this->data = new stdclass;
		
		$this->data->uri = $uri;
		
		$this->data->document = new stdclass;
		$this->data->tags = array();
		
		$this->data->target = array();


		$target = new stdclass;
		$target->source = $uri;
		$target->selector = array();
						
		$this->data->target[] = $target;

		
		$this->set_tags(array('api'));

	}
	
	//------------------------------------------------------------------------------------
	function add_permissions($user)
	{
		// Ensure that we have acct prefix
		if (!preg_match('/^acct:/', $user))
		{
			$user = 'acct:' . $user;
		}
		$this->data->user = $user;
		$this->data->permissions = new stdclass;
		$this->data->permissions->read = array("group:__world__");
		$this->data->permissions->update = array($user);
		$this->data->permissions->delete = array($user);
		$this->data->permissions->admin = array($user);	
	}
	
	//------------------------------------------------------------------------------------
	function add_range($startContainer, $startOffset, $endContainer, $endOffset)
	{
		$range = new stdclass;
		$range->type = "RangeSelector";				
		
		$range->startContainer  = $startContainer;
		$range->startOffset  	= $startOffset;
		$range->endContainer  	= $endContainer;
		$range->endOffset  		= $endOffset;
		
		$this->data->target[0]->selector[] = $range;	
	}
	
	//------------------------------------------------------------------------------------
	function add_text_position($start, $end)
	{
		$range = new stdclass;
		$range->type = "TextPositionSelector";				
		
		$range->start  = $start;
		$range->end    = $end;
		
		$this->data->target[0]->selector[] = $range;	
	}	
	
	//------------------------------------------------------------------------------------
	function add_text_quote($exact, $prefix = '', $suffix = '')
	{
		$quote = new stdclass;
		$quote->type = "TextQuoteSelector";		
		
		$quote->exact = $exact;
		if ($prefix != '')
		{
			$quote->prefix = $prefix;
		}
		if ($suffix != '')
		{
			$quote->suffix = $suffix;
		}
		
		$this->data->target[0]->selector[] = $quote;
	}
	
	//------------------------------------------------------------------------------------
	function add_tag($tag)
	{
		$this->data->tags[] = $tag;
	}
	
	
	//------------------------------------------------------------------------------------
	function set_tags($tags)
	{
		$this->data->tags = $tags;
	}

	//------------------------------------------------------------------------------------
	function set_text($text)
	{
		$this->data->text = $text;
	}
	
	//------------------------------------------------------------------------------------
	function set_doi($doi)
	{
		if (!isset($this->data->document->highwire))
		{
			$this->data->document->highwire = new stdclass;
		}
		$this->data->document->highwire->doi = array($doi);
	}	
	
	//------------------------------------------------------------------------------------
	function set_pdf_url($url)
	{
		if (!isset($this->data->document->highwire))
		{
			$this->data->document->highwire = new stdclass;
		}
		$this->data->document->highwire->pdf_url = array($url);
	}	
	
	//------------------------------------------------------------------------------------
	function set_title($title)
	{
		if (!isset($this->data->document->title))
		{
			$this->data->document->title = array();
		}
		$this->data->document->title[] = $title;
	}	
	
	//------------------------------------------------------------------------------------
	function add_identifier($identifier)
	{
		if (!isset($this->data->document->dc))
		{
			$this->data->document->dc = new stdclass;
		}
		if (!isset($this->data->document->dc->identifier))
		{
			$this->data->document->dc->identifier = array();
		}
		$this->data->document->dc->identifier[] = $identifier;
	}	
	

}



?>
