@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Branch</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <h5>{{$branch->name}}</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th colspan="2">
                                            <h5>Profile</h5>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{$branch->IndustryCategory->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{$branch->email}}</td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td>{{$branch->country}}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{$branch->phone}}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{$branch->address}}, {{$branch->Regency->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Verified Email At</th>
                                        <td>{{$branch->updated_at->format('D, d-m-Y H:i')}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form action="{{route('admin.registrationBranch.update', $branch->id)}}" method="post" style="display: inline">
                                @csrf
                                @method('PUT')
                                <button class="btn btn-primary">Approve</button>
                            </form>
                            <form action="{{route('admin.registrationBranch.destroy', $branch->id)}}" method="post" style="display: inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger ml-5">Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection