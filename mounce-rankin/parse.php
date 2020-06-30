<?php

// Parse Ross and Aime's data

error_reporting(E_ALL);
require_once (dirname(dirname(__FILE__)) . '/vendor/autoload.php');

require_once (dirname(dirname(__FILE__)) . '/context.php');


//----------------------------------------------------------------------------------------
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

//----------------------------------------------------------------------------------------

$filename = 'plosone-bmnh.csv';
$filename = 'subset.csv';

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");
		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$row = fgetcsv(
		$file_handle, 
		0, 
		translate_quoted(','),
		translate_quoted('"') 
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
		
			// print_r($obj);	
			
			// convert to annotation
			
			if (isset($obj->{'NHM Data Portal Link'}) && isset($obj->{'Article DOI Link'}))
			{
				$annotation = new stdclass;
				
				$annotation->{'@context'} = $context ;

				$annotation->{'@id'} = "https://github.com/rdmpage/pid-demonstrator/blob/master/mounce-rankin/subset.csv" . "#" . $row_count;
				$annotation->{'@type'} = 'Annotation';
			
				// Body is the specimen
				$annotation->body = $obj->{'NHM Data Portal Link'};

				// NHM has switched from specimen  to object
				$annotation->body = str_replace('/specimen/', '/object/', $annotation->body);

				// NHM is now HTTPS
				$annotation->body = str_replace('http', 'https', $annotation->body);
			
			
				// Target is the article
				$doi = $obj->{'Article DOI Link'};
				
				// Update DOI s to HTTPS
				$doi = preg_replace('/https?:\/\/(dx\.)?doi.org\//', '', $doi);
			
				$annotation->target = new stdclass;
				$annotation->target->canonical = 'https://doi.org/' . $doi;
				
				
				//print_r($annotation);
				
				$normalized = jsonld_normalize($annotation, array('algorithm' => 'URDNA2015', 'format' => 'application/nquads'));
				
				echo $normalized . "\n";

			}
			
			
			
			
		}
	}	
	$row_count++;
}
?>

