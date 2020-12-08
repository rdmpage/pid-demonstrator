// Bookmarklet

// http://code.tutsplus.com/tutorials/create-bookmarklets-the-right-way--net-18154
// http://stackoverflow.com/questions/5281007/bookmarklets-which-creates-an-overlay-on-page

var observer = null;

rdmp_init();

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
  // alert('kraken');


  var id = 'pagediv7';
  var page = document.getElementById(id);
  
  if (page) {
  
  	//alert('hi');
  	
  	var e = null;
  	
  	e = $('<div style="position:absolute;opacity:0.4;background:yellow;top:10px;left:10px;height:100px;width:100px;"></div>');
  	
  	e = document.createElement('div');
  	e.style.position = 'absolute';
  	e.style.top = '10px';
  	e.style.left = '20px';
  	e.style.width = '100px';
  	e.style.height = '50px';
  	e.style.background = 'yellow';
  	e.style.opacity = '0.5';

  	
  	/*
  	// svg
	var svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
	svg.setAttribute('xmlns','http://www.w3.org/2000/svg');
	//svg.setAttribute('id',svg_id);
	svg.setAttribute('version','1.1');
	svg.setAttribute('height',300);
	svg.setAttribute('width',300);
	page.appendChild(svg);  	
	
	//page.parentNode.insertBefore(svg, page.nextSibling);
	
		var rect = document.createElementNS('http://www.w3.org/2000/svg','rect');
		//newLine.setAttribute('id','node' + p.id);
		rect.setAttribute('width', 100);
		rect.setAttribute('height', 100);
		rect.setAttribute('style','fill:rgb(0,0,255);stroke-width:3;stroke:rgb(0,0,0)');
		svg.appendChild(rect);	
  	
  	*/

    // append it to the page:
    page.appendChild(e);
    //$('#' + id).append(e);

 
  }


}

//----------------------------------------------------------------------------------------
/* Can't use jquery at this point because it might not have been loaded yet */
// https://stackoverflow.com/a/17494943/9684

var startProductBarPos = -1;

window.onscroll = function() {
  var bar = document.getElementById('pidannotate');
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