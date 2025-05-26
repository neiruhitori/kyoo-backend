@extends('layouts.appCS')

@section('content')
    <div id="app">
        <monitor-component-2
            :max_recall="{{ Auth::user()->Branch->BranchConfiguration->maximum_recall }}"
            :max_requeue="{{ Auth::user()->Branch->BranchConfiguration->maximum_requeue_count }}"
            :allow_transfer="{{ Auth::user()->Branch->BranchConfiguration->allow_transfer ? 'true' : 'false' }}"
            :serving_directly="{{ Auth::user()->Branch->BranchConfiguration->serving_directly ? 'true' : 'false' }}"
            :auth="{{ Auth::user() }}"
            :accessible_features="{{ Auth::user()->Branch->getFeatures() }}"
            :workstation="{{ Auth::user()->WorkstationVct->Workstation }}"
            :sub_services="{{ $sub_services }}"
            lang={{ app()->getLocale() }}
        />
    </div>
@endsection

@push('js')
    <script src="{{ mix('/js/app.js') }}"></script>
@endpush
