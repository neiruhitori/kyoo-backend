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

  <!-- Inter Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">

  <!-- Template CSS -->
  <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

  <script src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
</head>

<body style="display: flex; align-items: stretch;">
  <div class="page-container">
    <div class="content-container">
      <div class="page-header">
        <img src="{{ asset('img/logo-color.svg') }}" class="app-icon" />
      </div>

      <div style="padding: 3rem 0 3rem 0;">
        <div style="margin-bottom: 1.5rem;">
          <h1 class="page-title" style="margin-bottom: 1rem;">Registrasi Cabang</h1>
          <p class="text-gray">Daftarkan Cabangmu Sekarang Juga!</p>
        </div>

        @if (Session::get('error'))
        <div class="alert alert-danger" role="alert">
          {{ Session::get('error') }}
        </div>
        @endif

        <form action="{{ route('registrationBranch.store') }}" method="POST" style="margin-bottom: 1rem;">
          @csrf

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="name">Nama Cabang</label>

            <div class="k-input">
              <div>
                <x-icon icon="home" class="k-icon" />
              </div>

              <input type="text" name="name" id="name" placeholder="Nama Cabang" value="{{ old('name') }}" required>
            </div>

            @error('name')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="industry_category_id">Kategori Industri</label>

            <div class="k-input">
              <div>
                <x-icon icon="file" class="k-icon" />
              </div>

              <select name="industry_category_id" id="industry_category_id" required>
                <option disabled selected>Pilih Kategori</option>
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
            <label class="font-weight-bold" for="queue_type">Jenis Antrian</label>

            <div class="k-input">
              <div>
                <x-icon icon="group" class="k-icon" />
              </div>

              <select name="queue_type" id="queue_type" required>
                <option disabled selected>Pilih Jenis Antrian</option>
                <option value="direct_queue" {{ old('queue_type')=='direct_queue' ? 'selected' : '' }}>
                  Antrian Onsite
                </option>
                <option value="appointment_queue" {{ old('queue_type')=='appointment_queue' ? 'selected' : '' }}>
                  Antrian Appointment
                </option>
                <option value="exhibition_queue" {{ old('queue_type')=='exhibition_queue' ? 'selected' : '' }}>
                  Antrian Exhibition
                </option>
              </select>
            </div>

            @error('queue_type')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div class="k-alert" style="margin-bottom: 1rem;">
            <div style="margin-bottom: 1.125rem;">
              <h6 class="font-weight-bold" style="margin-bottom: 0.25rem;">Antrian Onsite atau Kunjungan di Lokasi</h6>
              <p style="margin-bottom: 0.25rem;">
                Antri langsung di lokasi cabang, tidak perlu janji temu untuk mendapatkan layanan.
              </p>
              <small>Contoh: Antrian di kantor Telekomunikasi dll</small>
            </div>

            <div style="margin-bottom: 1.125rem;">
              <h6 class="font-weight-bold" style="margin-bottom: 0.25rem;">Antrian Appointment atau Janji Temu</h6>
              <p style="margin-bottom: 0.25rem;">
                Diperlukan janji temu atau booking waktu terlebih dahulu untuk mendapatkan layanan. Antrian jenis ini
                memiliki pengaturan kuota.
              </p>
              <small>Contoh: Antrian di Rumah Sakit dll</small>
            </div>

            <div>
              <h6 class="font-weight-bold" style="margin-bottom: 0.25rem;">Antrian Exhibition</h6>
              <p style="margin-bottom: 0.25rem;">
                Diperlukan booking slot terlebih dahulu dan kemudian check-in di lokasi. Antrian jenis ini memiliki
                pengaturan kuota.
              </p>
              <small>Contoh: Konser Musik, Pameran Seni dll</small>
            </div>
          </div>

          <div style="margin-bottom: 1rem;">
            <label class="font-weight-bold" for="email">Email</label>

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
            <label class="font-weight-bold" for="password">Password</label>

            <div class="k-input">
              <div>
                <x-icon icon="key" class="k-icon" />
              </div>

              <input type="password" name="password" id="password" placeholder="Masukkan Password" required>

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
            <label class="font-weight-bold" for="password_confirmation">Konfirmasi Password</label>

            <div class="k-input">
              <div>
                <x-icon icon="key" class="k-icon" aria-hidden="true" />
              </div>

              <input type="password" name="password_confirmation" id="password_confirmation"
                placeholder="Ketik Ulang Password" reuqired>

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
            <label class="font-weight-bold" for="country">Negara</label>

            <div class="k-input">
              <div>
                <x-icon icon="flag" class="k-icon" />
              </div>

              <select name="country" id="country" required>
                <option disabled selected>Pilih Negara</option>
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
            <label class="font-weight-bold" for="phone">Telepon</label>

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
              <label class="font-weight-bold" for="province_id">Provinsi</label>

              <div class="k-input">
                <div>
                  <x-icon icon="chat" class="k-icon" />
                </div>

                <select name="province_id" id="province_id" required>
                  <option disabled selected>Pilih Provinsi</option>
                  @foreach ($provinces as $province)
                  <option value="{{ $province->id }}" {{ old('province_id')==$province->id ? 'selected' : '' }}>
                    {{$province->name}}
                  </option>
                  @endforeach
                </select>
              </div>

              @error('province_id')
              <div class="text-danger mt-2">{{ $message }}</div>
              @enderror
            </div>

            <div style="margin-left: 1rem">
              <label class="font-weight-bold" for="regency_id">Kota</label>

              <div class="k-input">
                <div>
                  <x-icon icon="point" class="k-icon" />
                </div>

                <select name="regency_id" id="regency_id" required>
                  <option disabled selected>Pilih Kota</option>
                </select>
              </div>

              @error('regency_id')
              <div class="text-danger mt-2">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div style="margin-bottom: 1rem">
            <label class="font-weight-bold" for="address">Alamat</label>

            <div class="k-input">
              <textarea name="address" id="address" placeholder="Tulis alamat" required>{{ old('address') }}</textarea>
            </div>

            @error('address')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1rem">
            {!! NoCaptcha::renderJs() !!}
            {!! NoCaptcha::display() !!}

            @error('g-recaptcha-response')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div style="margin-bottom: 1.5rem;">
            <div class="k-checkbox">
              <input type="checkbox" name="accept_term_condition" id="accept_term_condition">
              <label for="accept_term_condition">
                Saya menyetujui <a href="https://kyoo.id/termsandconditions">Syarat dan Ketentuan yang Berlaku</a>
              </label>
            </div>

            @error('accept_term_condition')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="k-button">Daftar Sekarang</button>
        </form>

        <p class="text-gray">
          Sudah punya akun? <a href="{{ route('login') }}">Login</a>
        </p>
      </div>
    </div>
  </div>
  <div class="image-container">
    <img class="page-illustration" src="{{ asset('img/illustrations/registration.svg') }}">
  </div>

  <script>
    $(document).ready(function() {
      $('#country').val('Indonesia')

      if ('{{ old('province_id') }}') {
        fetchRegencies('{{ old('province_id') }}')
      }

      $('#province_id').change(() => {
        let provinceId = $('#province_id').val()
        fetchRegencies(provinceId)
      })

      $('.is-password-visible').click(function () {
        const input = $(this).parents().siblings('.k-input input')

        input.attr('type') === 'password'
          ? input.attr('type', 'text')
          : input.attr('type', 'password')
      })
    })

    function fetchRegencies(provinceId) {
      const oldRegency = '{{ old('regency_id') }}'

      fetch(`/api/regency/${provinceId}`)
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