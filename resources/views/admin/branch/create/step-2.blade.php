@push('css')
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
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
    <script src='https://developer.here.com/javascript/src/iframeheight.js'></script>
@endpush
<h5 class="my-3">
    <span class="badge badge-primary">Step 2 of 3</span>
</h5>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="province_id">Province (*)</label>
            <select name="province_id" id="province_id" class="form-control @error('province_id') is-invalid @enderror">
                @foreach ($provinces as $province)
                    <option value="{{$province->id}}">{{$province->name}}</option>
                @endforeach
            </select>
            @include('layouts.inputError', ['errorName' => 'province_id'])
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="regency_id">City (*)</label>
            <select name="regency_id" id="regency_id" class="form-control" required>
                <option value="">Choose City</option>
            </select>
            @include('layouts.inputError', ['errorName' => 'regency_id'])
        </div>
    </div>
</div>
<div class="form-group">
    <label for="address">Address (*)</label>
    <textarea name="address" cols="" rows="" class="form-control @error('address') is-invalid @enderror">{{old('address')}}</textarea>
    @include('layouts.inputError', ['errorName' => 'address'])
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="lat">Lat (*)</label>
            <input name="lat" id="latInput" type="text" class="form-control @error('lat') is-invalid @enderror" value="{{old('lat')}}">
            @include('layouts.inputError', ['errorName' => 'lat'])
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="long">Long (*)</label>
            <input name="long" id="lngInput" type="text" class="form-control @error('long') is-invalid @enderror" value="{{old('long')}}">
            @include('layouts.inputError', ['errorName' => 'long'])
        </div>
    </div>
</div>
<div class="form-group">
    <label id="map-text" for="name" class="text-primary" onclick="initMaps()" style="cursor: pointer">Click here to show the maps</label>
    <br>
    <div id="map"></div>
</div>
@push('js')
    <script>
        $(document).ready(function() {
            $('#province_id').change(() => {
                let provinceId = $('#province_id').val()
                fetch(`/api/regency/${provinceId}`)
                    .then(res => res.json())
                    .then(data => {
                        $('#regency_id option').remove()
                        data.data.forEach(regency => {
                            $('#regency_id')
                                .append($("<option></option>")
                                .attr("value", regency.id)
                                .text(regency.name)); 
                        });
                    })
                    .catch(err => console.log(err))
            })
        });
    </script>
    <script>
		function initMaps() {
            $('#map-text').hide()
            /**
            * Calculates and displays the address details of  425 Randolph St, Chicago, IL
            * based on a structured input
            *
            *
            * A full list of available request parameters can be found in the Geocoder API documentation.
            * see: http://developer.here.com/rest-apis/documentation/geocoder/topics/resource-geocode.html
            *
            * @param   {H.service.Platform} platform    A stub class to access HERE services
            */
            function geocode(platform) {
            let city = $('#regency_id option:selected').text().split(' ')[1]
            var geocoder = platform.getGeocodingService(),
                geocodingParameters = {
                    city,
                    country: 'indonesia',
                    jsonattributes : 1
                };

            geocoder.geocode(
                geocodingParameters,
                onSuccess,
                onError
            );
            }
            /**
            * This function will be called once the Geocoder REST API provides a response
            * @param  {Object} result          A JSONP object representing the  location(s) found.
            *
            * see: http://developer.here.com/rest-apis/documentation/geocoder/topics/resource-type-response-geocode.html
            */
            function onSuccess(result) {
            var locations = result.response.view[0].result;
            /*
            * The styling of the geocoding response on the map is entirely under the developer's control.
            * A representitive styling can be found the full JS + HTML code of this example
            * in the functions below:
            */
            addLocationsToMap(locations);
            }

            /**
            * This function will be called if a communication error occurs during the JSON-P request
            * @param  {Object} error  The error message received.
            */
            function onError(error) {
            alert('Can\'t reach the remote server');
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

            //Step 2: initialize a map - this map is centered over Jakarta
            var map = new H.Map(document.getElementById('map'),
            defaultLayers.vector.normal.map,{
            center: {lat: -6.175392, lng: 106.827153},
            zoom: 17,
            pixelRatio: window.devicePixelRatio || 1
            });
            // add a resize listener to make sure that the map occupies the whole container
            window.addEventListener('resize', () => map.getViewPort().resize());

            var locationsContainer = document.getElementById('panel');

            //Step 3: make the map interactive
            // MapEvents enables the event system
            // Behavior implements default interactions for pan/zoom (also on mobile touch environments)
            var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

            // Create the default UI components
            var ui = H.ui.UI.createDefault(map, defaultLayers);

            // Hold a reference to any infobubble opened
            var bubble;

            /**
            * Opens/Closes a infobubble
            * @param  {H.geo.Point} position     The location on the map.
            * @param  {String} text              The contents of the infobubble.
            */
            function openBubble(position, text){
            if(!bubble){
                bubble =  new H.ui.InfoBubble(
                position,
                {content: text});
                ui.addBubble(bubble);
            } else {
                bubble.setPosition(position);
                bubble.setContent(text);
                bubble.open();
            }
            }

            /**
            * Creates a series of H.map.Markers for each location found, and adds it to the map.
            * @param {Object[]} locations An array of locations as received from the
            *                             H.service.GeocodingService
            */
            function addLocationsToMap(locations){
            var group = new  H.map.Group(),
                position,
                i;

            // Add a marker for each location found
            for (i = 0;  i < locations.length; i += 1) {
                position = {
                lat: locations[i].location.displayPosition.latitude,
                lng: locations[i].location.displayPosition.longitude
                };
                marker = new H.map.Marker(position);
                marker.label = locations[i].location.address.label;
                group.addObject(marker);
            }

            group.addEventListener('tap', function (evt) {
                map.setCenter(evt.target.getGeometry());
                openBubble(
                evt.target.getGeometry(), evt.target.label);
            }, false);

            // Add the locations group to the map
            map.addObject(group);
            map.getViewModel().setLookAtData({
                bounds: group.getBoundingBox(),
                zoom: 15
            });
            }

            // Now use the map as required...
            geocode(platform);
        }
	</script>
@endpush