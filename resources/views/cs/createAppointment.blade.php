@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Virtual Counter {{Auth::user()->Branch->name}}</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
        </div>
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Active Appointment</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cs.appointment.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        <div class="col-md-12 form-group">
                            <label for="slot_id">Slot</label>
                            <select name="slot_id" id="slot_id" class="form-control">
                                <option value="" selected disabled>Choose Slot</option>
                                @foreach ($slots as $slot)
                                    <option value="{{ $slot->id }}">{{ $slot->Service->name }} ({{ $slot->day }} | {{ $slot->start_time }} - {{ $slot->end_time }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="date">Date</label>
                            <input type="date" name="date" id="" class="form-control" value="{{ old('date') }}" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" name="phone" id="" class="form-control" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="" cols="" rows="" class="form-control">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary">Submit</button>
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
            const slot_idValue = '{{ old('slot_id') }}';
                
            if(slot_idValue !== '') {
                $('#slot_id').val(slot_idValue);
            }
        });
    </script>
@endpush