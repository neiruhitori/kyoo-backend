<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Kyoo Admin</title>

  <!-- Custom fonts for this template-->
  <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{asset('admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
  <link rel="icon" href="{{ asset('img/logo-icon.svg') }}" type="image/icon type">
  <style>
    .bg-gradient-primary {
      background: linear-gradient(121.16deg, #189DCD 0.95%, #0A5194 97.59%);
    }
    .fullwidth {
        width: 100%;
    }
  </style>
</head>

<body class="bg-gradient-primary">

  <div class="container pt-5">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Regist your Branch here</h6>
                    </div>
                    <div class="card-body">
                        <img src="{{asset('img/logo-color.svg')}}" alt="" class="mb-3">
                        <div class="row">
                            <div class="col-md-12">
                                @include('layouts.alert')
                                <form action="{{route('registrationBranch.store')}}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
                                        @include('layouts.inputError', ['errorName' => 'name'])
                                    </div>
                                    <div class="form-group">
                                        <label for="industry_category_id">Industry Category</label>
                                        <select name="industry_category_id" id="industry_category_id" class="form-control @error('industry_category_id') is-invalid @enderror" required>
                                            @foreach ($categories as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'industry_category_id'])
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email')}}" required>
                                        @include('layouts.inputError', ['errorName' => 'email'])
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <br>
                                        <small>
                                            rules:
                                            <ul>
                                                <li>must be at least 8 characters in length</li>
                                                <li>must contain at least one lowercase letter</li>
                                                <li>must contain at least one uppercase letter</li>
                                                <li>must contain at least one digit</li>
                                            </ul>
                                        </small>
                                        <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" value="{{old('password')}}" required>
                                        @include('layouts.inputError', ['errorName' => 'password'])
                                    </div>
                                    <div class="form-group">
                                        <label for="password_confirmation">Password Confirmation</label>
                                        <input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" value="{{old('password_confirmation')}}" required>
                                        @include('layouts.inputError', ['errorName' => 'password_confirmation'])
                                    </div>
                                    <div class="form-group">
                                        <label for="country">Country</label>
                                        <select name="country" id="country" class="form-control @error('country') is-invalid @enderror" required>
                                            @foreach ($countries as $country)
                                                <option value="{{$country}}">{{$country}}</option>
                                            @endforeach
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'country'])
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Mobile Phone</label>
                                        <input name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{old('phone')}}" required>
                                        @include('layouts.inputError', ['errorName' => 'phone'])
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="province_id">Province</label>
                                                <select name="province_id" id="province_id" class="form-control @error('province_id') is-invalid @enderror">
                                                    @foreach ($provinces as $province)
                                                        <option value="{{$province->id}}">{{$province->name}}</option>
                                                    @endforeach
                                                </select>
                                                @include('layouts.inputError', ['errorName' => 'province_id'])
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="regency_id">City</label>
                                                <select name="regency_id" id="regency_id" class="form-control">
                                                    <option value="">Choose City</option>
                                                </select>
                                                @include('layouts.inputError', ['errorName' => 'regency_id'])
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea name="address" id="" cols="" rows="" class="form-control @error('address') is-invalid @enderror">{{old('address')}}</textarea>
                                        @include('layouts.inputError', ['errorName' => 'address'])
                                    </div>
                                    <div class="form-group">
                                        <label for="captcha">Captcha</label>
                                        {!! NoCaptcha::renderJs() !!}
                                        {!! NoCaptcha::display() !!}
                                        <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                                    </div>
                                    <button class="btn btn-primary fullwidth">Register</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{asset('admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{asset('admin/js/sb-admin-2.min.js')}}"></script>
  <script>
        $(document).ready(function() {
            const industry_category_idOldValue = '{{ old('industry_category_id') }}';
            
            if(industry_category_idOldValue !== '') {
                $('#industry_category_id').val(industry_category_idOldValue);
            }

            const countryOldValue = '{{ old('country') ?: "Indonesia" }}';
            
            if(countryOldValue !== '') {
                $('#country').val(countryOldValue);
            }

            $('#province_id').change(() => {
                let provinceId = $('#province_id').val()
                fetch(`/api/regency/${provinceId}`)
                    .then(res => res.json())
                    .then(data => {
                        $('#regency_id option').remove()
                        data.data.forEach(regency => {
                            $('#regency_id')
                                .append($("<option></option>")
                                .attr("value", regency.id)
                                .text(regency.name)); 
                        });
                    })
                    .catch(err => console.log(err))
            })
        });
    </script>
</body>

</html>
