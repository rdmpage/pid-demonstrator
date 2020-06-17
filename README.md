# Persistent Identifier (PID) demonstrator

Persistent identifiers demonstrator for [Towards a National Collection - HeritagePIDs](https://tanc-ahrc.github.io/HeritagePIDs/).

## Configuration

When developing locally, put “secrets” in env.php (which is not in the Github repo). When running on Heroku (or elsewhere) add secret values as environmental parameters:

Key | Value
-- | --
HYPOTHESIS_API_TOKEN | 6879-06f…
BLAZEGRAPH_URL | http://167…

## Annotations

Discuss that we are concerned with a subset of annotations, namely those where both body and target are external entities that have stable(ish) identifiers.

## Displaying annotations

There are at least two problems to solve here. The first is displaying the actual annotations in situ in the document being annotated, as well as enabling them to be created and edit. This is the problem hypothesis.is solves.

The second problem arises if we are using annotations to represent links between two entities, such as a specimen and a publication that mentions that specimen. Ideally we should be able to display the annotation on the web pages for BOTH entities. 

One way to do this is have a bookmarklet that injects HTML into the web page for an entity, and displays annotations linked to that entity.


## Tasks

### Multiple representations

Annotations can be attached to multiple representations of the same thing, and hypothes.is doesn’t always record the DOI of a paper. Therefore we will need to call a service to convert a URL to an identifier.

### Convert text to PID

Selected text need to be parsed and converted to an identifier, need service such as one that converts specimen code to a PID.

### Add annotations to triple store

### Fetch annotations from annotation feed

Hypothes.is feed is 

```https://hypothes.is/stream.rss?user=<username>```

### Fetch annotations related to content

Need to be able to fetch annotations using source and body identifiers. For example, given a paper that is the ```source``` for one or more annotations, what are those annotations? Given specimen that is the body of an annotation, what papers refer to that specimen?

### Fetch annotations as user changes view in document

User can scroll through a document, so we need ways to track that movement so we can display relevant annotations. For example, the basic unit of BHL is a scanned physical book, such as a journal volume. Each page has its own unique identifier (the BHL pageID), which makes it natural to link annotations to that PageID. However, the page being viewed by the user can change as they scroll through the document, so we need a mechanism for determining which page the reader is currently viewing so that we can display the appropriate annotation.

In the bookmarklet I use the MutationObserver interface to track whether the Page been viewed has changed, then query the annotation store for any annotations for that page.

(What do we do with PDFs?)


## Examples of PIDs and data sources

Institution | Data type | PID | RDF? | URL in meta
-- | -- | -- | -- | -- 
NHM | specimen | https://data.nhm.ac.uk/object/ + UUID | yes (extension) | no
RBGE | specimen | http://data.rbge.org.uk/herb/ + barcode | yes (content negotiation) | no
KEW | specimen | http://specimens.kew.org/herbarium/ + barcode |  yes (content negotiation) | no


### NHM

NHM serves RDF in triples and turtle, e.g. https://data.nhm.ac.uk/object/31a84c68-6295-4e5b-aa0a-5c2844f1fb50.n3 and https://data.nhm.ac.uk/object/31a84c68-6295-4e5b-aa0a-5c2844f1fb50.ttl RDF is flat Darwin Core.

### RBGE 

RBGE serves RDF (flat Darwin Core) that includes links to IIIF.

### Kew

Kew serves RDF (flat Darwin Core)

### National Gallery

https://data.ng-london.org.uk/0F6J-0001-0000-0000.json

NG6195 
- https://www.wikidata.org/wiki/Q24939442
- https://books.google.co.uk/books?id=SrWbDwAAQBAJ&pg=PA102&lpg=PA102&dq=NG6195&source=bl&ots=u0vsLAN8s1&sig=ACfU3U2Ss5XwNcEONQW32Be0Z-iHQlxeCw&hl=en&sa=X&ved=2ahUKEwjmiKe0hYnqAhW0mFwKHTKqA4QQ6AEwDnoECAoQAQ#v=onepage&q=NG6195&f=false
- https://www.nationalgalleryimages.co.uk/imagedetails.aspx?q=NG6195&ng=NG6195&view=lg&frm=1

### Science Museum

https://collection.sciencemuseumgroup.org.uk/api/objects/co8084947

- https://collection.sciencemuseumgroup.org.uk/objects/co8084947/stephensons-rocket-steam-locomotive




## Recommendations for PID providers

These recommendations are w.r.t. to making projects like this doable.

### Make identifier discoverable within web page for item

The web page for an entity should include the persistent identifier in a machine readable way. For example, academic publishers typically include the DOI for an article in a ```meta``` tag.

1. Include persistent identifier in HEAD of web page
1. Use standard tag, e.g. canonical link, og:url, etc.
1. Ideally PID should be resolvable by both browser and machine, e.g. by supporting content negotiation



### Use consistent data descriptions

e.g. do museums serving RDF all use same approach?


