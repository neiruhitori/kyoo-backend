<h5 class="my-3">
    <span class="badge badge-warning">Step 2 of 3</span>
</h5>
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
                <option value="{{$branch->regency_id}}">{{$branch->Regency->name}}</option>
            </select>
            @include('layouts.inputError', ['errorName' => 'regency_id'])
        </div>
    </div>
</div>
<div class="form-group">
    <label for="address">Address</label>
    <textarea name="address" id="" cols="" rows="" class="form-control @error('address') is-invalid @enderror">{{old('address') ?: $branch->address}}</textarea>
    @include('layouts.inputError', ['errorName' => 'address'])
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="lat">Lat</label>
            <input name="lat" type="text" class="form-control @error('lat') is-invalid @enderror" value="{{old('lat') ?: $branch->lat}}">
            @include('layouts.inputError', ['errorName' => 'lat'])
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="long">Long</label>
            <input name="long" type="text" class="form-control @error('long') is-invalid @enderror" value="{{old('long') ?: $branch->long}}">
            @include('layouts.inputError', ['errorName' => 'long'])
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name">Get Lat n Long from Maps</label>
    <br>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.011310751414!2d107.5938467146131!3d-6.889247869328712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e69314ebee4b%3A0xbc5831d22e61beeb!2sParis%20Van%20Java%20Resort%20Lifestyle%20Place!5e0!3m2!1sid!2sid!4v1587043514969!5m2!1sid!2sid" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    @include('layouts.inputError', ['errorName' => 'name'])
</div>

@push('js')
    <script>
        $(document).ready(function() {
            const province_idOldValue = '{{ old('province_id') ?: $branch->Regency->province_id }}';
            
            if(province_idOldValue !== '') {
                $('#province_id').val(province_idOldValue);
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
@endpush