# Annotations from a spreadsheet

Given a CSV spreadsheet we can create a list of annotations. An annotation is simply a pair of things that we link, both identified by URIs (ideally those URIs are persistent identifiers that resolve to RDF). 

If we use the W3C annotation model then we need to distinguish between body and target. If we are consuming a source of these annotations (e.g., https://hypothes.is) then which is the target and which is the body are defined for us by that annotation. If not, then we have to decide which is the target and which is the body. For example, if we are looking at an article that lists specimens then there target is that article, and each specimen is a body.

Other possible models would be a more generic one expressing relationships, say as a nanopub where we have a triple linking the two URIs, accompanied by metadata on the source of the triple.

Column heading | Comments
--|--
ID | Option id for the annotation
Body URI | URI for the body of the annotation, typically a specimen
Body name | Name of the body
Target URI | URI for target, the thing being annotated, typically an article
Target name | Name of the target
Notes | Optional notes on this annotation