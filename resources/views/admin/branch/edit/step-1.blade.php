<h5 class="my-3">
    <span class="badge badge-warning">Step 1 of 3</span>
</h5>
<div class="form-group">
    <label for="name">Name (*)</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name') ?: $branch->name}}" >
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="form-group">
    <label for="industry_category_id">Category (*)</label>
    <select name="industry_category_id" id="industry_category_id" class="form-control @error('industry_category_id') is-invalid @enderror" >
        @foreach ($categories as $category)
            <option value="{{$category->id}}">{{$category->name}}</option>
        @endforeach
    </select>
    @include('layouts.inputError', ['errorName' => 'industry_category_id'])
</div>
<div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="" cols="" rows="" class="form-control @error('description') is-invalid @enderror">{{old('description') ?: $branch->description}}</textarea>
    @include('layouts.inputError', ['errorName' => 'description'])
</div>
<div class="form-group">
    <label for="email">Email (*)</label>
    <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email') ?: $branch->email}}">
    @include('layouts.inputError', ['errorName' => 'email'])
</div>
<div class="form-group">
    <label for="country">Country (*)</label>
    <select name="country" id="country" class="form-control @error('country') is-invalid @enderror" >
        @foreach ($countries as $country)
            <option value="{{$country}}">{{$country}}</option>
        @endforeach
    </select>
    @include('layouts.inputError', ['errorName' => 'country'])
</div>
<div class="form-group">
    <label for="fixed_phone">Fixed Phone</label>
    <input name="fixed_phone" type="text" class="form-control @error('fixed_phone') is-invalid @enderror" value="{{old('fixed_phone') ?: $branch->fixed_phone}}">
    @include('layouts.inputError', ['errorName' => 'fixed_phone'])
</div>
<div class="form-group">
    <label for="mobile_phone">Mobile Phone (*)</label>
    <input name="mobile_phone" type="text" class="form-control @error('mobile_phone') is-invalid @enderror" value="{{old('mobile_phone') ?: $branch->mobile_phone}}" >
    @include('layouts.inputError', ['errorName' => 'mobile_phone'])
</div>
<div class="form-group">
    <label for="logo">Logo (*)</label>
    <br>
    <img src="{{asset('storage/'.$branch->logo)}}" alt="" style="max-height: 100px">
    <br>
    <input name="logo" type="file" class="form-control @error('logo') is-invalid @enderror">
    @include('layouts.inputError', ['errorName' => 'logo'])
</div>
<div class="form-group">
    <label for="photo">Image Background (*)</label>
    <br>
    <img src="{{asset('storage/'.$branch->photo)}}" alt="" style="max-height: 100px">
    <br>
    <input name="photo" type="file" class="form-control @error('photo') is-invalid @enderror">
    @include('layouts.inputError', ['errorName' => 'photo'])
</div>
<div class="form-group">
    <label for="is_active">Show on Mobile (*)</label>
    <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror" >
        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
    @include('layouts.inputError', ['errorName' => 'is_active'])
</div>
@push('js')
    <script>
        $(document).ready(function() {
            const industry_category_idOldValue = '{{ old('industry_category_id') ?: $branch->industry_category_id }}';
            
            if(industry_category_idOldValue !== '') {
                $('#industry_category_id').val(industry_category_idOldValue);
            }

            const countryOldValue = '{{ old('country') ?: $branch->country }}';
            
            if(countryOldValue !== '') {
                $('#country').val(countryOldValue);
            }

            const is_activeOldValue = '{{ old('is_active') ?: $branch->is_active }}';
            
            if(is_activeOldValue !== '') {
                $('#is_active').val(is_activeOldValue);
            }
        });
    </script>
@endpush