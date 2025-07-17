@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
<style>
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
                                {{ __('infobox.termsandcondition') }} 
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
</div>


<div id="app">
  <terms-conditions-component 
    locale = "{{ app()->getLocale() }}"
  />
</div>
@endsection

@push('js')
  <script src="{{ mix('js/app.js') }}"></script>
@endpush