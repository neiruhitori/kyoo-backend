@extends('layouts.app')

@section('content')

    @include('layouts.alert')
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
                                {{ __('infobox.access1') }}
                            </li>
                            <li style="margin-bottom: 0.25rem;">
                                {{ __('infobox.access2') }}
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
</div>

    <div class="card shadow mb-4">
        {{-- <div class="card-header">
        </div> --}}
        
        <div class="card-body">
            <h5 class="mb-4 font-weight-bold" style="color: #103C7C">{{ __('Activation of Additional Menu') }}</h6>
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

                <button type="submit" class="btn btn-primary float-right px-3" style="background-color: #103C7C">{{ __('Save') }}</button>
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
