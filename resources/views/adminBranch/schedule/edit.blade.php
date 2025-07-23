@extends('layouts.app')

@section('content')
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
                   {{ __('infobox.editschedule') }}
                </p>
            </div>
        </div>
    </div>
</div>
    {{-- <div class="card mb-4 custom-info" data-open="open" role="alert">
        <div class="card-body">
            <div class="custom-info-head">
                <h6 class="font-weight-bold my-0">
                    <span class="fas fa-info-circle text-primary mr-1"></span>
                    Informasi
                </h6>

                <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                    Tampilkan
                </button>
            </div>

            <div class="custom-info-body">
                <p>
                    Jam buka ditutup menggunakan format 24 jam (Jam 00.00 sd 23.00)
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('edit.module', ['module' => __('Schedule')]) }}
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin-branch.branch-configuration.schedule.update', $schedule->id)}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="day">{{ __('Day') }}</label>
                                    <select name="day" id="day" class="form-control @error('day') is-invalid @enderror" required>
                                        <option value="sunday" {{ $schedule->day != 'sunday' ?: 'selected' }}>
                                            {{ __('Sunday') }}
                                        </option>
                                        <option value="monday" {{ $schedule->day != 'monday' ?: 'selected' }}>
                                            {{ __('Monday') }}
                                        </option>
                                        <option value="tuesday" {{ $schedule->day != 'tuesday' ?: 'selected' }}>
                                            {{ __('Tuesday') }}
                                        </option>
                                        <option value="wednesday" {{ $schedule->day != 'wednesday' ?: 'selected' }}>
                                            {{ __('Wednesday') }}
                                        </option>
                                        <option value="thursday" {{ $schedule->day != 'thursday' ?: 'selected' }}>
                                            {{ __('Thursday') }}
                                        </option>
                                        <option value="friday" {{ $schedule->day != 'friday' ?: 'selected' }}>
                                            {{ __('Friday') }}
                                        </option>
                                        <option value="saturday" {{ $schedule->day != 'saturday' ?: 'selected' }}>
                                            {{ __('Saturday') }}
                                        </option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'day'])
                                </div>

                                <div class="form-group">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" onchange="changeStatus()">
                                        <option value="open" {{ $schedule->status != 'open' ?: 'selected' }}>
                                            {{ __('Open') }}
                                        </option>
                                        <option value="fullday" {{ $schedule->status != 'fullday' ?: 'selected' }}>
                                            {{ __('Fullday') }}
                                        </option>
                                        <option value="closed" {{ $schedule->status != 'closed' ?: 'selected' }}>
                                            {{ __('Closed') }}
                                        </option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'status'])
                                </div>

                                <div class="form-group" id="start_time">
                                    <label for="start_time">{{ __('Start Time') }}</label>

                                    <input
                                        type="text"
                                        name="start_time"
                                        class="form-control datetimepicker-input @error('start_time') is-invalid @enderror"
                                        data-toggle="datetimepicker"
                                        value="{{ $schedule->start_time }}"
                                    />

                                    @include('layouts.inputError', ['errorName' => 'start_time'])
                                </div>

                                <div class="form-group" id="end_time">
                                    <label for="end_time">{{ __('End Time') }}</label>
                      
                                    <input
                                        type="text"
                                        name="end_time"
                                        class="form-control datetimepicker-input @error('end_time') is-invalid @enderror"
                                        data-toggle="datetimepicker"
                                        value="{{ $schedule->end_time }}"
                                    />

                                    @include('layouts.inputError', ['errorName' => 'end_time'])
                                </div>

                                <button class="btn btn-warning">{{ __('Update') }}</button>
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

            if (statusOldValue == 'open') {
                $('#start_time').show()
                $('#end_time').show()
            } else {
                $('#start_time').hide()
                $('#end_time').hide()
            }
        });

        function changeStatus() {
            let status = $('#status option:selected').val()
            if (status == 'open') {
                $('#start_time').show()
                $('#end_time').show()
            } else {
                $('#start_time').hide()
                $('#end_time').hide()
            }
        }
    </script>
@endpush