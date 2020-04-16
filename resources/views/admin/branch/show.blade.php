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
                                <img src="https://3.bp.blogspot.com/-JWqH64vAzUI/U2dY3LVwlPI/AAAAAAAARSQ/rjgVQ7bBdlg/s1600/RS+ORTHOPEDI+DAN+TRAUMATOLOGI.png" class="img-fluid">    
                                <p class="mt-3">Image Background:</p>
                                <img src="https://rsborromeus.com/wp-content/uploads/2017/02/EB.jpg" class="img-fluid">    
                        </div>
                        <div class="col-md-8">
                            <h5>RS Boromeus Bandung</h5>
                            <div class="table-responsive">
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
                                        <th>Country</th>
                                        <td>Indonesia</td>
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
                                        <th>Show in Mobile?</th>
                                        <td>
                                            <h5 class="badge badge-primary">YA</h5>
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
                                        <td>admin@boromeus.org.id</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>Admin Boromeius</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{route('admin.branch.index')}}" class="btn btn-primary">Back to List</a>
                            <a href="{{route('admin.branch.index')}}" class="btn btn-danger">Suspend Branch</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection