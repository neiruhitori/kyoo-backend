@extends('layouts.app')

@section('content')

    @include('layouts.alert')
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
                <ul style="padding-left: 2rem;">
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('infobox.access1') }}
                    </li>
                    <li>
                        {{ __('infobox.access2') }}
                    </li>
                </ul>
                <button class="btn btn-warning float-right" data-toggle="alert">{{ __('Hide') }}</button>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Activation of Additional Menu') }}</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('admin-branch.cs.access.update', $branch->id) }}" method="POST">
                @csrf
                @method('PUT')
                @foreach ($features as $feature)
                    <div class="license-cfg-item mb-2">
                        <label for="{{ $feature->code }}" class="font-weight-bold">{{ __($feature->name) }}</label>
                        <div>
                            <input
                                type="checkbox"
                                name="feature_name[]"
                                id="{{ $feature->code }}"
                                class="license-checkbox form-control"
                                value="{{ $feature->id }}"
                                autocomplete="off"
                                {{ sizeof($active_menus->where('feature_id', $feature->id)) ? 'checked' : '' }}
                            >
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-warning float-right">{{ __('Save') }}</button>
            </form>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .license-cfg-item {
            display: flex;
            gap: 1rem;
        }

        .license-cfg-item label {
            display: block;
            width: 100%;
            max-width: 240px;
        }

        .license-cfg-item div {
            width: 100%;
            max-width: 240px;
        }

        .license-checkbox {
            width: 1.25rem;
            height: 1.25rem;
        }
    </style>
@endpush
