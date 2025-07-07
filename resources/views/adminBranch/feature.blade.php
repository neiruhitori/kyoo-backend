@extends('layouts.app')

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
                        {{ __('infobox.features1') }}                    </li>
                    <li>
                        {{ __('infobox.features2') }} 
                    </li>
                </ul>
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert"> {{ __('Hide') }}</button>
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
                            <form action="{{ route('admin-branch.branch-configuration.feature.update') }}"
                            method="post">
                                @csrf
                                @method('PUT')

                                @if (Auth::user()->Branch->BranchType->is_direct_queue)
                                    <div class="form-group">
                                        <label for="maximum_recall">{{ __('Max Recall') }}</label>
                                        <input name="maximum_recall" type="number" min="0"
                                            class="form-control @error('maximum_recall') is-invalid @enderror"
                                            value="{{ $branch_config->maximum_recall ?? old('maximum_recall') }}"
                                            required>
                                        @include('layouts.inputError', ['errorName' => 'maximum_recall'])
                                    </div>

                                    <div class="form-group">
                                        <label for="maximum_requeue_count">{{ __('Max Requeue') }}</label>
                                        <input name="maximum_requeue_count" type="number" min="0"
                                            class="form-control @error('maximum_requeue_count') is-invalid @enderror"
                                            value="{{
                                                $branch_config->maximum_requeue_count ?? old('maximum_requeue_count')
                                            }}"
                                            required>
                                        @include('layouts.inputError', [
                                            'errorName' => 'maximum_requeue_count',
                                        ])
                                    </div>

                                    <div class="form-group">
                                        <label for="allow_transfer">{{ __('Allow Transfer') }}</label>
                                        <select name="allow_transfer" id="allow_transfer"
                                            class="form-control @error('allow_transfer') is-invalid @enderror"
                                            {{ Auth::user()->Branch->BranchType->is_premium ?: 'disabled' }}>
                                            <option value="0"
                                                {{ $branch_config && $branch_config->allow_transfer ?: 'selected' }}>
                                                {{ __('No') }}</option>
                                            <option value="1"
                                                {{ !$branch_config || !$branch_config->allow_transfer ?: 'selected' }}>
                                                {{ __('Yes') }}</option>
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'allow_transfer'])
                                    </div>

                                    <div class="form-group">
                                        <label for="queue_layout_configuration">{{ __('Queue Layout Configuration') }}</label>
                                        <select name="queue_layout_configuration" id="queue_layout_configuration"
                                            class="form-control @error('queue_layout_configuration') is-invalid @enderror"
                                            {{ Auth::user()->Branch->BranchType->is_premium ?: 'disabled' }}>
                                            <option value="standard-ui"
                                                {{ !$branch_config || $branch_config->queue_layout_configuration == "standard-ui" ? 'selected' : '' }}>
                                                {{ __('Standard UI') }}
                                            </option>
                                            <option value="modern-ui"
                                                {{ $branch_config && $branch_config->queue_layout_configuration == "modern-ui" ? 'selected' : '' }}>
                                                {{ __('Modern UI') }}
                                            </option>
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'queue_layout_configuration'])
                                    </div>

                                    @if (Auth::user()->Branch->hasAccess('Panggilan Suara'))
                                    <div class="form-group">
                                        <label for="signage_vo_format ">{{ __('Signage VO Format') }}</label>
                                        <select name="signage_vo_format" id="signage_vo_format"
                                            class="form-control @error('signage_vo_format') is-invalid @enderror">
                                            <option value="wav"
                                            {{ !$branch_config || $branch_config->signage_vo_format == "wav" ? 'selected' : '' }}>
                                                
                                                {{ __('WAV') }}
                                            </option>
                                        @if (Auth::user()->Branch->country == 'Indonesia')
                                            <option value="mp3"
                                            {{ !$branch_config || $branch_config->signage_vo_format == "mp3" ? 'selected' : '' }}>
                                            {{ __('MP3') }}
                                            </option>
                                        @endif
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'signage_vo_format'])
                                    </div>

                                    <div class="form-group">
                                        <label for="vo_call_style ">{{ __('Signage VO Style') }}</label>
                                        <select name="vo_call_style" id="vo_call_style"
                                            class="form-control @error('vo_call_style') is-invalid @enderror"
                                            {{ Auth::user()->Branch->BranchType->is_premium ?: 'disabled' }}>
                                            <option value="standard"
                                            {{ !$branch_config || $branch_config->vo_call_style == "standard" ? 'selected' : '' }}>
                                                {{ __('Voice Announcement Standard') }}
                                            </option>
                                            <option value="simple"
                                            {{ !$branch_config || $branch_config->vo_call_style == "simple" ? 'selected' : '' }}>
                                            {{ __('Voice Announcement Simple') }}
                                        </option>
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'vo_call_style'])
                                    </div>
                                    <div class="form-group">
                                            <label for="cs_page">{{ __('CS Page UI Style') }}</label>
                                            <select name="cs_page" id="cs_page"
                                                class="form-control @error('cs_page') is-invalid @enderror">
                                                <option value="style-1"
                                                    {{ !$branch_config || $branch_config->cs_page == "style-1" ? 'selected' : '' }}>
                                                    {{ __('Style 1') }}
                                                </option>
                                                <option value="style-2"
                                                    {{ !$branch_config || $branch_config->cs_page == "style-2" ? 'selected' : '' }}>
                                                    {{ __('Style 2') }}
                                                </option>
                                            </select>
                                    </div>

                                        <label for="">{{ __('Voice Call') }}</label>

                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" name="queue_voice" class="form-check-input"
                                                    id="queue-voice-label"
                                                    {{
                                                        ($branch_config && $branch_config->queue_voice) ||
                                                        old('queue_voice')
                                                        ? 'checked'
                                                        : ''
                                                    }}>

                                                <label for="queue-voice-label" class="form-check-label">{{ __('Activate') }}</label>
                                            </div>
                                            @include('layouts.inputError', ['errorName' => 'queue_voice'])
                                        </div>
                                    @endif

                                    @if (Auth::user()->Branch->hasAccess('Promosi'))
                                        <label for="">{{ __('Promotion') }}</label>

                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" name="promotion" class="form-check-input"
                                                    id="promotion-label"
                                                    {{
                                                        ($branch_config && $branch_config->promotion) ||
                                                        old('promotion')
                                                        ? 'checked'
                                                        : ''
                                                    }}>

                                                <label for="promotion-label" class="form-check-label">{{ __('Activate') }}</label>
                                            </div>

                                            @include('layouts.inputError', ['errorName' => 'queue_voice'])
                                        </div>
                                    @endif
                                @endif
                                

                                <label for="wa-notification">{{ __('Whatsapp Notification') }}</label>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="wa_notification" class="form-check-input"
                                            id="wa-notification"
                                            {{
                                                ($branch_config && $branch_config->wa_notification) ||
                                                old('wa_notification')
                                                ? 'checked'
                                                : ''
                                            }}>

                                        <label for="wa-notification" class="form-check-label">{{ __('Activate') }}</label>
                                    </div>

                                    @include('layouts.inputError', ['errorName' => 'wa_notification'])
                                </div>

                                <label for="serving-directly">{{ __('Serve Directly') }}</label>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="serving_directly" class="form-check-input"
                                            id="serving-directly"
                                            {{
                                                ($branch_config && $branch_config->serving_directly) ||
                                                old('serving_directly')
                                                ? 'checked'
                                                : ''
                                            }}>

                                        <label for="serving-directly" class="form-check-label">{{ __('Activate') }}</label>
                                    </div>

                                    @include('layouts.inputError', ['errorName' => 'serving_directly'])
                                </div>
                                

                                <button type="submit" class="btn btn-warning">{{ __('Update') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Setting Check-in --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('Onsite Hybrid Check-in Configuration') }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin-branch.branch-configuration.feature.checkIn') }}" method="post">
                    <div class="row">
                            @csrf

                            {{-- jika layer 1 --}}
                            @if ($branch_config->layer == 1)
                            <div class="col-md-12">
                                <b class="my-2">
                                    {{ __('This feature can only be used for the Hybrid Appointment-Onsite Portal Page') }}
                                </b>
                            </div>
                            @endif


                        <div class="col-md-3" id="select-check">
                            @if ($branch_config->layer != 1)
                            <select class="custom-select" id="check-in" name="check_in_rule">
                                <option value="0" {{ $branch_config->check_in_rule == 0 ? 'selected' : ''}}>0 {{ __('Hour')  }}</option>
                                <option value="1" {{ $branch_config->check_in_rule == 1 ? 'selected' : ''}}>1 {{ __('Hour')  }}</option>
                                <option value="2" {{ $branch_config->check_in_rule == 2 ? 'selected' : ''}}>2 {{ __('Hour')  }}</option>
                                <option value="3" {{ $branch_config->check_in_rule == 3 ? 'selected' : ''}}>3 {{ __('Hour')  }}</option>
                                <option value="4" {{ $branch_config->check_in_rule == 4 ? 'selected' : ''}}>4 {{ __('Hour')  }}</option>
                                <option value="5" {{ $branch_config->check_in_rule == 5 ? 'selected' : ''}}>5 {{ __('Hour')  }}</option>
                                <option value="24" {{ $branch_config->check_in_rule == 24 ? 'selected' : ''}}>24 {{ __('Hour')  }}</option>
                              </select>
                            @else
                                
                            @endif

                        </div>
                        
                        <div class="col-md-9">
                            <b class="my-2" id="desc">
                            </b>
                        </div>
                        <div class="col-md-3 mt-3">
                            @if ($branch_config->layer != 1)
                            <button type="submit" id="checkInBtn" class="btn btn-warning" {{ $branch_config->layer == 1 ? 'disabled' : ''}}>{{ __('Update') }}</button>
                            @endif
                        </div>
                    </div>
                </form>
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

            

            @push('js')
                <script>
                    $(document).ready(function() {
                        let allow_transferOldValue =
                            `{{
                                old('allow_transfer') ??
                                (Auth::user()->Branch->BranchConfiguration->allow_transfer ?? '')
                            }}`;

                        if (allow_transferOldValue !== '') {
                            $('#allow_transfer').val(allow_transferOldValue);
                        }
                    });

                    let checkin = document.getElementById('check-in');
                    let desc = document.getElementById('desc');
                    let layerVal = @json($branch_config->layer);
                    
                    if (layerVal == 1) {
                        
                    } else {
                        function getValue() {
                        let value = checkin.value;                       
                            if (value == '0') {
                                desc.innerHTML = `*{{ __('Users can check in directly') }}`;
                            } else {
                                desc.innerHTML = `*${"{{ __('Users can check in :num hour before the appointment', ['num' => ':num']) }}".replace(':num', value)}`;
                                }
                        }
                        getValue()
                        checkin.addEventListener('change', getValue);
                    }
                   
                </script>
            @endpush
        </div>
    </div>
@endsection
