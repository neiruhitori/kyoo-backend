@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Appointment</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
        </div>

        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Active Appointment') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cs.appointments.store') }}" method="post">
                        @csrf

                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        <div class="col-md-12 form-group">
                            <label for="date">{{ __('Date') }}</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date') ?: date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="service_id">{{ __('Service') }}</label>
                            <select name="service_id" id="service_id" class="form-control">
                                <option value="" selected disabled>{{ __('Choose Service') }}</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" {{ $service->id == old('service_id') ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="slot_id">{{ __('Slot') }}</label>
                            <select name="slot_id" id="slot_id" class="form-control">
                                <option value="" selected disabled>{{ __('Choose Slot') }}</option>
                                {{-- @foreach ($slots as $slot)
                                    <option value="{{ $slot->id }}">{{ $slot->Service->name }} ({{ $slot->day }} | {{ $slot->start_time }} - {{ $slot->end_time }})</option>
                                @endforeach --}}
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="phone">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" id="" class="form-control" value="{{ old('phone') }}" required>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" name="email" id="" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="notes">{{ __('Notes') }}</label>
                            <textarea name="notes" id="" cols="" rows="" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('cs.appointments.monitor') }}" class="btn btn-secondary ml-1">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            getSlots()

            $('#service_id').change(() => {
                getSlots()
            })

            $('#date').change(() => {
                getSlots()
            })

            function getSlots() {
                let service_id = $('#service_id').val()
                let date = $('#date').val()
                fetch(`/api/slot`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        date,
                        service_id
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        $('#slot_id option').remove()

                        data.data.forEach(slot => {
                            $('#slot_id')
                                .append($("<option></option>")
                                .attr("value", slot.id)
                                .text(`${slot.start_time} - ${slot.end_time} (${slot.filledSlot}/${slot.max_slots})`));
                        });
                    })
                    .catch(err => console.log(err))
            }
        });
    </script>
@endpush