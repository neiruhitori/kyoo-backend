@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Schedule</h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('adminBranch.schedule.update', $schedule->id)}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="day">Day</label>
                                    <select name="day" id="day" class="form-control @error('day') is-invalid @enderror" required>
                                        <option value="sunday">Sunday</option>
                                        <option value="monday">Monday</option>
                                        <option value="tuesday">Tuesday</option>
                                        <option value="wednesday">Wednesday</option>
                                        <option value="thursday">Thursday</option>
                                        <option value="friday">Friday</option>
                                        <option value="saturday">Saturday</option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'day'])
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="">Open</option>
                                        <option value="fullday">Open Fullday</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'status'])
                                </div>

                                <div class="form-group">
                                    <label for="start_time">Start Time</label>
                                    <input name="start_time" type="time" class="form-control @error('start_time') is-invalid @enderror" value="{{old('start_time') ?: $schedule->start_time}}" required>
                                    @include('layouts.inputError', ['errorName' => 'start_time'])
                                </div>

                                <div class="form-group">
                                    <label for="end_time">End Time</label>
                                    <input name="end_time" type="time" class="form-control @error('end_time') is-invalid @enderror" value="{{old('end_time') ?: $schedule->end_time}}" required>
                                    @include('layouts.inputError', ['errorName' => 'end_time'])
                                </div>

                                <button class="btn btn-warning">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            const dayOldValue = '{{ old('day') ?: $schedule->day }}';
                
            if(dayOldValue !== '') {
                $('#day').val(dayOldValue);
            }

            const statusOldValue = '{{ old('status') ?: $schedule->status }}';
                
            if(statusOldValue !== '') {
                $('#status').val(statusOldValue);
            }
        });
    </script>
@endpush