@extends('layouts.app')
@section('content')
    <div id="app">
        <monitor-component 
        :max_recall="{{Auth::user()->Branch->BranchConfiguration->maximum_recall}}" 
        :max_requeue="{{Auth::user()->Branch->BranchConfiguration->maximum_requeue_count}}" 
        :allow_transfer="{{Auth::user()->Branch->BranchConfiguration->allow_transfer ? 'true' : 'false'}}" 
        :auth="{{Auth::user()}}"
        />
    </div>
@endsection
@push('js')
    <script src="{{asset('js/app.js')}}"></script>
@endpush