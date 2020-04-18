<h5 class="my-3">
    <span class="badge badge-primary">Step 3 of 3</span>
</h5>
<div class="form-group">
    <label for="name">Name</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Email</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Phone</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Password</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Confirmation Password</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<button class="btn btn-primary fullwidth">Save</button>