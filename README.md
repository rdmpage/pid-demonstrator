# Persistent Identifier (PID) demonstrator

Persistent identifiers demonstrator for [Towards a National Collection - HeritagePIDs](https://tanc-ahrc.github.io/HeritagePIDs/).

## Configuration

When developing locally, put “secrets” in env.php (which is not in the Github repo). When running on Heroku (or elsewhere) add secret values as environmental parameters:

Key | Value
-- | --
HYPOTHESIS_API_TOKEN | 6879-06f…
BLAZEGRAPH_URL | http://167…


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


## Recommendations for PID providers

These recommendations are w.r.t. to making projects like this doable.

### Make identifier discoverable within web page for item

The web page for an entity should include the persistent identifier in a machine readable way. For example, academic publishers typically include the DOI for an article in a ```meta``` tag.

### Use consistent data descriptions

e.g. do museums serving RDF all use same approach?


