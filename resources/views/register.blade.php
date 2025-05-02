<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Kyoo Admin</title>
  <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css"/>
  <!-- Inter Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">
  <!-- Bootstrap core JavaScript-->
  <script type="text/javascript" src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script>
  <script src="{{asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- Template CSS -->
  <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

  <script src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
</head>

<body style="display: flex; align-items: stretch;">
  <div class="page-container">
    <div class="content-container">
      <div class="page-header d-flex justify-content-between">
        <img src="{{ asset('img/logo-color.svg') }}" class="app-icon" />

        <div class="dropdown align-self-center lang">
          <button class="btn dropdown-toggle " type="button" data-toggle="dropdown">
            <span class="fi fi-{{ app()->getLocale() == 'en' ? 'gb' : 'id'}} fib border"></span>
             {{-- below is lang  --}}
            {{ strtoupper(app()->getLocale()) }}
          </button>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('change.locale', 'en') }}">
              <span class="fi fi-gb fib border"></span> English
          </a>
          <a class="dropdown-item" href="{{ route('change.locale', 'id') }}">
              <span class="fi fi-id fib border"></span> Bahasa
          </a>
          </div>
        </div>
      </div>

      <div style="padding: 3rem 0 3rem 0;">
        <div style="margin-bottom: 1.5rem;">
          <h1 class="page-title" style="margin-bottom: 1rem;">{{ __('Branch Registration') }}</h1>
          <p class="text-gray">{{ __('Register Your Branch Now!') }}</p>
        </div>

        @if (Session::get('error'))
        <div class="alert alert-danger" role="alert">
          {{ Session::get('error') }}
        </div>
        @endif

        <form action="{{ route('registrationBranch.store') }}" method="POST" style="margin-bottom: 1rem;">
          @csrf

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="name">{{ __('Branch Name') }}</label>

            <div class="k-input">
              <div>
                <x-icon icon="home" class="k-icon" />
              </div>

              <input type="text" name="name" id="name" placeholder="{{ __('Branch Name') }}" value="{{ old('name') }}" required>
            </div>

            @error('name')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="industry_category_id">{{ __('Industry Category') }}</label>

            <div class="k-input">
              <div>
                <x-icon icon="file" class="k-icon" />
              </div>

              <select name="industry_category_id" id="industry_category_id" required>
                <option disabled selected>{{ __('Select Category') }}</option>
                @foreach ($categories as $category)
                <option value="{{$category->id}}" {{ old('industry_category_id')==$category->id ? 'selected' : '' }}>
                  {{$category->name}}
                </option>
                @endforeach
              </select>
            </div>

            @error('industry_category_id')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="queue_type">{{ __('Queue Type') }}</label>

            <div class="k-input">
              <div>
                <x-icon icon="group" class="k-icon" />
              </div>

             
              <select name="queue_type" id="queue_type" required>
                <option disabled selected>{{ __('Select Queue Type') }}</option>
                
                <option value="direct_queue" {{ old('queue_type')=='direct_queue' ? 'selected' : '' }}>
                  {{ __('Direct Queue') }}
                </option>
                <option value="appointment_queue" {{ old('queue_type')=='appointment_queue' ? 'selected' : '' }}>
                  {{ __('Appointment Queue') }}
                </option>
                @if (app()->getLocale() != 'en')
                <option value="exhibition_queue" {{ old('queue_type')=='exhibition_queue' ? 'selected' : '' }}>
                  {{ __('Exhibition Queue') }}
                </option>
                @else
              @endif
              </select>
            </div>

            @error('queue_type')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div class="k-alert" style="margin-bottom: 1rem;">
            <div style="margin-bottom: 1.125rem;">
              <h6 class="font-weight-bold" style="margin-bottom: 0.25rem;">{{ __('register.onsite') }}</h6>
              <p style="margin-bottom: 0.25rem;">
                {{ __('register.onsite_desc') }}
              </p>
              <small>{{ __('register.onsite_ex') }}</small>
            </div>

            <div style="margin-bottom: 1.125rem;">
              <h6 class="font-weight-bold" style="margin-bottom: 0.25rem;">{{ __('register.appointment') }}</h6>
              <p style="margin-bottom: 0.25rem;">
                {{ __('register.appointment_desc') }}
              </p>
              <small>{{ __('register.appointment_ex') }}</small>
            </div>

            <div>
              <h6 class="font-weight-bold" style="margin-bottom: 0.25rem;">{{ __('register.exhibition') }}</h6>
              <p style="margin-bottom: 0.25rem;">
                {{ __('register.exhibition_desc') }}
              </p>
              <small>{{ __('register.exhibition_ex') }}</small>
            </div>
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="email">{{ __('Email') }}</label>

            <div class="k-input">
              <div>
                <x-icon icon="letter" class="k-icon" />
              </div>
              <input type="email" name="email" id="email" placeholder="mail@website.com" value="{{ old('email') }}"
                required>
            </div>

            @error('email')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="password">{{ __('Password') }}</label>

            <div class="k-input">
              <div>
                <x-icon icon="key" class="k-icon" />
              </div>

              <input type="password" name="password" id="password" placeholder="Password" required>

              <div>
                <button type="button" class="k-button-text is-password-visible">
                  <x-icon icon="eyeClosed" />
                </button>
              </div>
            </div>

            @error('password')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="password_confirmation">{{ __('Password Confirmation') }}</label>

            <div class="k-input">
              <div>
                <x-icon icon="key" class="k-icon" aria-hidden="true" />
              </div>

              <input type="password" name="password_confirmation" id="password_confirmation"
                placeholder="{{ __('Password Confirmation') }}" reuqired>

              <div>
                <button type="button" class="k-button-text is-password-visible">
                  <x-icon icon="eyeClosed" />
                </button>
              </div>
            </div>

            @error('password_confirmation')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="country">{{ __('Country') }}</label>

            <div class="k-input">
              <div>
                <x-icon icon="flag" class="k-icon" />
              </div>

              <select name="country" id="country" required>
                <option disabled selected>{{ __('Select Country') }}</option>
                @foreach ($countries as $country)
                <option value="{{$country}}" {{ old('country')==$country ? 'selected' : '' }}>
                  {{$country}}
                </option>
                @endforeach
              </select>
            </div>

            @error('country')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="phone">{{ __('Phone') }}</label>

            <div class="k-input">
              <div>
                <x-icon icon="phone" class="k-icon" />
              </div>

              <input type="tel" name="phone" id="phone" placeholder="Ch.+628***" value="{{ old('phone') }}" required>
            </div>

            @error('phone')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div class="inline" style="margin-bottom: 1rem; margin-left: -1rem;">
            <div style="margin-left: 1rem;">
              <label class="font-weight-bold" for="province_id">{{ __('Province') }}</label>

              <div class="k-input">
                <div>
                  <x-icon icon="chat" class="k-icon" />
                </div>
                
                <select name="province_id" id="province_id" required>
                  <option disabled selected>{{ __('Select Province') }}</option>
                </select>
                
                {{-- <select name="province_id" id="province_id" required>
                  <option disabled selected>{{ __('Select Province') }}</option>
                  @foreach ($provinces as $province)
                  <option value="{{ $province->id }}" {{ old('province_id')==$province->id ? 'selected' : '' }}>
                    {{$province->name}}
                  </option>
                  @endforeach
                </select> --}}
              </div>

              @error('province_id')
              <div class="text-danger mt-2">{{ $message }}</div>
              @enderror
            </div>

            <div style="margin-left: 1rem">
              <label class="font-weight-bold" for="regency_id">{{ __('City') }}</label>

              <div class="k-input">
                <div>
                  <x-icon icon="point" class="k-icon" />
                </div>

                <select name="regency_id" id="regency_id" required>
                  <option disabled selected>{{ __('Select City') }}</option>
                </select>
              </div>

              @error('regency_id')
              <div class="text-danger mt-2">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div style="margin-bottom: 1rem">
            <label class="font-weight-bold" for="address">{{ __('Address') }}</label>

            <div class="k-input">
              <textarea name="address" id="address" placeholder="{{ __('Address') }}" required>{{ old('address') }}</textarea>
            </div>

            @error('address')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1.5rem;">
            <div class="k-checkbox">
              <input type="checkbox" name="accept_term_condition" id="accept_term_condition">
              <label for="accept_term_condition">
                {{ __('I agree to the') }} 
                <a href="{{ app()->getLocale() == 'en' ? 'https://worldwide.kyoo.id/term_condition.html' : 'https://kyoo.id/termsandconditions' }}">
                  {{ __('Terms and Conditions') }}</a>
              </label>
            </div>

            @error('accept_term_condition')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="k-button">{{ __('Register Now') }}</button>
        </form>

        <p class="text-gray">
          {{ __('Already have an account?') }} <a href="{{ route('login') }}">Login</a>
        </p>
      </div>
    </div>
  </div>
  <div class="image-container">
    <div class="dropdown position-absolute" style="
    top: 55px;
    right: 135px;
    z-index: 4;
">
      <button class="btn dropdown-toggle " type="button" data-toggle="dropdown">
        <span class="fi fi-{{ app()->getLocale() == 'en' ? 'gb' : 'id'}} fib border"></span>
         {{-- below is lang  --}}
        {{ strtoupper(app()->getLocale()) }}
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="{{ route('change.locale', 'en') }}">
          <span class="fi fi-gb fib border"></span> English
      </a>
      <a class="dropdown-item" href="{{ route('change.locale', 'id') }}">
          <span class="fi fi-id fib border"></span> Bahasa
      </a>
      </div>
    </div>
    <img class="page-illustration" src="{{ asset('img/illustrations/in-line.svg') }}">
    {{-- <img class="page-illustration" src="{{ asset('img/illustrations/registration.svg') }}"> --}}
  </div>


  <script>
    $(document).ready(function() {
      // $('#country').val('ID')

      if ('{{ old('province_id') }}') {
        fetchRegencies('{{ old('province_id') }}')
      }

      $('#province_id').change(() => {
        let provinceId = $('#province_id').val()
        let country = $('#country').val()
        $('#regency_id').html('<option disabled selected>{{ __("Select City") }}</option>');
        fetchRegencies(country,provinceId)
      })

      $('#country').change(() => {
        let country = $('#country').val()
        $('#province_id').html('<option disabled selected>{{ __("Select Province") }}</option>');
        $('#regency_id').html('<option disabled selected>{{ __("Select City") }}</option>');
        fetchProvinces(country)
      })

      $('.is-password-visible').click(function () {
        const input = $(this).parents().siblings('.k-input input')

        input.attr('type') === 'password'
          ? input.attr('type', 'text')
          : input.attr('type', 'password')
      })
    })

    function fetchProvinces(country){
      fetch(`/api/allProvince/${country}`)
        .then(res => res.json())
        .then(data => {
          $('#province_id option:not(:disabled)').remove()
          data.data.forEach(province => {
            const provinceSelect = $('#province_id')

            provinceSelect.append($("<option></option>")
              .attr("value", province.id)
              .text(province.name));
          });
        })
        .catch(err => console.log(err))
    }

    function fetchRegencies(country,provinceId) {
      const oldRegency = '{{ old('regency_id') }}'

      fetch(`/api/regency/${country}/${provinceId}`)
        .then(res => res.json())
        .then(data => {
          $('#regency_id option:not(:disabled)').remove()
          data.data.forEach(regency => {
            const regencySelect = $('#regency_id')

            regencySelect.append($("<option></option>")
              .attr("value", regency.id)
              .text(regency.name));
          });

          if (oldRegency) {
            console.log(oldRegency)

            $('#regency_id').val(oldRegency)
          }
        })
        .catch(err => console.log(err))
    }
  </script>
</body>

</html>