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