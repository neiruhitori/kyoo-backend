<h5 class="my-3">
    <span class="badge badge-warning">{{ __('step.total', ['current' => 3, 'total' => 3]) }}</span>
</h5>
<div class="form-group">
    <label for="admin_name">{{ __('Name') }} (*)</label>
    <input name="admin_name" type="text" class="form-control @error('admin_name')}} is-invalid @enderror"
        value="{{old('admin_name') ?: $branch->Admin[0]->name}}">
    @include('layouts.inputError', ['errorName' => 'admin_name'])
</div>
<div class="form-group">
    <label for="admin_email">{{ __('Email') }} (*)</label>
    <input name="admin_email" type="email" class="form-control @error('admin_email') is-invalid @enderror"
        value="{{old('admin_email') ?: $branch->Admin[0]->email}}">
    @include('layouts.inputError', ['errorName' => 'admin_email'])
</div>
<div class="form-group">
    <label for="admin_phone">{{ __('Phone') }} (*)</label>
    <input name="admin_phone" type="text" class="form-control @error('admin_phone') is-invalid @enderror"
        value="{{old('admin_phone') ?: $branch->Admin[0]->phone}}">
    @include('layouts.inputError', ['errorName' => 'admin_phone'])
</div>