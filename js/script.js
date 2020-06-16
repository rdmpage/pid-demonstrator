// Bookmarklet

// http://code.tutsplus.com/tutorials/create-bookmarklets-the-right-way--net-18154

// http://stackoverflow.com/questions/5281007/bookmarklets-which-creates-an-overlay-on-page

var observer = null;

var use_citationjs = false;

if (use_citationjs) {

  // Create a script tag to load citation.js
  script = document.createElement('script');
  script.src = 'https://cdn.jsdelivr.net/npm/citation-js';
  script.onload = rdmp_init;
  document.body.appendChild(script);
}
else {
  rdmp_init();
}

function rdmp_init() {
  //--------------------------------------------------------------------------------------------------
  // http://code.tutsplus.com/tutorials/create-bookmarklets-the-right-way--net-18154
  // Test for presence of jQuery, if not, add it
  if (!($ = window.jQuery)) { // typeof jQuery=='undefined' works too
    // Create a script tag to load the bookmarklet
    script = document.createElement('script');
    script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js';
    script.onload = releasetheKraken;
    document.body.appendChild(script);
  }
  else {
    releasetheKraken();
  }
}

//--------------------------------------------------------------------------------------------------
function rdmp_close(id) {
  $('#' + id).remove();
}

//--------------------------------------------------------------------------------------------------
function releasetheKraken() {
  // The Kraken has been released, master!
  // Yes, I'm being childish. Place your code here 
  //alert('kraken');

  if (use_citationjs) {
    const Cite = require('citation-js');
  }

  var e = null;
  if (!$('#rdmpannotate').length) {

    // create the element:
    var e = $('<div id="rdmpannotate"></div>');

    // append it to the body:
    $('body').append(e);

    // style it:
    e.css({
      position: 'fixed',
      top: '0px',
      right: '0px',
      width: '300px',
      height: '100vh',
      padding: '20px',
      backgroundColor: "#FFFFCC",
      color: 'black',
      'text-align': 'left',
      'font-size': '12px',
      'font-weight': 'normal',
      'font-family': '\'Helvetica Neue\', Helvetica, Arial, sans-serif',
      'box-shadow': '-5px 5px 5px 0px rgba(50, 50, 50, 0.3)',
      'z-index': '200000'
    });

    $('#rdmpannotate').data("top", $('#rdmpannotate').offset().top);
  }
  else {
    e = $('#rdmpannotate');
  }

  // ×

  var html = '<span style="float:right;" onclick="rdmp_close(\'rdmpannotate\')">Close [x]</span>';
  html += '<div style="width:200px;font-size:120%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' +
    '<span style="font-weight:bold;">' + window.document.title + '</span>' + '</div>';
  e.html(html);

  // Get identifier(s) from page elements or URL
  // http://stackoverflow.com/questions/7524585/how-do-i-get-the-information-from-a-meta-tag-with-javascript

  var guid = {
    namespace: null,
    identifier: null
  };

  var metas = document.getElementsByTagName('meta');

  for (i = 0; i < metas.length; i++) {

    // Google Scholar tags
    if (metas[i].getAttribute("name") == "citation_doi") {
      guid.namespace = 'doi';
      guid.identifier = metas[i].getAttribute("content");
    }

    // Dublin Core
    // Taylor and Francis
    if (metas[i].getAttribute("name") == "dc.Identifier") {
      if (metas[i].getAttribute("scheme") == "doi") {
        guid.namespace = 'doi';
        guid.identifier = metas[i].getAttribute("content");
      }
    }

    // Ingenta
    if (metas[i].getAttribute("name") == "DC.identifier") {
      if (metas[i].getAttribute("scheme") == "URI") {
        if (metas[i].getAttribute("content").match(/info:doi\//)) {
          guid.namespace = 'doi';
          guid.identifier = metas[i].getAttribute("content");
          guid.identifier = guid.identifier.replace(/info:doi\//, "");
        }
      }
    }

    // OJS (e.g. EJT)    
    if (metas[i].getAttribute("name") == "DC.Identifier.DOI") {
      guid.namespace = 'doi';
      guid.identifier = metas[i].getAttribute("content");
    }

    // BHL
    if (metas[i].getAttribute("name") == "DC.identifier.URI") {
      var m = metas[i].getAttribute("content").match(/https?:\/\/(?:www.)?biodiversitylibrary.org\/item\/(\d+)/);
      if (m) {
        guid.namespace = 'bhl';
        guid.identifier = m[1];
      }
    }

    // Facebook meta tags
    if (!guid.namespace) {
      if (metas[i].getAttribute("property") == "og:url") {
        var url = metas[i].getAttribute("content");

        // GBIF
        if (url.match(/gbif.org\/occurrence/)) {
          guid.namespace = 'occurrence';
          guid.identifier = url;
          guid.identifier = guid.identifier.replace(/https?:\/\/(www\.)?gbif.org\/occurrence\//, '');
        }

        // ALA
        if (url.match(/bie.ala.org.au\/species\/urn:lsid/)) {
          guid.namespace = 'ala';
          guid.identifier = url;
          guid.identifier = guid.identifier.replace(/https?:\/\/bie.ala.org.au\/species\//, '');
        }

      }
    }

  }

  // No GUID from meta tags, try other rules
  if (!guid.namespace) {

    // canonical link

    // <link rel="canonical" href="https://www.jstor.org/stable/24532712">
    var elements = document.querySelectorAll('link[rel="canonical"]');
    for (i = 0; i < elements.length; i++) {
      guid.namespace = 'uri';
      guid.identifier = elements[i].getAttribute("href");
    }

  }

  // Still nothing, let's get more specific (and dive into the HTML)
  if (!guid.namespace) {

    // RBGE
    var elements = document.querySelectorAll('[alt="Stable URI"]');
    for (i = 0; i < elements.length; i++) {
      guid.namespace = 'uri';
      guid.identifier = elements[i].getAttribute("href");

    }
 

  }
  
  if (!guid.namespace) {

    // IPNI
    var elements = document.querySelectorAll('dl dd');
    for (i = 0; i < elements.length; i++) {
      var text = elements[i].textContent;
      if (text.match(/urn:lsid/)) {
      	guid.namespace = 'ipni';
      	guid.identifier = text;
      }

    }

  }  
  
  if (!guid.namespace) {

    // GenBank
    var elements = document.querySelectorAll('p[class="itemid"]');
    for (i = 0; i < elements.length; i++) {
      var text = elements[i].textContent;
      var m = text.match(/GenBank:\s+([A-Z]+\d+)(\.\d+)$/);
      if (m) {
      	guid.namespace = 'genbank';
      	guid.identifier = m[1];
      }

    }

  }    



  // Still no GUID, use page URL
  if (!guid.namespace) {
    // Last resort use URL...
    // var pattern = /gbif.org\/occurrence\/(\d+)/;	
    // var m  = pattern.exec(window.location.href);
  }

  // Now we (might) have an identifier, what can we do with it?

  // 1. display entity
  // 2. List of linked entities (data feed)

  if (guid.namespace) {
    switch (guid.namespace) {

      case 'bhl':
        // BHL pages can change as user browses content, so we use a MutationObserver
        // to track current PageID, so that we could then display annotations relevant
        // to the page being displayed.

        e.html(e.html() + JSON.stringify(guid));

        var html = '<div id="bhl_page"></a>';
        e.html(e.html() + '<br />' + html);

        var currentpageURL = document.querySelector('[id=currentpageURL]');

        document.getElementById('bhl_page').innerHTML = currentpageURL;

        // https://stackoverflow.com/questions/41424989/javascript-listen-for-attribute-change
        observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.type == "attributes") {
              var currentpageURL = document.querySelector('[id=currentpageURL]');
              document.getElementById('bhl_page').innerHTML = currentpageURL;
              console.log("attributes changed")
            }
          });
        });

        observer.observe(currentpageURL, {
          attributes: true //configure it to listen to attribute changes
        });

        break;

      case 'doi':
        // e.html(e.html() + '<div>doi:' + guid.identifier + '</div>');

        // display formatted citation (helps validate that we've got the DOI)
        $.ajax({
          type: "GET",
          url: '//api.crossref.org/v1/works/' +
            encodeURIComponent(guid.identifier),
          success: function(data) {

            var html = '<div style="padding:20px;">';
            html += data;
            html += '</div>';

            if (use_citationjs) {
              var formatter = new Cite(data.message);

              e.html(e.html() + formatter.format('bibliography', {
                format: 'html',
                template: 'apa',
                lang: 'en'
              }));
            }
            else {
              e.html(e.html() + JSON.stringify(data));
            }

          }
        });

        // annotations?

        break;

      case 'occurrence':
        $.getJSON('//api.gbif.org/v1/occurrence/' + guid.identifier + '?callback=?',
          function(data) {
            if (data.key == guid.identifier) {
              var html = '<div style="text-align:left;">';
              html += '<div>' + data.institutionCode + ' ' + data.catalogNumber + '</div>';

              if (data.decimalLongitude && data.decimalLatitude) {
                html += '<span>[' + data.decimalLatitude + ',' + data.decimalLongitude + ']</span>' + '<br />';
                html += '<img width="100" src="https://api.mapbox.com/styles/v1/mapbox/light-v10/static/pin-s(' + data.decimalLongitude + ',' + data.decimalLatitude + ')/' + data.decimalLongitude + ',' + data.decimalLatitude + ',2/100x100@2x?access_token=pk.eyJ1IjoicmRtcGFnZSIsImEiOiJjajJrdmJzbW8wMDAxMnduejJvcmEza2k4In0.bpLlN9O6DylOJyACE8IteA">';
              }

              if (data.media) {
                for (var i in data.media) {
                  html += '<img src="http://exeg5le.cloudimg.io/s/height/100/' + data.media[i].identifier + '" height="100">';
                }
              }

              html += '</div>';
              e.html(e.html() + html);
            }
          });
        break;

      default:
        e.html(e.html() + JSON.stringify(guid));
        break;
    }


  }

  /*
	// GBIF occurrence
	var pattern = /gbif.org\/occurrence\/(\d+)/;
	
	hit = pattern.exec(window.location.href);
	
	if (hit) {
		$.getJSON('http://api.gbif.org/v0.9/occurrence/' + hit[1] + '?callback=?',
			function(data){
				if (data.key == hit[1]) {
					var html = '<div style="text-align:left;">';
					html += '<div>' + data.institutionCode + ' ' + data.catalogNumber + '</div>';
					html += '<span>[' + data.decimalLatitude + ',' + data.decimalLongitude + ']</span>';
					if (data.decimalLongitude && data.decimalLatitude) {
						html += '<img src="http://maps.googleapis.com/maps/api/staticmap?' 
							+ 'size=300x100&zoom=6&maptype=terrain&markers=size:mid|' 
							+  data.decimalLatitude + ',' + data.decimalLongitude + '&sensor=false'
							+ '" />';
					}
					html += '</div>';
					e.html(e.html() + html);
				}
			});
	}
	
	*/

}

//----------------------------------------------------------------------------------------
/* Can't use jquery at this point because it might not have been loaded yet */
// https://stackoverflow.com/a/17494943/9684

var startProductBarPos = -1;

window.onscroll = function() {
  var bar = document.getElementById('rdmpannotate');
  if (startProductBarPos < 0) startProductBarPos = findPosY(bar);

  if (pageYOffset > startProductBarPos) {
    bar.style.position = 'fixed';
    bar.style.top = 0;
  }
  else {
    bar.style.position = 'fixed';
  }

};

function findPosY(obj) {
  var curtop = 0;
  if (typeof(obj.offsetParent) != 'undefined' && obj.offsetParent) {
    while (obj.offsetParent) {
      curtop += obj.offsetTop;
      obj = obj.offsetParent;
    }
    curtop += obj.offsetTop;
  }
  else if (obj.y)
    curtop += obj.y;
  return curtop;
}