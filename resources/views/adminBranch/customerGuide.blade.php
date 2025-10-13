@extends('layouts.app')

@push('css')
    <style>
        .customer-guide__card-title {
            color: #0C61A1;
        }

        .accordion .card:first-of-type {
            border-bottom: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .accordion .card {
            margin-bottom: .75rem;
            box-shadow: 0px 0px 0px 1px rgba(139, 179, 210, 0.3);
            border-radius: .25rem;
            border: none;
        }

        .accordion .card .card-header {
            background-color: transparent;
            border: none;
            padding: 20px;
        }

        .card-header:first-child {
            border-radius: calc(0.25rem - 1px) calc(0.25rem - 1px) 0 0;
        }

        .accordion .card .card-header * {
            font-weight: 700;
            font-size: 1rem;
            color: #0D61A1;
        }

        .accordion .card .card-header a {
            display: block;
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            position: relative;
            -webkit-transition: color 0.5s ease;
            -moz-transition: color 0.5s ease;
            -ms-transition: color 0.5s ease;
            -o-transition: color 0.5s ease;
            transition: color 0.5s ease;
            padding-right: 1.5rem;
        }

        .accordion .card .card-header a[aria-expanded="false"]:before {
            content: "\f107";
        }

        .accordion .card .card-header a[aria-expanded="true"]:before {
            content: "\f106";
        }

        .accordion .card .card-header a:before {
            position: absolute; 
            right: 7px;
            top: 0;
            font-size: 18px;
            display: block;
            font-family: 'Font Awesome 5 Free';
            
            display: inline-block;
            padding-right: 3px;
            vertical-align: middle;
            font-size: .756em;
            color: #405189
        }

        .accordion .highlight {
            background: #F7FBFE !important;
        }

        .accordion .card .collapsing {
            background: #F7FBFE;
        }

        .accordion .card .collapse.show {
            background: #F7FBFE;
        }

        .customer-guide__image-wrapper {
            align-items: center;
            justify-content: center;
        }

        .customer-guide__image-wrapper .customer-guide__image {
            width: 400px;
            height: auto;
        }

        .customer-guide__image-wrapper.show {
            display: flex;
        }

        .customer-guide__image-wrapper.hide {
            display: none;
        }

        @media (max-width: 992px) {
            .show-image {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-5">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold customer-guide__card-title">{{ __('Customer Guide') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="accordion" id="accordion">
                        <div class="card">
                            <div class="card-header" role="tab" id="heading-1">
                                <h6 class="mb-0">
                                    <a data-toggle="collapse" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1" onclick="showImage('web-portal-asset')">
                                        {{ __('Take a queue through the Web Portal') }}
                                    </a>
                                </h6>
                                </div>
                                <div id="collapse-1" class="collapse show" aria-labelledby="heading-1" data-parent="#accordion">
                                <div class="card-body">
                                    {{ __('Customers can access the web address below to take an onsite queue, appointment, or service booking. The web address below can be placed on your website, Instagram, social media, and other information channels of your institution') }}
                                    <div>
                                        <a href="{{ $short_url }}" target="_blank" id="customer-url">{{ $short_url }}</a>
                                        <button class="btn btn-secondary ml-2" onclick="copyToClipboard()">Copy URL</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" role="tab" id="heading-2">
                            <h6 class="mb-0">
                                <a class="collapsed" data-toggle="collapse" href="#collapse-2" aria-expanded="false" aria-controls="collapse-2" onclick="showImage('qr-code-asset')">
                                    {{ __('Take a queue via QR Code') }}
                                </a>
                            </h6>
                            </div>
                            <div id="collapse-2" class="collapse" aria-labelledby="heading-2" data-parent="#accordion">
                            <div class="card-body">
                                {{ __('Customers can scan the Branch QR code. You can print and place this QR code at the Branch entrance') }}
                                <div style="margin-top: 15px;">
                                    <a href="{{ url('') }}/admin-branch/branch-qr-code" class="btn btn-sm btn-primary">{{ __('See QR Code') }}</a>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" role="tab" id="heading-3">
                            <h6 class="mb-0">
                                <a class="collapsed" data-toggle="collapse" href="#collapse-3" aria-expanded="false" aria-controls="collapse-3" onclick="showImage('app-asset')">
                                    {{ __('Take a queue through the KYOO App') }}
                                </a>
                            </h6>
                            </div>
                            <div id="collapse-3" class="collapse" aria-labelledby="heading-3" data-parent="#accordion">
                            <div class="card-body">
                                {{ __('Customers can download the KYOO App to take a queue') }}
                                <div style="width: 145px;height: auto;">
                                <a href="https://play.google.com/store/apps/details?id=com.kyoo.kyoo_app" target="__blank">
                                    <img src="/img/playstore.png" height="70px" />
                                </a>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 show-image">
                        <div id="web-portal-asset" class="customer-guide__image-wrapper show">
                            <img class="customer-guide__image" src="{{ asset('img/customer-guide/web-portal-asset.svg') }}">
                        </div>
                        <div id="qr-code-asset" class="customer-guide__image-wrapper hide">
                            <img class="customer-guide__image" src="{{ asset('img/customer-guide/qr-code-asset.svg') }}">
                        </div>
                        <div id="app-asset" class="customer-guide__image-wrapper hide">
                            <img class="customer-guide__image" src="{{ asset('img/customer-guide/app-asset.svg') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $(".collapse.show").each(function(){
                $(this).prev(".card-header").addClass("highlight");
            });

            $(".card-header a").click(function(){
                $(".card-header").not($(this).parents()).removeClass("highlight");
                $(this).parents(".card-header").toggleClass("highlight");
            });
        })

        function showImage(id) {
            const listOfImageIds = ['web-portal-asset', 'qr-code-asset', 'app-asset'];
            listOfImageIds.forEach((elementID) => {
                const element = document.getElementById(elementID);
                if (elementID == id) {
                    element.classList.remove('hide');
                    element.classList.add('show');
                } else {
                    element.classList.remove('show');
                    element.classList.add('hide');
                }
            });
        }
        function copyToClipboard() {
            const urlEl = document.getElementById('customer-url')
            navigator.clipboard.writeText(urlEl.href)
        }
    </script>
@endpush