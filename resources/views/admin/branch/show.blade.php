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
                        <div class="col-md-4">
                                <p>Logo:</p>
                                <img src="{{asset('storage/'.$branch->logo)}}" class="img-fluid">    
                                <p class="mt-3">Image Background:</p>
                                <img src="{{asset('storage/'.$branch->photo)}}" class="img-fluid">    
                        </div>
                        <div class="col-md-8">
                            <h5>{{$branch->name}}</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th colspan="2">Profile</th>
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
                                        <th>Phone Number</th>
                                        <td>
                                            {{$branch->fixed_phone}}
                                            @isset($branch->mobile_phone)
                                                / {{$branch->mobile_phone}}
                                            @endisset
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Services Likes</th>
                                        <td>{{number_format($branch->likes)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Show in Mobile?</th>
                                        <td>
                                            @if ($branch->is_active)
                                                <h5 class="badge badge-primary">Yes</h5>
                                            @else
                                                <h5 class="badge badge-danger">No</h5>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            <br>
                                            <br>
                                            Location Detail
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{$branch->address}}, {{$branch->Regency->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Latitude</th>
                                        <td>{{$branch->lat}}</td>
                                    </tr>
                                    <tr>
                                        <th>Longitude</th>
                                        <td>{{$branch->long}}</td>
                                    </tr>
                                    <tr>
                                        <th>Maps</th>
                                        <td>
                                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.011310751414!2d107.5938467146131!3d-6.889247869328712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e69314ebee4b%3A0xbc5831d22e61beeb!2sParis%20Van%20Java%20Resort%20Lifestyle%20Place!5e0!3m2!1sid!2sid!4v1587043514969!5m2!1sid!2sid" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            <br>
                                            <br>
                                            Services Days and Times
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Monday</th>
                                        <td>10.00 - 18.00</td>
                                    </tr>
                                    <tr>
                                        <th>Tuesday</th>
                                        <td>10.00 - 18.00</td>
                                    </tr>
                                    <tr>
                                        <th>Wednesday</th>
                                        <td>10.00 - 18.00</td>
                                    </tr>
                                    <tr>
                                        <th>Thursday</th>
                                        <td>10.00 - 18.00</td>
                                    </tr>
                                    <tr>
                                        <th>Friday</th>
                                        <td>10.00 - 18.00</td>
                                    </tr>
                                    <tr>
                                        <th>Saturday</th>
                                        <td>10.00 - 12.00</td>
                                    </tr>
                                    <tr>
                                        <th>Sunday</th>
                                        <td>Close</td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            <br>
                                            <br>
                                            Admin Account
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{$branch->Admin[0]->email}}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{$branch->Admin[0]->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{$branch->Admin[0]->phone}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{route('admin.branch.index')}}" class="btn btn-primary">Back to List</a>
                            <form action="{{route('admin.branch.destroy', $branch->id)}}" method="post" style="display: inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Remove Branch">
                                    Supend Branch
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection