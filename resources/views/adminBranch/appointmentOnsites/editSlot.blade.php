@extends('layouts.app')

@section('content')
@push('css')
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
    </style>
@endpush
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 custom-card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('edit.module', ['module' => __('Slot')]) }}
                </h6>

                <button class="btn btn-outline-primary" onclick="onEdit(this)">
                    Edit
                </button>
            </div>

            <div class="card-body">
                @include('layouts.alert')

                <form
                    action="{{route('admin-branch.appointment-onsites.update', $appointment_onsite->id)}}"
                    method="post"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{$appointment_onsite->id}}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $appointment_onsite->name }}" disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="service_id">{{ __('Service') }}</label>
                                <select name="service_id" id="service_id" class="form-control @error('service_id') is-invalid @enderror" onchange="getSlots()" disabled>
                                    @foreach ($services as $service)
                                        <option value="{{$service->id}}" {{ $service->id == $appointment_onsite->service_id ? 'selected' : '' }}>{{$service->name}}</option>
                                    @endforeach
                                </select>
                                @include('layouts.inputError', ['errorName' => 'service_id'])
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">{{ __('Date') }}</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" id="date" value="{{ $appointment_onsite->date }}" onchange="getSlots()" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" readonly>
                                @include('layouts.inputError', ['errorName' => 'date'])
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slot_id">{{ __('Slot') }}</label>
                                <select name="slot_id" id="slot_id" class="form-control @error('slot_id') is-invalid @enderror" disabled>
                                    <option value="{{$appointment_onsite->slot_id}}" selected>
                                        {{ \Carbon\Carbon::parse($appointment_onsite->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment_onsite->end_time)->format('H:i') }}
                                    </option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'slot_id'])
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn d-none btn-warning fullwidth mb-3">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        function onEdit(e) {
            const isEdit = !$(e).hasClass('btn-outline-danger')

            if (isEdit) {
                $(e).addClass('btn-outline-danger');
                $(e).text('Batal')
                $('input[type="text"], input[type="email"], input[type="date"], input[type="number"], textarea').prop('readonly', false);
                $('input[type="file"], select').prop('disabled', false);
                $(".d-none").addClass('d-block');
            } else {
                $(e).removeClass('btn-outline-danger');
                $(e).text('Edit')
                $('input[type="text"], input[type="email"], input[type="date"], input[type="number"], textarea').prop('readonly', true);
                $('input[type="file"], select').prop('disabled', true);
                $(".d-none").removeClass('d-block');
            }

            getSlots();
        }

        function getDayFromDate(dateString) {
            var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            var date = new Date(dateString);
            var dayIndex = date.getDay();
            return days[dayIndex];
        }

        function getSlots() {
            var serviceId = $('#service_id').val();
            var date = $('#date').val();

            $.ajax({
                url: '/admin-branch/appointment-onsites/slots',
                type: 'GET',
                data: {
                    service_id: serviceId,
                    day: getDayFromDate(date)
                },
                success: function(response) {
                    var slots = response.slots;
                    var slotDropdown = $('#slot_id');
                    slotDropdown.empty();

                    if (slots.length > 0) {
                        $.each(slots, function(index, slot) {
                            var option = $('<option></option>').attr('value', slot.id).text(slot.start_time + ' - ' + slot.end_time);
                            slotDropdown.append(option);
                        });
                    } else {
                        var option = $('<option></option>').attr('value', '').text('Slot Waktu Kosong');
                        slotDropdown.append(option);
                    }
                }
            });
        }
    </script>
@endpush
@endsection
