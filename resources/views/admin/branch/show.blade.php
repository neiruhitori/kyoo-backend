@extends('layouts.app')
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
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Branch</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-4">
                                <p>Logo:</p>
                                @isset($branch->logo)
                                    <img src="{{asset('storage/'.$branch->logo)}}" class="img-fluid">  
                                @endisset  
                                <p class="mt-3">Image Background:</p>
                                @isset($branch->photo)
                                    <img src="{{asset('storage/'.$branch->photo)}}" class="img-fluid">
                                @endisset    
                        </div>
                        <div class="col-md-8">
                            <h5>{{$branch->name}}</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th colspan="2">
                                            <h5>Profile</h5>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{$branch->IndustryCategory->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Branch Type</th>
                                        <td>
                                            <span class="badge badge-primary">
                                                {{$branch->BranchType->code}} - {{$branch->BranchType->name}}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{$branch->email}}</td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td>{{$branch->country}}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone Number</th>
                                        <td>
                                            <ul>
                                                <li>Fixed Phone: <b>{{$branch->fixed_phone ?: '-'}}</b></li>
                                                <li>Mobile Phone: <b>{{$branch->mobile_phone}}</b></li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Services Likes</th>
                                        <td>{{number_format($branch->likes)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Show in Mobile?</th>
                                        <td>
                                            @if ($branch->is_active)
                                                <h5 class="badge badge-primary">Yes</h5>
                                            @else
                                                <h5 class="badge badge-danger">No</h5>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            <br>
                                            <br>
                                            <h5>Location Detail</h5>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{$branch->address}}, {{$branch->Regency->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Latitude</th>
                                        <td>{{$branch->lat}}</td>
                                    </tr>
                                    <tr>
                                        <th>Longitude</th>
                                        <td>{{$branch->long}}</td>
                                    </tr>
                                    <tr>
                                        <th>Maps</th>
                                        <td>
                                            {{-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.011310751414!2d107.5938467146131!3d-6.889247869328712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e69314ebee4b%3A0xbc5831d22e61beeb!2sParis%20Van%20Java%20Resort%20Lifestyle%20Place!5e0!3m2!1sid!2sid!4v1587043514969!5m2!1sid!2sid" width="400" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe> --}}
                                            <div class="" id="map"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            <br>
                                            <br>
                                            <h5>Services Days and Times</h5>
                                        </th>
                                    </tr>
                                    @foreach ($branch->Schedule as $schedule)
                                        <tr>
                                            <th>{{$schedule->day}}</th>
                                            <td>
                                                @switch($schedule->status)
                                                    @case('closed')
                                                        <span class="badge badge-danger">Closed</span>
                                                        @break
                                                    @case('fullday')
                                                        <span class="badge badge-success">Fullday</span>
                                                        @break
                                                    @default
                                                        {{$schedule->start_time}} - {{$schedule->end_time}}
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="2">
                                            <br>
                                            <br>
                                            <h5>Admin Account</h5>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{$branch->Admin[0]->email}}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{$branch->Admin[0]->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{$branch->Admin[0]->phone}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#unregisterModal">
                                                Unregister Branch
                                            </button>
                                            {{-- START MODAL --}}
                                            <div class="modal fade" id="unregisterModal" tabindex="-1" role="dialog" aria-labelledby="unregisterModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="unregisterModalLabel">Unregistering Branch</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure want to unregister this branch?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                        <form action="{{route('admin.branch.destroy', $branch->id)}}" method="post" style="display: inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Remove Branch">
                                                                Yes
                                                            </button>
                                                        </form>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- END MODAL --}}
                                            <div class="text-right">
                                                <a href="{{route('admin.branch.index')}}" class="btn btn-primary">Back to List</a>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        const branch = JSON.parse('{!! $branch !!}')
		/**
        * Adds markers to the map highlighting the locations of the captials of
        * France, Italy, Germany, Spain and the United Kingdom.
        *
        * @param  {H.Map} map      A HERE Map instance within the application
        */
        function addMarkersToMap(map) {
            var parisMarker = new H.map.Marker({lat: branch.lat, lng: branch.long});
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

        //Step 2: initialize a map - this map is centered over Europe
        var map = new H.Map(document.getElementById('map'),
        defaultLayers.vector.normal.map,{
        center: {lat: branch.lat, lng: branch.long},
        zoom: 15,
        pixelRatio: window.devicePixelRatio || 1
        });
        // add a resize listener to make sure that the map occupies the whole container
        window.addEventListener('resize', () => map.getViewPort().resize());

        //Step 3: make the map interactive
        // MapEvents enables the event system
        // Behavior implements default interactions for pan/zoom (also on mobile touch environments)
        var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

        // Create the default UI components
        var ui = H.ui.UI.createDefault(map, defaultLayers);

        // Now use the map as required...
        window.onload = function () {
        addMarkersToMap(map);
        }               
	</script>
@endpush