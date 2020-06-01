<?php

error_reporting(E_ALL);

require_once 'vendor/autoload.php';
use Sunra\PhpSimple\HtmlDomParser;


//----------------------------------------------------------------------------------------
function get_html($url)
{	
	$data = null;

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
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	
	// print_r($info);
	
	curl_close($ch);
	
	return $data;
}


//----------------------------------------------------------------------------------------
// Get one or more poitentially canonical identifiers fron a URL
function get_canonical_identifiers($url)
{
	$identifiers = array();
	
	// Do we need to clean url?
	// Is URL for a PDF?
	
	// OJS PDF to OJS HTML
	if (preg_match('/(?<base>^https?.*)\/download\/(?<id>\d+)\/(?<file>\d+)/', $url, $m))
	{
		$url = $m['base'] . '/view/' . $m['id'];
	}	
	
	// Get article HTML and extract meta tags
	$html = get_html($url);
	
	
	if ($html != '')
	{
		// echo $html;

		$dom = HtmlDomParser::str_get_html($html);

		$metas = $dom->find('meta');


		foreach ($metas as $meta)
		{
			// echo $meta->name . " " . $meta->content . "\n";
		
			switch ($meta->name)
			{			
				case 'citation_doi':
				case 'prism.doi':
				case 'DC.Identifier.DOI':
					$identifiers['doi'] = $meta->content;
					break;
				
				case 'dc.identifier':
					break;
				
				default:
					break;
			}
		}	
	}
	
	return $identifiers;

}



$url = 'https://phytokeys.pensoft.net/article_preview.php?id=21753';

$url = 'https://europeanjournaloftaxonomy.eu/index.php/ejt/article/view/405';
$url = 'https://europeanjournaloftaxonomy.eu/index.php/ejt/article/download/405/856';


$identifiers = get_canonical_identifiers($url);

print_r($identifiers);




?>
