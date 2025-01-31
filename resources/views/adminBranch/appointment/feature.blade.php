@extends('layouts.app')

@push('css')
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endpush
@section('content')
    <div class="card mb-4 custom-info" data-open="open" role="alert">
        <div class="card-body">
            <div class="custom-info-head">
                <h6 class="font-weight-bold my-0">
                    <span class="fas fa-info-circle text-primary mr-1"></span>
                    {{ __('Information') }}
                </h6>

                <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                    {{ __('Show') }}
                </button>
            </div>

            <div class="custom-info-body">
                <p>
                <ul style="padding-left: 2rem;">
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('infobox.features1') }} 
                    </li>
                    <li>
                        {{ __('infobox.features2') }}
                    </li>
                </ul>
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">{{ __('Hide') }}</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('update.module', ['module' => __('Branch Configuration')]) }}
                    </h6>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('admin-branch.branch-configuration.feature.update') }}" method="post">
                                @csrf
                                @method('PUT')

                                @if (Auth::user()->Branch->hasAccess('Panggilan Suara'))
                                    <label for="">Panggilan Suara</label>

                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" name="queue_voice" class="form-check-input"
                                                id="queue-voice-label"
                                                {{ ($branch_config && $branch_config->queue_voice) || old('queue_voice') ? 'checked' : '' }}>

                                            <label for="queue-voice-label" class="form-check-label">Aktifkan</label>
                                        </div>
                                        @include('layouts.inputError', ['errorName' => 'queue_voice'])
                                    </div>
                                @endif

                                @if (Auth::user()->Branch->hasAccess('Promosi'))
                                    <label for="">Promosi</label>

                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" name="promotion" class="form-check-input"
                                                id="promotion-label"
                                                {{ ($branch_config && $branch_config->promotion) || old('promotion') ? 'checked' : '' }}>

                                            <label for="promotion-label" class="form-check-label">Aktifkan</label>
                                        </div>

                                        @include('layouts.inputError', ['errorName' => 'queue_voice'])
                                    </div>
                                @endif

                                <label for="wa-notification">Notifikasi WA ke Pelanggan</label>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="wa_notification" class="form-check-input"
                                            id="wa-notification"
                                            {{ ($branch_config && $branch_config->wa_notification) || old('wa_notification') ? 'checked' : '' }}>

                                        <label for="wa-notification" class="form-check-label">Aktifkan</label>
                                    </div>

                                    @include('layouts.inputError', ['errorName' => 'wa_notification'])
                                </div>

                                @if (Auth::user()->Branch->isPremium)
                                    <label for="wa-notification-owner">Notifikasi WA ke Owner</label>

                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" name="wa_notification_owner" class="form-check-input"
                                                id="wa-notification-owner"
                                                {{ ($branch_config && $branch_config->wa_notification_owner) || old('wa_notification_owner') ? 'checked' : '' }}>

                                            <label for="wa-notification-owner" class="form-check-label">Aktifkan</label>
                                        </div>

                                        @include('layouts.inputError', [
                                            'errorName' => 'wa_notification_owner',
                                        ])
                                    </div>

                                    <div class="form-group row">
                                        <label for="phone-owner" class="col-md-2 col-form-label">Nomer WA Owner</label>
                                        <div class="col-md-3">
                                            <input type="number" name="phone_owner" class="form-control form-control-sm"
                                                id="phone-owner" value="{{ $branch_config->phone_owner }}">

                                            @include('layouts.inputError', ['errorName' => 'phone_owner'])
                                        </div>
                                    </div>
                                @endif

                                <button type="submit" class="btn btn-warning">{{ __('Update') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('Kyoo Queue API') }}
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <b class="my-4">
                                {{ __('Branch Token API only available for premium license branch and registered for API. Please contact') }}
                                <a href="mailto:support@kyoo.id"> support@kyoo.id</a>
                            </b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
