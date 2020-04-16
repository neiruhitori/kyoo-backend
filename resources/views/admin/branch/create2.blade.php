<h5 class="my-3">
    <span class="badge badge-primary">Step 2 of 3</span>
</h5>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Province</label>
            <select name="" id="" class="form-control">
                <option value="">Jawa Timur</option>
            </select>
            @include('layouts.inputError', ['errorName' => 'name'])
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">City</label>
            <select name="" id="" class="form-control">
                <option value="">Malang</option>
            </select>
            @include('layouts.inputError', ['errorName' => 'name'])
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name">Address</label>
    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Lat</label>
            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" disabled value="1,2234">
            @include('layouts.inputError', ['errorName' => 'name'])
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Long</label>
            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" disabled value="1,2234">
            @include('layouts.inputError', ['errorName' => 'name'])
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name">Get Lat n Long from Maps</label>
    <br>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.011310751414!2d107.5938467146131!3d-6.889247869328712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e69314ebee4b%3A0xbc5831d22e61beeb!2sParis%20Van%20Java%20Resort%20Lifestyle%20Place!5e0!3m2!1sid!2sid!4v1587043514969!5m2!1sid!2sid" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>
