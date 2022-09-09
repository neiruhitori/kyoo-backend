@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('create.module', ['module' => __('Slot')]) }}
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin-branch.branch-configuration.service.slot.store', $service->id)}}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="max_slots">{{ __('Maximum Slot') }}</label>
                                    <input name="max_slots" type="number" class="form-control @error('max_slots') is-invalid @enderror" value="{{old('max_slots')}}" min='1' required>
                                    @include('layouts.inputError', ['errorName' => 'max_slots'])
                                </div>

                                <div class="form-group">
                                    <label for="day">{{ __('Day') }}</label>
                                    <select name="day" id="day" class="form-control @error('day') is-invalid @enderror" required>
                                        <option value="sunday">{{ __('Sunday') }}</option>
                                        <option value="monday">{{ __('Monday') }}</option>
                                        <option value="tuesday">{{ __('Tuesday') }}</option>
                                        <option value="wednesday">{{ __('Wednesday') }}</option>
                                        <option value="thursday">{{ __('Thursday') }}</option>
                                        <option value="friday">{{ __('Friday') }}</option>
                                        <option value="saturday">{{ __('Saturday') }}</option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'day'])
                                </div>

                                <div class="form-group">
                                    <label for="start_time">{{ __('Start Time') }}</label>

                                    <input
                                        type="text"
                                        name="start_time"
                                        class="form-control datetimepicker-input @error('start_time') is-invalid @enderror"
                                        data-toggle="datetimepicker"
                                        value="{{ old('start_time') }}"
                                        autocomplete="off"
                                    />

                                    @include('layouts.inputError', ['errorName' => 'start_time'])
                                </div>

                                <div class="form-group">
                                    <label for="end_time">{{ __('End Time') }}</label>

                                    <input
                                        type="text"
                                        name="end_time"
                                        class="form-control datetimepicker-input @error('end_time') is-invalid @enderror"
                                        data-toggle="datetimepicker"
                                        value="{{ old('end_time') }}"
                                        autocomplete="off"
                                    />

                                    @include('layouts.inputError', ['errorName' => 'end_time'])
                                </div>
                                <button class="btn btn-primary">{{ __('Save') }}</button>
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
            const dayOldValue = '{{ old('day') }}';
                
            if(dayOldValue !== '') {
                $('#day').val(dayOldValue);
            }
        });
    </script>
@endpush