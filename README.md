# Persistent Identifier (PID) demonstrator

Persistent identifiers demonstrator for [Towards a National Collection - HeritagePIDs](https://tanc-ahrc.github.io/HeritagePIDs/).


## Tasks

### Multiple representations

Annotations can be attached to multiple representations of the same thing, and hypothes.is doesn’t always record the DOI of a paper. Therefore we will need to call a service to convert a URL to an identifier.

### Convert text to PID

Selected text need to be parsed and converted to an identifier, need service such as one that converts specimen code to a PID.

### Add annotations to triple store

### Fetch annotations from annotation feed

Hypothes.is feed is 

```https://hypothes.is/stream.rss?user=<username>```




## Recommendations for PID providers

These recommendations are w.r.t. to making projects like this doable.

### Make identifier discoverable within web page for item

The web page for an entity should include the persistent identifier in a machine readable way. For example, academic publishers typically include the DOI for an article in a ```meta``` tag.


