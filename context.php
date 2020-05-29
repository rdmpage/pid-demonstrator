<?php

// Context for Annotations

$context = new stdclass;
$context->oa	= "http://www.w3.org/ns/oa#";
$context->rdf = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";

// so things are easy to work with in clients we rewrite @id and @type

$context->id = '@id';
$context->type = '@type';

$context->Annotation = new stdclass;
$context->Annotation->{'@type'} = '@id';
$context->Annotation->{'@id'} = 'oa:Annotation';		

$context->body = new stdclass;
$context->body->{'@type'} = '@id';
$context->body->{'@id'} = 'oa:hasBody';	

// targets and sources	

$context->target = new stdclass;
$context->target->{'@type'} = '@id';
$context->target->{'@id'} = 'oa:hasTarget';

$context->source = new stdclass;
$context->source->{'@type'} = '@id';
$context->source->{'@id'} = 'oa:hasSource';

// selectors

$context->selector = new stdclass;
$context->selector->{'@type'} = '@id';
$context->selector->{'@id'} = 'oa:hasSelector';

$context->startSelector = new stdclass;
$context->startSelector->{'@type'} = '@id';
$context->startSelector->{'@id'} = 'oa:hasStartSelector';

$context->endSelector = new stdclass;
$context->endSelector->{'@type'} = '@id';
$context->endSelector->{'@id'} = 'oa:hasEndSelector';

$context->RangeSelector = new stdclass;
$context->RangeSelector->{'@type'} = '@id';
$context->RangeSelector->{'@id'} = 'oa:RangeSelector';

$context->TextQuoteSelector = new stdclass;
$context->TextQuoteSelector->{'@type'} = '@id';
$context->TextQuoteSelector->{'@id'} = 'oa:TextQuoteSelector';

$context->TextPositionSelector = new stdclass;
$context->TextPositionSelector->{'@type'} = '@id';
$context->TextPositionSelector->{'@id'} = 'oa:TextPositionSelector';

$context->XPathSelector = new stdclass;
$context->XPathSelector->{'@type'} = '@id';
$context->XPathSelector->{'@id'} = 'oa:XPathSelector';


// simple terms 

$context->exact  = 'oa:exact';
$context->prefix = 'oa:prefix';
$context->suffix = 'oa:suffix';

$context->start  = 'oa:start';
$context->end    = 'oa:end';

$context->value  = 'rdf:value';

?>
