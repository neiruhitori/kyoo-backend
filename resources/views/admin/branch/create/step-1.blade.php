<h5 class="my-3">
    <span class="badge badge-primary">Step 1 of 3</span>
</h5>
<div class="form-group">
    <label for="name">Name</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Category</label>
    <select name="" id="" class="form-control">
        <option value="">Healtcare</option>
    </select>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Description</label>
    <textarea name="" id="" cols="" rows="" class="form-control"></textarea>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Email</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Country</label>
    <select name="" id="" class="form-control">
        <option value="">Indonesia</option>
    </select>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Fixed Phone</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Mobile Phone</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Logo</label>
    <input name="name" type="file" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Image Background</label>
    <input name="name" type="file" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Show on Mobile</label>
    <select name="" id="" class="form-control">
        <option value="">Yes</option>
    </select>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="name">Follow Template Schedule</label>
    <select name="" id="" class="form-control">
        <option value="">Yes</option>
    </select>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>