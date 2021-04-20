<?php

require_once (dirname(__FILE__) . '/config.inc.php');

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<style>
		body {
			margin: 0px;
			padding:40px;
			font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
			font-size:1em;
			line-height: 1.5;
		}
	</style>
	<title><?php echo $config['site_name']; ?></title>
	<script type="text/javascript" src="https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js"></script>

</head>
<body>
	<h1>PID Demonstrator</h1>
	
	<p>A proof of concept by Rod Page as part of the <a href="https://tanc-ahrc.github.io/HeritagePIDs/">Towards a National Collection - HeritagePIDs</a> project. Source on <a href="https://github.com/rdmpage/pid-demonstrator">GitHub</a>.</p>
	
	<p>The PID Demonstrator is a simple tool to explore links between collection objects and work based on those objects, using persistent identifiers (PIDs) as the glue.</p>
	
	<h2>Presentations</h2>
	<ul>
	<li><a href="demo">First demonstration</a> (see also <a href="https://iphylo.blogspot.com/2020/07/diddling-with-semantic-data-linking.html">Diddling with semantic data: linking natural history collections to the scientific literature</a> and <a href="https://iphylo.blogspot.com/2020/07/persistent-identifiers-demo-and-rant.html">Persistent Identifiers: A demo and a rant</a>). This version was based on Semantic Web technology (e.g., annotations and triple stores).
	
	<div style="text-align:center;padding:20px;">
	<iframe width="560" height="315" src="https://www.youtube.com/embed/yrlskGRFKps" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>
	
	</li>
	
	<li>Second demonstration (based on spreadsheet listing PIDs for collection objects and work based on those objects).
	
	</ul>
	
	<h2>Rationale</h2>
	
<h3>Citation graph as inspiration</h3>
		
		<p>Academic articles have a Digital Object Identifiers (DOI) (e.g., <b>10.1371/journal.pone.0053873</b>), as do the works cited by that article (mostly). This makes it easy to have a network of interconnected papers (linked by the citation relationship).</p>
		
		<ol>
			<li>Reader can trace the <b>provenance</b> of ideas, data, quotes, etc. (follow the DOIs)</li>
			<li>We get <b>metrics</b> of use (e.g., number of citations of that paper)</li>
			<li>Enable <b>third party ecosystem</b> that link stuff to the DOI (e.g. altmetric "donut")
			
			<div style="padding:10px;" data-badge-details="right" data-badge-type="medium-donut" data-doi="10.1371/journal.pone.0053873" data-hide-no-mentions="true" class="altmetric-embed"></div>

			</li>
		</ol>	
		
	<h3>Vision: A network of collection objects and works that use them</h3>
	
	<p>
		The citation graph is nice, but it is limited to links between the same sort of things (academic papers). A <b>collection objects graph</b> would connect collection objects (specimens, objects, scanned images, etc.) with work based on those objects (e.g., academic papers, analysis of DNA or other properties, social media, Wikipedia, etc.). The PID demonstrator takes a small, manually assembled collection object graph and uses it to enrich the experience of visiting web sites for things that are in that graph.
	</p>		
	
	<h2>See for yourself</h2>
	
	<h3>Step 1</h3>
	
	<p>
		<a onclick="alert('Please drag this link to your bookmarks bar.'); return false;" href="javascript:(function(a){%20a=document.createElement('script');a.type='text/javascript';a.src='<?php echo $config['web_server'] . $config['web_root']; ?>js/script.js?x='+Date.now();document.getElementsByTagName('body')[0].appendChild(a);})();">Annotate It!</a>	
		<span>← Drag this to your bookmarks bar</span>
	</p>	
	
	<h3>Step 2</h3>
	
	<p>Visit some sites that the PID demonstrator knows about (see below), then click on the "Annotate It!" bookmark and see the links.</p>

	<h4>Collection objects</h4>
	<ul>
		<li><a href="http://access.bl.uk/item/viewer/ark:/81055/vdc_100024135011.0x000001" target="_new">Italy, a poem (British Library)</a></li>
		<li><a href="https://data.rbge.org.uk/herb/E00179300" target="_new"><i>Begonia albo-coccinea</i> E00179300 (Royal Botanic Gardens, Edinburgh)</a></li>
		<li><a href="https://data.nhm.ac.uk/object/adbba503-eef1-44de-b7d2-fddc8b4e6275" target="_new"><i>Begonia floccifera</i> BM000944668 (Natural History Museum)</a></li>

		<li><a href="http://access.bl.uk/item/viewer/ark:/81055/vdc_100025566860.0x000001" target="_new">Catiline: a tragedy in five acts. With other poems(British Library)</a></li>
		
		<li><a href="http://n2t.net/ark:/65665/3715cc0ec-8f02-44ba-8bd7-00371fae6ab9" target="_new">Stick Navigation Chart</a></li>
	</ul>
	
	<h4>Work that cites Collection objects</h4>
	<ul>
		<li><a href="https://doi.org/10.4000/cve.6886" target="_new">Proust’s Ruskin: From Illustration to Illumination</a></li>
		<li><a href="https://doi.org/10.1017/S0960428619000349" target="_new">A NEW SECTION (BEGONIA SECT. FLOCCIFERAE SECT. NOV.) AND TWO NEW SPECIES IN BEGONIACEAE FROM THE WESTERN GHATS OF INDIA</a></li>
		<li><a href="https://www.biodiversitylibrary.org/page/56312118" target="_new">European Journal of Taxonomy in BHL</a></li>
		
		
		<li><a href="https://books.google.com/books?id=URKEDwAAQBAJ" target="_new">Reviving Cicero in Drama (Google Books)</a></li>
		<li><a href="https://doi.org/10.1016/j.jcz.2020.06.006" target="_new">The killer flies Coenosia Meigen (Diptera: Muscidae) of southern South America: Resolving the taxonomic puzzle of Coenosia inaequalis Malloch, 1934</a></li>
	</ul>	

</body>
</html>


