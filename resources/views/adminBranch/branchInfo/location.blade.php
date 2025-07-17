@extends('layouts.app')

@section('content')
@push('css')
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
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
    
        .custom-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .custom-card-header h6 {
            color: black !important;
        }
        .accordion-toggle-custom.collapsed {
            padding-bottom: 1rem !important;
        }

        .accordion-toggle-custom:not(.collapsed) {
            padding-bottom: 0rem !important;
        }

        .accordion-toggle-custom {
            transition: padding 0.3s ease;
        }

        .accordion-toggle-custom::after {
                font-family: "Font Awesome 5 Free";
                font-weight: 900;
                transition: transform 0.2s ease;
                margin-left: auto;
            }

        .accordion-toggle-custom[aria-expanded="false"]::after {
                    content: "\f107";
                }

        .accordion-toggle-custom[aria-expanded="true"]::after {
                    content: "\f106";
                }
    </style>
@endpush
<div class="accordion mb-3" id="accordionParent3">
    <div class="border-left-primary rounded " style="border-radius: 0.5rem; overflow: hidden;">

        <div  id="headingOne" style="background-color: #E6F3FF;">
            <button 
                class="btn btn-block text-left d-flex align-items-center accordion-toggle-custom" 
                type="button"
                data-toggle="collapse" 
                data-target="#accordion3" 
                aria-expanded="true" 
                aria-controls="accordion3"
                style="color: #103C7C; gap: 0.5rem; outline: none; box-shadow: none; padding: 1rem;"
                >
                    <span class="fas fa-info-circle text-primary"></span>
                    <h5 class="font-weight-bold my-0 text-primary">
                        {{ __('Information') }}
                    </h5>
             </button>
        </div>

        <div 
            id="accordion3" 
            class="collapse show" 
            aria-labelledby="headingOne" 
            data-parent="#accordionParent3" 
            style="background-color: #E6F3FF;"
            >
            <div style="padding: 0rem 1rem 1rem 2.5rem;">
                <p class="mb-0">
                    {{ __('infobox.location') }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 custom-card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('Branch Location') }}
                </h6>

                <button class="btn btn-outline-primary" onclick="onEdit(this)">
                    Edit
                </button>
            </div>

            <div class="card-body">
                @include('layouts.alert')

                <form
                    action="{{route('admin-branch.branch-information.update')}}"
                    method="post"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{$branch->id}}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="province_id">{{ __('Province') }}</label>
                                <select name="province_id" id="province_id" class="form-control @error('province_id') is-invalid @enderror" disabled>
                                    @foreach ($provinces as $province)
                                        <option value="{{$province->id}}">{{$province->name}}</option>
                                    @endforeach
                                </select>
                                @include('layouts.inputError', ['errorName' => 'province_id'])
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="regency_id">{{ __('City') }}</label>
                                <select name="regency_id" id="regency_id" class="form-control" disabled>
                                    <option value="{{$branch->regency_id}}">{{$branch->Regency->name}}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'regency_id'])
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">{{ __('Address') }}</label>
                        <textarea name="address" id="" cols="" rows="" class="form-control @error('address') is-invalid @enderror" readonly>{{old('address') ?: $branch->address}}</textarea>
                        @include('layouts.inputError', ['errorName' => 'address'])
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lat">{{ __('Lat') }}</label>
                                <input name="lat" id="latInput" type="text" class="form-control @error('lat') is-invalid @enderror" value="{{old('lat') ?: $branch->lat}}" readonly>
                                @include('layouts.inputError', ['errorName' => 'lat'])
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="long">{{ __('Long') }}</label>
                                <input name="long" id="lngInput" type="text" class="form-control @error('long') is-invalid @enderror" value="{{old('long') ?: $branch->long}}" readonly>
                                @include('layouts.inputError', ['errorName' => 'long'])
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {{-- <label for="name" id="map-text" class="text-primary" onclick="initMaps()">{{ __('Click here to show the maps') }}</label> --}}
                        <label for="name" id="map-text" class="text-primary" >{{ __('Click here to show the maps') }}</label>
                        <br>
                        <div id="map" style="width: 100%;"></div>
                    </div>

                    <button type="submit" class="btn d-none btn-warning fullwidth mb-3">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
    <script src='https://developer.here.com/javascript/src/iframeheight.js'></script>
    <script>
        function onEdit(e) {
            const isEdit = !$(e).hasClass('btn-outline-danger')

            if (isEdit) {
                $(e).addClass('btn-outline-danger');
                $(e).text("{{ __('Cancel') }}")
                $('input[type="text"], input[type="email"], input[type="number"], textarea').prop('readonly', false);
                $('input[type="file"], select').prop('disabled', false);
                $(".d-none").addClass('d-block');
            } else {
                $(e).removeClass('btn-outline-danger');
                $(e).text('Edit')
                $('input[type="text"], input[type="email"], input[type="number"], textarea').prop('readonly', true);
                $('input[type="file"], select').prop('disabled', true);
                $(".d-none").removeClass('d-block');
            }
        }

        $(document).ready(function() {
            const province_idOldValue = '{{ old('province_id') ?: $branch->Regency->province_id }}';
            
            if(province_idOldValue !== '') {
                $('#province_id').val(province_idOldValue);
            }

            $('#province_id').change(() => {
                let provinceId = $('#province_id').val()
                let country = "{{ $branch->country }}"
                fetch(`/api/regency/${country}/${provinceId}`)
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

        mapboxgl.accessToken = 'pk.eyJ1Ijoic3VwcG9ydGt5b28iLCJhIjoiY204dGxjNzF1MDFhbDJrb3AxeWljenpmcCJ9.Di2l0rvbaJwiT8vWaLwcJw';
        const latValue = parseFloat(document.getElementById('latInput').value)
        const lngValue = parseFloat(document.getElementById('lngInput').value)

        // Default ke Monas kalau input kosong atau invalid
        const initialLat = isNaN(latValue) ? -6.175392 : latValue
        const initialLng = isNaN(lngValue) ? 106.827153 : lngValue

        const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [initialLng, initialLat],
        zoom: 14
        });

        // Buat marker kosong di awal
        let marker = new mapboxgl.Marker()
        .setLngLat([initialLng, initialLat])
        .addTo(map);

        // Event klik di map
        map.on('click', function (e) {
        const lng = e.lngLat.lng;
        const lat = e.lngLat.lat;

        // Pindahkan marker ke posisi baru
        marker.setLngLat([lng, lat]);

        // Masukkan koordinat ke input
        document.getElementById('latInput').value = lat.toFixed(6);
        document.getElementById('lngInput').value = lng.toFixed(6);

        // console.log('Clicked at:', lat, lng);
        });
	</script>
@endpush
@endsection