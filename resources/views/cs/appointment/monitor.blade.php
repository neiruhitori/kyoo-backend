@extends('layouts.app')

@section('content')
    <div id="app">

        @if (Auth::user()->Branch->BranchConfiguration->cs_page == 'style-2')
            <appointment-monitor-2
                :branch="{{ Auth::user()->Branch }}"
                :vct="{{ Auth::user() }}"
                :auth="{{ Auth::user() }}"
                :workstation="{{ Auth::user()->WorkstationVct->Workstation }}"
                :services="{{ $services }}"
                lang={{ app()->getLocale() }}
            />
            @else
            <appointment-monitor
                :branch="{{ Auth::user()->Branch }}"
                :vct="{{ Auth::user() }}"
                :auth="{{ Auth::user() }}"
                :workstation="{{ Auth::user()->WorkstationVct->Workstation }}"
                :services="{{ $services }}"
                lang={{ app()->getLocale() }}
            />
        @endif
    </div>
@endsection

@push('js')
    <script src="{{ mix('/js/app.js') }}"></script>
@endpush