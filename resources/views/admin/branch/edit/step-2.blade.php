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
    <span class="badge badge-warning">Step 2 of 3</span>
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
            <select name="regency_id" id="regency_id" class="form-control">
                <option value="{{$branch->regency_id}}">{{$branch->Regency->name}}</option>
            </select>
            @include('layouts.inputError', ['errorName' => 'regency_id'])
        </div>
    </div>
</div>
<div class="form-group">
    <label for="address">Address (*)</label>
    <textarea name="address" id="" cols="" rows="" class="form-control @error('address') is-invalid @enderror">{{old('address') ?: $branch->address}}</textarea>
    @include('layouts.inputError', ['errorName' => 'address'])
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="lat">Lat (*)</label>
            <input name="lat" id="latInput" type="text" class="form-control @error('lat') is-invalid @enderror" value="{{old('lat') ?: $branch->lat}}">
            @include('layouts.inputError', ['errorName' => 'lat'])
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="long">Long (*)</label>
            <input name="long" id="lngInput" type="text" class="form-control @error('long') is-invalid @enderror" value="{{old('long') ?: $branch->long}}">
            @include('layouts.inputError', ['errorName' => 'long'])
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" id="map-text" class="text-primary" onclick="initMaps()">Click here to show the maps</label>
    <br>
    <div id="map"></div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            const province_idOldValue = '{{ old('province_id') ?: $branch->Regency->province_id }}';
            
            if(province_idOldValue !== '') {
                $('#province_id').val(province_idOldValue);
            }

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
                    $('#latInput').val(coord.lat)
                    $('#lngInput').val(coord.lng)
            });
            }
            function addMarkersToMap(map) {
                var parisMarker = new H.map.Marker({lat: {{ $branch->lat ?: -6.175392 }}, lng: {{ $branch->long ?: 106.827153 }}});
                map.addObject(parisMarker);
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
            center: {lat: {{ $branch->lat }}, lng: {{ $branch->long }}},
            zoom: 17,
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
            addMarkersToMap(map);
            // Create the default UI:
            var ui = H.ui.UI.createDefault(map, defaultLayers);
            ui.getControl('zoom').setEnabled(true)
        }
	</script>
@endpush