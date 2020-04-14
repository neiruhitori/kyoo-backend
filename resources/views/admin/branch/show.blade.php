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
                        <div class="col-md-3">
                                <img src="https://rsborromeus.com/wp-content/uploads/2017/02/EB.jpg" class="img-fluid">    
                        </div>
                        <div class="col-md-9">
                            <h5>RS Boromeus Bandung</h5>
                            <table class="table">
                                <tr>
                                    <th colspan="2">Profile</th>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>Healtcare</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>boromeusbandung@gmail.com</td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td>2552000</td>
                                </tr>
                                <tr>
                                    <th>Services Likes</th>
                                    <td>2023</td>
                                </tr>
                                <tr>
                                    <th colspan="2">Location Detail</th>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>Jl. Ir. H. Juanda No.100, Lebakgede, Kecamatan Coblong, Kota Bandung, Jawa Barat 40132</td>
                                </tr>
                                <tr>
                                    <th>Latitude</th>
                                    <td>1,24512</td>
                                </tr>
                                <tr>
                                    <th>Longitude</th>
                                    <td>2,1241123</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{route('admin.branch.index')}}" class="btn btn-primary">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection