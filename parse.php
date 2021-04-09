<?php

// Parse CSV data

error_reporting(E_ALL);

require_once 'vendor/autoload.php';
use Sunra\PhpSimple\HtmlDomParser;

require_once (dirname(__FILE__) . '/config.inc.php');

//----------------------------------------------------------------------------------------
function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '._-');
}

//----------------------------------------------------------------------------------------
function base64_url_decode($input) {
 return base64_decode(strtr($input, '._-', '+/='));
}

//----------------------------------------------------------------------------------------
// get
function get($url, $format = 'application/ld+json')
{
	global $config;
	
	$data = '';
	
	$extension = 'txt';
	
	switch ($format)
	{
		case 'application/ld+json':
			$extension = 'json';
			break;

		case 'application/rdf+xml':
			$extension = 'xml';
			break;
	
		default:
			break;
	}
	
	$filename = $config['cache'] . '/' . base64_url_encode($url) . '.' . $extension;
	
	if (!file_exists($filename))
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
		
		
		if ($response != '')
		{
			file_put_contents($filename, $response);
	
		}		
	
		curl_close($ch);
	}
	
	$data = file_get_contents($filename);
	
	return $data;
}

//----------------------------------------------------------------------------------------
function get_html($url)
{	
	global $config;
	
	$html = '';
	
	$filename = $config['cache'] . '/' . base64_url_encode($url) . '.html';
	
	if (!file_exists($filename))
	{
		$opts = array(
		  CURLOPT_URL =>$url,
		  CURLOPT_FOLLOWLOCATION => TRUE,
		  CURLOPT_RETURNTRANSFER => TRUE,
		  CURLOPT_COOKIEJAR		=> 'cookie.txt',
		  CURLOPT_SSL_VERIFYPEER => FALSE /* handle cases where SSH is broken */
		);

		$opts[CURLOPT_HTTPHEADER] = array(
			'Accept: text/html',
			'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
		);	
	
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$html = curl_exec($ch);
		$info = curl_getinfo($ch); 
		
		if ($html != '')
		{
			file_put_contents($filename, $html);
	
		}
	
		
		curl_close($ch);
		
	}
	
	$html = file_get_contents($filename);
	
	
	return $html;
}


//----------------------------------------------------------------------------------------
// Get title and thumbnail from a web page where we can
function get_details($url)
{
	$site = new stdclass;
	$site->url = $url;
		
	// Get page HTML and extract meta tags
	$html = get_html($url);
	
	
	if ($html != '')
	{
		// echo $html;

		$dom = HtmlDomParser::str_get_html($html);

		// meta tags
		$metas = $dom->find('meta');

		foreach ($metas as $meta)
		{
			if (0)
			{
				if (isset($meta->name))
				{
					echo $meta->name;
				}
				if (isset($meta->property))
				{
					echo $meta->property;
				}
				if (isset($meta->content))
				{
					echo ' ' . $meta->content;
				}
				echo "\n";
			}
				
			if (isset($meta->property))
			{
				switch ($meta->property)
				{
					// Facebook
					case 'og:title':
						if (!isset($site->title))
						{
							$site->title = $meta->content;
						}
						break;

					case 'og:image':
						if (!isset($site->image))
						{
							$site->image = $meta->content;
						}
						break;
						
					case 'og:url':
						if (!isset($site->site_url))
						{
							$site->site_url = $meta->content;
						}
						break;
						
					
					default:
						break;
				}
			}

			if (isset($meta->name))
			{
				switch ($meta->name)
				{		
					// Twitter
					case 'twitter:title':
						if (!isset($site->title))
						{
							$site->title = $meta->content;
						}
						break;

					case 'twitter:image':
						if (!isset($site->image))
						{
							$site->image = $meta->content;
						}
						break;
					
					// Google Scholar
					case 'citation_title':
						if (!isset($site->title))
						{
							$site->title = $meta->content;
						}
						break;

					default:
						break;
				}
			}
			
		}	
		
		// Do we need to keep trying...?
		
		if (!isset($site->image))
		{
			// Site-specific rules...
			
			// BHL
			if (preg_match('/biodiversitylibrary.org\/page\/(?<page>\d+)/', $url, $m))
			{
				$site->image = 'https://www.biodiversitylibrary.org/pagethumb/' . $m['page'] . ',200,200';
			}		
		}
		
		// canonical URL
		if (!isset($site->site_url))
		{
			$links = $dom->find('link');

			foreach ($links as $link)
			{
				if (isset($link->rel) && $link->rel == "canonical")
				{
					$site->site_url = $link->href;
				}
			}
		}
		
		
		// favicon
		if (!isset($site->favicon))
		{
			$links = $dom->find('link');

			foreach ($links as $link)
			{
				if (isset($link->rel))
				{
					// try for the favicon 
					switch ($link->rel)
					{
						case 'icon':
						case 'shortcut icon':
						case 'Shortcut Icon':
						case 'SHORTCUT ICON':
							if (!isset($site->favicon))
							{
								// is it a relative or an absolute link?
								if (preg_match('/^https?/', $link->href))
								{							
									$site->favicon = $link->href;
								}
								
								if (preg_match('/^\/\//', $link->href))
								{					
									$parts = parse_url($url);
										
									$site->favicon = $parts['scheme'] . ':' . $link->href;
								}

								if (preg_match('/^\/\w/', $link->href) && isset($site->site_url))
								{					
									$parts = parse_url($site->site_url);
										
									$site->favicon = $parts['scheme'] . '://' . $parts['host'] . $link->href;
								}
								
							}
							break;
							
							/*
							<link rel="alternate" type="text/xml+oembed" href="http://api.bl.uk/metadata/oembed/xml/?url=http://access.bl.uk/item/viewer/ark:/81055/vdc_100024135011.0x000001" title="Italy, a poem">
							*/
						case 'alternate':
							break;
					
						default:
							break;
					}
				}
		
			}
		}
		
		// hard code sites that are just fucked up
		if (!isset($site->favicon))
		{
			if (preg_match('/kew.org/', $url))
			{
				$site->favicon = 'https://www.kew.org/themes/custom/kew_gardens/assets/favicons/favicon-32x32.png';
			}
		}
		
		// last ditch attempt to get title
		if (!isset($site->title))
		{
			$elements = $dom->find('head/title');

			foreach ($elements as $element)
			{
				if (!isset($site->title))
				{
					$site->title = $element->plaintext;
				}
			}
		}
		
		// special handling for specimens
		
		if (preg_match('/data.nhm.ac.uk/', $url))
		{
			/*
			// JSON-LD
			$scripts = $dom->find('script[type=application/ld+json]');
			foreach ($scripts as $script)
			{
				$json = $script->innertext;
			}
			*/
			
			$rdf = get($url, 'application/rdf+xml');
			
			$dom= new DOMDocument;
			$dom->loadXML($rdf);
			$xpath = new DOMXPath($dom);
			
			$xpath->registerNamespace('foaf', 'http://xmlns.com/foaf/0.1/');
			$xpath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

			foreach($xpath->query('//foaf:Image/foaf:thumbnail/@rdf:resource') as $node)
			{
				$site->image = $node->firstChild->nodeValue;
			}
		}
		
		if (preg_match('/data.rbge.org.uk/', $url))
		{
			// RDF
			
			$rdf = get($url, 'application/rdf+xml');
			
			$dom= new DOMDocument;
			$dom->loadXML($rdf);
			$xpath = new DOMXPath($dom);
			
			$xpath->registerNamespace('dwc', 'http://rs.tdwg.org/dwc/terms/');
			$xpath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

			foreach($xpath->query('//dwc:associatedMedia/@rdf:resource') as $node)
			{
				$site->image = $node->firstChild->nodeValue;
			}
		}
		
		if (preg_match('/kew.org/', $url))
		{
			// RDF
			
			$rdf = get($url, 'application/rdf+xml');
			
			$dom= new DOMDocument;
			$dom->loadXML($rdf);
			$xpath = new DOMXPath($dom);
			
			$xpath->registerNamespace('dwc', 'http://rs.tdwg.org/dwc/terms/');
			$xpath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

			foreach($xpath->query('//dwc:associatedMedia/@rdf:resource') as $node)
			{
				$site->image = $node->firstChild->nodeValue;
			}
		}		
		
		if (preg_match('/botanicalcollections.be/', $url))
		{
			// RDF
			
			$rdf = get($url, 'application/rdf+xml');
			
			$dom= new DOMDocument;
			$dom->loadXML($rdf);
			$xpath = new DOMXPath($dom);
			
			$xpath->registerNamespace('dwc', 'http://rs.tdwg.org/dwc/terms/');
			$xpath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

			foreach($xpath->query('//dwc:associatedMedia/@rdf:resource') as $node)
			{
				$site->image = $node->firstChild->nodeValue;
			}
		}		
				
		
	}
	
	return $site;

}



//----------------------------------------------------------------------------------------
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

//----------------------------------------------------------------------------------------


// Read a CSV of collection item and citing thing and build map with some basic metadata

$items = array();

$map = array();

$data = array();


$filename = 'PID demonstrator examples - Sheet1.tsv';

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");
		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$row = fgetcsv(
		$file_handle, 
		0, 
		"\t" 
		);
		
	$go = is_array($row);
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
		}
		else
		{
			$obj = new stdclass;
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$obj->{$headings[$k]} = $v;
				}
			}
		
			//print_r($obj);
			
			// store items
			
			// collection object
			if (!in_array($obj->{$headings[0]}, $items))
			{
				$items[] = $obj->{$headings[0]};
			}

			// thing linking to colelction object
			if (!in_array($obj->{$headings[1]}, $items))
			{
				$items[] = $obj->{$headings[1]};
			}
			
			// store links
			
			// source of link
			if (!isset($map[$obj->{$headings[0]}]))
			{
				$item = new stdclass;
				$item->links = array();
				$map[$obj->{$headings[0]}] = $item;
			}			
			$map[$obj->{$headings[0]}]->links[] = $obj->{$headings[1]};
			
			// target of link
			if (!isset($map[$obj->{$headings[1]}]))
			{
				$item = new stdclass;
				$item->links = array();
								
				$map[$obj->{$headings[1]}] = $item;
			}			
			
			$map[$obj->{$headings[1]}]->links[] = $obj->{$headings[0]};
			
			

			
		}
	}	
	$row_count++;
}


// print_r($items);


// Get metadata for items into cache
foreach ($items as $item)
{
	$fname = base64_url_encode($item);
	
	// echo $item . "\n";
	// echo $fname . "\n";
	
	$r = get_details($item);
	
	// print_r($r);

}

// print_r($map);

$data = array();

foreach ($map as $k => $v)
{
	$data[$k] = array();
	foreach ($map[$k]->links as $link)
	{
		$item = get_details($link);
		$data[$k][] = $item;
	}
}

// print_r($data);

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


?>

