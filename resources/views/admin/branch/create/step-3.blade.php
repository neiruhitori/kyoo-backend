<h5 class="my-3">
    <span class="badge badge-primary">Step 3 of 3</span>
</h5>
<div class="form-group">
    <label for="admin_name">Name</label>
    <input name="admin_name" type="text" class="form-control @error('admin_name')}} is-invalid @enderror" value="{{old('admin_name')}}" required>
    @include('layouts.inputError', ['errorName' => 'admin_name'])
</div>
<div class="form-group">
    <label for="admin_email">Email</label>
    <input name="admin_email" type="email" class="form-control @error('admin_email') is-invalid @enderror" value="{{old('admin_email')}}" required>
    @include('layouts.inputError', ['errorName' => 'admin_email'])
</div>
<div class="form-group">
    <label for="admin_phone">Phone</label>
    <input name="admin_phone" type="text" class="form-control @error('admin_phone') is-invalid @enderror" value="{{old('admin_phone')}}" required>
    @include('layouts.inputError', ['errorName' => 'admin_phone'])
</div>
<div class="form-group">
    <label for="admin_password">Password</label>
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
    <input name="admin_password" type="password" class="form-control @error('admin_password') is-invalid @enderror" value="{{old('admin_password')}}" required>
    @include('layouts.inputError', ['errorName' => 'admin_password'])
</div>
<div class="form-group">
    <label for="admin_password_confirmation">Confirmation Password</label>
    <input name="admin_password_confirmation" type="password" class="form-control @error('admin_password_confirmation') is-invalid @enderror" value="{{old('admin_password_confirmation')}}" required>
    @include('layouts.inputError', ['errorName' => 'admin_password_confirmation'])
</div>