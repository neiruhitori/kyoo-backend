@extends('layouts.app')

@push('css')
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    .accordion-toggle-custom {
                transition: padding 0.3s ease;
            }
    .accordion-toggle-custom::after {
                    font-family: "Font Awesome 5 Free";
                    font-weight: 900;
                    transition: transform 0.2s ease;
                    margin-left: auto;
                }
    .accordion-toggle-custom[aria-expanded="false"]::after {
                        content: "\f107";
                    }

    .accordion-toggle-custom[aria-expanded="true"]::after {
                        content: "\f106";
                    }
    </style>
@endpush
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
                    <div style="padding: 0rem 1rem 1rem 1rem;">
                       <ul style="">
                            <li style="margin-bottom: 0.25rem;">
                               {{ __('infobox.features1') }} 
                            </li>
                            <li style="margin-bottom: 0.25rem;">
                               {{ __('infobox.features2') }}                   
                            </li>
                        </ul>
                    </div>
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
                                <div class="row">
                                    <div class="col-md-6">
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
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone-owner" >{{ __('Owner Whatsapp Number') }}</label>
                                            <input type="number" name="phone_owner" class="form-control"
                                                    id="phone-owner" value="{{ $branch_config->phone_owner }}">
                                            @include('layouts.inputError', ['errorName' => 'phone_owner'])
                                        
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if (Auth::user()->Branch->hasAccess('Panggilan Suara'))
                                                    <label for="">{{ __('Voice Call') }}</label>

                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="queue_voice" class="form-check-input"
                                                                id="queue-voice-label"
                                                                {{ ($branch_config && $branch_config->queue_voice) || old('queue_voice') ? 'checked' : '' }}>

                                                            <label for="queue-voice-label" class="form-check-label">{{ __('Activate') }}</label>
                                                        </div>
                                                        @include('layouts.inputError', ['errorName' => 'queue_voice'])
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                @if (Auth::user()->Branch->hasAccess('Promosi'))
                                                    <label for="">{{ __('Promotion') }}</label>

                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="promotion" class="form-check-input"
                                                                id="promotion-label"
                                                                {{ ($branch_config && $branch_config->promotion) || old('promotion') ? 'checked' : '' }}>

                                                            <label for="promotion-label" class="form-check-label">{{ __('Activate') }}</label>
                                                        </div>

                                                        @include('layouts.inputError', ['errorName' => 'queue_voice'])
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="wa-notification">{{ __('WhatsApp Notification to Customers') }}</label>

                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <input type="checkbox" name="wa_notification" class="form-check-input"
                                                            id="wa-notification"
                                                            {{ ($branch_config && $branch_config->wa_notification) || old('wa_notification') ? 'checked' : '' }}>

                                                        <label for="wa-notification" class="form-check-label">{{ __('Activate') }}</label>
                                                    </div>

                                                    @include('layouts.inputError', ['errorName' => 'wa_notification'])
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @if (Auth::user()->Branch->isPremium)
                                                    <label for="wa-notification-owner">{{ __('WhatsApp Notification to Owner') }}</label>

                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="wa_notification_owner" class="form-check-input"
                                                                id="wa-notification-owner"
                                                                {{ ($branch_config && $branch_config->wa_notification_owner) || old('wa_notification_owner') ? 'checked' : '' }}>

                                                            <label for="wa-notification-owner" class="form-check-label">{{ __('Activate') }}</label>
                                                        </div>

                                                        @include('layouts.inputError', [
                                                            'errorName' => 'wa_notification_owner',
                                                        ])
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                    {{-- <div class="form-group row">
                                        <label for="phone-owner" class="col-md-2 col-form-label">{{ __('Owner Whatsapp Number') }}</label>
                                        <div class="col-md-3">
                                            <input type="number" name="phone_owner" class="form-control form-control-sm"
                                                id="phone-owner" value="{{ $branch_config->phone_owner }}">

                                            @include('layouts.inputError', ['errorName' => 'phone_owner'])
                                        </div>
                                    </div> --}}
                                

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
