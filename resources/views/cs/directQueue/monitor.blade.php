@extends('layouts.app')

@section('content')
    <div id="app">
        <monitor-component 
            :max_recall="{{ Auth::user()->Branch->BranchConfiguration->maximum_recall }}" 
            :max_requeue="{{ Auth::user()->Branch->BranchConfiguration->maximum_requeue_count }}" 
            :allow_transfer="{{ Auth::user()->Branch->BranchConfiguration->allow_transfer ? 'true' : 'false' }}" 
            :auth="{{ Auth::user() }}"
            :accessible_features="{{ Auth::user()->Branch->getFeatures() }}"
            :workstation="{{ Auth::user()->WorkstationVct->Workstation }}"
        />
    </div>
@endsection

@push('js')
    <script src="{{ mix('/js/app.js') }}"></script>
@endpush