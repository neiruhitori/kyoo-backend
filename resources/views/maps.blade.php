<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title>Calculating a Location from a Mouse Click</title>
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
    {{-- <script type="text/javascript" src='../test-credentials.js'></script> --}}
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
    <style type="text/css">
      .log {
        position: absolute;
        top: 5px;
        left: 5px;
        height: 150px;
        width: 250px;
        overflow: scroll;
        background: white;
        margin: 0;
        padding: 0;
        list-style: none;
        font-size: 12px;
      }
      .log-entry {
        padding: 5px;
        border-bottom: 1px solid #d0d9e9;
      }
      .log-entry:nth-child(odd) {
          background-color: #e1e7f1;
      }
	  #map {
			width: 95%;
			height: 450px;
			background: grey;
		}

		#panel {
			width: 100%;
			height: 400px;
		}
    </style>
  	<script src='https://developer.here.com/javascript/src/iframeheight.js'></script></head>
  <body id="markers-on-the-map">

    <div class="page-header">
        <h1>Calculating a Location from a Mouse Click</h1>
        <p>Obtain the latitude and longitude of a location within the map</p>
    </div>
    <p>This example displays a map of the world. Clicking on the map displays an alert box containing the latitude and longitude of the location pressed.</p>
    <div id="map"></div>
	<h3>Code</h3>
	<p>Current lat</p>
	<input type="text" id="lat">
	<p>Current long <span id="long">123</span></p>
    <p>The <code>tap</code> event holds the x and y screen coordinates of the location clicked. 
	  These are converted to a latitude and longitude using the <code>map.screenToGeo()</code> method.</p>
	  <script src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
    <script>
		/**
		* An event listener is added to listen to tap events on the map.
		* Clicking on the map displays an alert box containing the latitude and longitude
		* of the location pressed.
		* @param  {H.Map} map      A HERE Map instance within the application
		*/
		function setUpClickListener(map) {
		// Attach an event listener to map display
		// obtain the coordinates and display in an alert box.
		map.addEventListener('tap', function (evt) {
			var coord = map.screenToGeo(evt.currentPointer.viewportX,
					evt.currentPointer.viewportY);
			logEvent('Clicked at ' + Math.abs(coord.lat.toFixed(4)) +
				((coord.lat > 0) ? 'N' : 'S') +
				' ' + Math.abs(coord.lng.toFixed(4)) +
				((coord.lng > 0) ? 'E' : 'W'));
				console.log('lat', coord.lat)
				$('#lat').val(coord.lat)
				console.log('lng', coord.lng)
		});
		}



		/**
		* Boilerplate map initialization code starts below:
		*/

		//Step 1: initialize communication with the platform
		// In your own code, replace variable window.apikey with your own apikey
		var platform = new H.service.Platform({
		apikey: 'lr27OGV_xlkWUrjFSfHhpMKBtxL1zzi3n5tu-jOOYJ4'
		});
		var defaultLayers = platform.createDefaultLayers();

		//Step 2: initialize a map
		var map = new H.Map(document.getElementById('map'),
		defaultLayers.vector.normal.map,{
		center: {lat: -6.175392, lng: 106.827153},
		zoom: 13,
		pixelRatio: window.devicePixelRatio || 1
		});
		// add a resize listener to make sure that the map occupies the whole container
		window.addEventListener('resize', () => map.getViewPort().resize());

		//Step 3: make the map interactive
		// MapEvents enables the event system
		// Behavior implements default interactions for pan/zoom (also on mobile touch environments)
		var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

		// Step 4: create custom logging facilities
		var logContainer = document.createElement('ul');
		logContainer.className ='log';
		logContainer.innerHTML = '<li class="log-entry">Try clicking on the map</li>';
		map.getElement().appendChild(logContainer);

		// Helper for logging events
		function logEvent(str) {
		var entry = document.createElement('li');
		entry.className = 'log-entry';
		entry.textContent = str;
		logContainer.insertBefore(entry, logContainer.firstChild);
		}


		setUpClickListener(map);
	</script>
  </body>
</html>
                  