<?php

if (0)
{
	// https://www.w3.org/ns/anno.jsonld
	$json = file_get_contents(dirname(__FILE__) . '/anno.jsonld');
	$context = json_decode($json);
}
else
{

	// Context for Annotations

	$context = new stdclass;
	$context->oa	  	= "http://www.w3.org/ns/oa#";
	$context->dcterms 	= "http://purl.org/dc/terms/";
	$context->rdf     	= "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
	$context->xsd		= "http://www.w3.org/2001/XMLSchema#";
	$context->schema	= "http://schema.org/";
	

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

	$context->canonical = new stdclass;
	$context->canonical->{'@type'} = '@id';
	$context->canonical->{'@id'} = 'oa:canonical';


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

	// Note use of "@vocab" so that URI terms such as oa:identifying come out correctly
	$context->motivation = new stdclass;
	$context->motivation->{'@type'} = '@vocab';
	$context->motivation->{'@id'} = 'oa:motivatedBy';

	
	$context->identifying = new stdclass;
	$context->identifying->{'@type'} = '@id';
	$context->identifying->{'@id'} = 'http://www.w3.org/ns/oa#identifying';

	// simple terms 

	$context->exact  = 'oa:exact';
	$context->prefix = 'oa:prefix';
	$context->suffix = 'oa:suffix';

	$context->start  = 'oa:start';
	$context->end    = 'oa:end';

	$context->TextualBody    = 'oa:TextualBody';

	$context->value  = 'rdf:value';

	// other vocabs
	$context->creator = new stdclass;
	$context->creator->{'@type'} = '@id';
	$context->creator->{'@id'} = 'dcterms:creator';

	$context->created = new stdclass;
	$context->created->{'@type'} = 'xsd:dateTime';
	$context->created->{'@id'} = 'dcterms:created';

	$context->modified = new stdclass;
	$context->modified->{'@type'} = 'xsd:dateTime';
	$context->modified->{'@id'} = 'dcterms:modified';
	
	// images
	$context->thumbnailUrl = new stdclass;
	$context->thumbnailUrl->{'@type'} = '@id';
	$context->thumbnailUrl->{'@container'} = "@set";

	$context->thumbnailUrl->{'@id'} = 'schema:thumbnailUrl';
	
	
	// domain specific
	$context->dwc		= "http://rs.tdwg.org/dwc/terms/";

}

    



?>
