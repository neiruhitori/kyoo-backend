@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('edit.module', ['module' => __('Branch')]) }}:
                    {{$branch->name}}</h6>
            </div>
            @csrf
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('admin.branch.update', $branch->id)}}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{$branch->id}}">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile"
                                        role="tab" aria-controls="profile" aria-selected="true">
                                        {{ __('Branch Profile') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="location-tab" data-toggle="tab" href="#location" role="tab"
                                        aria-controls="location" aria-selected="false">
                                        {{ __('Branch Location') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="admin-tab" data-toggle="tab" href="#admin" role="tab"
                                        aria-controls="admin" aria-selected="false">
                                        {{ __('Branch Admin') }}
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="profile" role="tabpanel"
                                    aria-labelledby="profile-tab">
                                    @include('admin.branch.edit.step-1')
                                </div>
                                <div class="tab-pane fade" id="location" role="tabpanel" aria-labelledby="location-tab">
                                    @include('admin.branch.edit.step-2')
                                </div>
                                <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                                    @include('admin.branch.edit.step-3')
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning fullwidth mb-3">{{ __('Update') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    $(document).on('click', 'form button[type=submit]', function(e) {
            // PAGE 1
            let name = $("input[name=name]").val();
            let industry_category_id = $("select[name=industry_category_id]").val();
            let email = $("input[name=email]").val();
            let country = $("select[name=country]").val();
            let mobile_phone = $("input[name=mobile_phone]").val();
            let is_active = $("select[name=is_active]").val();
            
            if (!name || !industry_category_id || !email || !country || !mobile_phone || !is_active) {
                e.preventDefault();
                swal('', '{{ __('page.mandatory', ['page' => 'Branch Profile']) }}', 'info')
                return
            }

            // PAGE 2
            let lat = $("input[name=lat]").val();
            let long = $("input[name=long]").val();
            let province_id = $("select[name=province_id]").val();
            let regency_id = $("select[name=regency_id]").val();
            let address = $("textarea[name=address]").val();

            if (!lat || !long || !province_id || !regency_id || !address) {
                e.preventDefault();
                swal('', {{ __('page.mandatory', ['page' => 'Branch Location']) }}, 'info')
                return
            }

            // PAGE 3
            let admin_name = $("input[name=admin_name]").val();
            let admin_email = $("input[name=admin_email]").val();
            let admin_phone = $("input[name=admin_phone]").val();
            
            if (!admin_name || !admin_email || !admin_phone) {
                e.preventDefault();
                swal('', {{ __('page.mandatory', ['page' => 'Branch Admin']) }}, 'info')
                return
            }
        });
</script>
@endpush