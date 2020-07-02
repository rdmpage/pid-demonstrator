<?php

// Parse CSV data

error_reporting(E_ALL);
require_once (dirname(dirname(__FILE__)) . '/vendor/autoload.php');

require_once (dirname(dirname(__FILE__)) . '/context.php');

//----------------------------------------------------------------------------------------
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}


//----------------------------------------------------------------------------------------
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

//----------------------------------------------------------------------------------------

$filename = 'Annotation data - Sheet1.csv';

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
		
			//print_r($obj);	
			
			// convert to annotation
			
			if (isset($obj->{'Body URI'}) && isset($obj->{'Target URI'}))
			{
				$doc = new stdclass;
				$doc->{'@context'} = $context ;
				$doc->{'@graph'} = array();
			
				$annotation = new stdclass;

				$annotation->{'@id'} = "urn:uuid:" . gen_uuid();
				$annotation->{'@type'} = 'Annotation';
			
				// Body 
				$annotation->body = $obj->{'Body URI'};
			
			
				// Target 
				$annotation->target = new stdclass;
				$annotation->target->{'@id'} = $annotation->{'@id'} . '#target';
				$annotation->target->canonical = $obj->{'Target URI'};
				$annotation->target->{'http://schema.org/name'} = $obj->{'Target name'};
				
				$doc->{'@graph'}[] = $annotation;
				
				$x = new stdclass;
				$x->{'@id'} = $annotation->target->canonical;
				$x->{'schema:name'} = $obj->{'Target name'};
				
				$doc->{'@graph'}[] = $x;
								
				//print_r($doc);
				
				$normalized = jsonld_normalize($doc, array('algorithm' => 'URDNA2015', 'format' => 'application/nquads'));
				
				echo $normalized . "\n";

			}
			
			
			
			
			
		}
	}	
	$row_count++;
}
?>

