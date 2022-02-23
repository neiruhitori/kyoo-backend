@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Virtual Counter') }} {{Auth::user()->Branch->name}}</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
        </div>
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('create.module', ['module' => __('Direct Queue')]) }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cs.directQueue.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="vct_id" value="{{ Auth::id() }}">
                        <div class="col-md-12 form-group">
                            <label for="workstation_service_id">{{ __('Service') }}</label>
                            <select name="workstation_service_id" id="workstation_service_id" class="form-control">
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->Service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="" class="form-control" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="phone">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" id="" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary">{{ __('Save') }}</button>
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
            const workstation_service_idValue = '{{ old('workstation_service_id') }}';
                
            if(workstation_service_idValue !== '') {
                $('#workstation_service_id').val(workstation_service_idValue);
            }
        });
    </script>
@endpush