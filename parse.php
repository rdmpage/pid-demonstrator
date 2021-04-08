<?php

// Parse CSV data

error_reporting(E_ALL);


//----------------------------------------------------------------------------------------
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

//----------------------------------------------------------------------------------------


$map = array();


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
			
			// source of link
			if (!isset($map[$obj->{$headings[0]}]))
			{
				$item = new stdclass;
				$item->linkUrls = array();
				
				if (isset($obj->{$headings[2]}))
				{
					$item->name = $obj->{$headings[2]};
				}

				if (isset($obj->{$headings[3]}))
				{
					$item->imageUrl = $obj->{$headings[3]};
				}
				
				$map[$obj->{$headings[0]}] = $item;

			}
			
			$map[$obj->{$headings[0]}]->linkUrls[] = $obj->{$headings[1]};
			
			// target of link
			if (!isset($map[$obj->{$headings[1]}]))
			{
				$item = new stdclass;
				$item->linkUrls = array();
								
				$map[$obj->{$headings[1]}] = $item;

			}			
			
			$map[$obj->{$headings[1]}]->linkUrls[] = $obj->{$headings[0]};
			
			//$map[$obj->{'Thing (e.g., collection object)'}]->links[] = $obj->{'Thing (e.g., collection object)'}
			
			
			

			
		}
	}	
	$row_count++;
}

foreach ($map as $k => $v)
{
	$map[$k]->linkImages = array();
	
	foreach ($map[$k]->linkUrls as $link)
	{
		if (isset($map[$link]->imageUrl))
		{
			$map[$k]->linkImages[$link] = $map[$link]->imageUrl;
		}
	
	}
}

// print_r($map);

echo json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);



?>

