<h5 class="my-3">
    <span class="badge badge-primary">{{ __('step.total', ['current' => 1, 'total' => 3]) }}</span>
</h5>

@isset($corporate)
    <div class="form-group">
        <label for="name">Corporate</label>
        <input
            class="form-control"
            name="corporate_name"
            type="text"
            value="{{ $corporate->name }}"
            required
            readonly
        >
    </div>

    <input type="hidden" name="corporate_id" value={{ $corporate->id }}>
@endisset

<div class="form-group">
    <label for="name">{{ __('name.module', ['module' => __('Branch')]) }} (*)</label>
    <input
        class="form-control @error('name') is-invalid @enderror"
        name="name"
        type="text"
        value="{{ old('name') }}"
        required
    >
    @include('layouts.inputError', ['errorName' => 'name'])
</div>

<div class="form-group">
    <label for="industry_category_id">{{ __('Category') }} (*)</label>
    <select
        class="form-control @error('industry_category_id') is-invalid @enderror"
        id="industry_category_id"
        name="industry_category_id"
        required
    >
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    @include('layouts.inputError', ['errorName' => 'industry_category_id'])
</div>

<div class="form-group">
    <label for="branch_type_id">{{ __('Branch License') }} (*)</label>
    <select
        class="form-control @error('branch_type_id') is-invalid @enderror"
        id="branch_type_id"
        name="branch_type_id"
    >
        @foreach ($branchTypes as $branchType)
            <option value="{{ $branchType->id }}">{{ $branchType->code }} - {{ $branchType->name }}</option>
        @endforeach
    </select>
    @include('layouts.inputError', ['errorName' => 'branch_type_id'])
</div>

<div class="form-group">
    <label for="max_counter">{{ __('Max Counter') }}</label>
    <input
        class="form-control @error('max_counter') is-invalid @enderror"
        name="max_counter"
        type="number"
        value="{{ old('max_counter') }}"
        min="1"
        max="20"
    >
    @include('layouts.inputError', ['errorName' => 'max_counter'])
</div>

<div class="form-group">
    <label for="description">{{ __('Description') }}</label>
    <textarea
        class="form-control @error('description') is-invalid @enderror"
        id=""
        name="description"
        cols=""
        rows=""
    >{{ old('description') }}</textarea>
    @include('layouts.inputError', ['errorName' => 'description'])
</div>

<div class="form-group">
    <label for="email">{{ __('Email') }} (*)</label>
    <input
        class="form-control @error('email') is-invalid @enderror"
        name="email"
        type="email"
        value="{{ old('email') }}"
    >
    @include('layouts.inputError', ['errorName' => 'email'])
</div>

<div class="form-group">
    <label for="country">{{ __('Country') }} (*)</label>
    <select
        class="form-control @error('country') is-invalid @enderror"
        id="country"
        name="country"
        required
    >
        @foreach ($countries as $country)
            <option value="{{ $country }}">{{ $country }}</option>
        @endforeach
    </select>
    @include('layouts.inputError', ['errorName' => 'country'])
</div>

<div class="form-group">
    <label for="fixed_phone">{{ __('Fixed Phone') }}</label>
    <input
        class="form-control @error('fixed_phone') is-invalid @enderror"
        name="fixed_phone"
        type="text"
        value="{{ old('fixed_phone') }}"
    >
    @include('layouts.inputError', ['errorName' => 'fixed_phone'])
</div>

<div class="form-group">
    <label for="mobile_phone">{{ __('Mobile Phone') }} (*)</label>
    <input
        class="form-control @error('mobile_phone') is-invalid @enderror"
        name="mobile_phone"
        type="text"
        value="{{ old('mobile_phone') }}"
        required
    >
    @include('layouts.inputError', ['errorName' => 'mobile_phone'])
</div>

<div class="form-group">
    <label for="logo">{{ __('Logo') }} (*)</label>
    <input
        class="form-control @error('logo') is-invalid @enderror"
        name="logo"
        type="file"
        required
    >
    @include('layouts.inputError', ['errorName' => 'logo'])
</div>

<div class="form-group">
    <label for="photo">{{ __('Image Background') }} (*)</label>
    <input
        class="form-control @error('photo') is-invalid @enderror"
        name="photo"
        type="file"
        required
    >
    @include('layouts.inputError', ['errorName' => 'photo'])
</div>

<div class="form-group">
    <label for="is_active">{{ __('Show in Mobile') }} (*)</label>
    <select
        class="form-control @error('is_active') is-invalid @enderror"
        id="is_active"
        name="is_active"
        required
    >
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
    @include('layouts.inputError', ['errorName' => 'is_active'])
</div>

@push('js')
    <script>
        $(document).ready(function() {
            const industry_category_idOldValue = '{{ old('industry_category_id') }}';

            if (industry_category_idOldValue !== '') {
                $('#industry_category_id').val(industry_category_idOldValue);
            }

            const countryOldValue = '{{ old('country') ?: 'Indonesia' }}';

            if (countryOldValue !== '') {
                $('#country').val(countryOldValue);
            }

            const is_activeOldValue = '{{ old('is_active') }}';

            if (is_activeOldValue !== '') {
                $('#is_active').val(is_activeOldValue);
            }

            const branch_type_idOldValue = '{{ old('branch_type_id') }}';

            if (branch_type_idOldValue !== '') {
                $('#branch_type_id').val(branch_type_idOldValue);
            }
        });
    </script>
@endpush
