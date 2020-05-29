<?php

// tests and demos

require_once (dirname(__FILE__) . '/hypothesis.php');

$annotation_id = 'YEez2qEAEeqgNWc0aIiyEg';

$obj = hypothesis_get_annotation($annotation_id);

//print_r($obj);


$rdf = hypothesis_annotation_to_rdf($obj);

echo $rdf;


?>


