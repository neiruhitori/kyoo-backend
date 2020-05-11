@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Branch: {{$branch->name}}</h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('adminBranch.branch.update')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id" value="{{$branch->id}}">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Branch Profile</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="location-tab" data-toggle="tab" href="#location" role="tab" aria-controls="location" aria-selected="false">Branch Location</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        @include('adminBranch.branch.edit.step-1')
                                    </div>
                                    <div class="tab-pane fade" id="location" role="tabpanel" aria-labelledby="location-tab">
                                        @include('adminBranch.branch.edit.step-2')
                                    </div>
                                </div>
                                <button class="btn btn-warning fullwidth mb-3">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection