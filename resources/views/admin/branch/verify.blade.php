@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">List need to Verify</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Logo</th>
                                            <th>Category</th>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <img src="https://rsborromeus.com/wp-content/uploads/2017/02/EB.jpg" style="max-width: 200px">    
                                            </td>
                                            <td>Healtcare</td>
                                            <td>RS Boromeus Bandung</td>
                                            <td>Jl. Ir. H. Juanda No.100, Lebakgede, Kecamatan Coblong, Kota Bandung, Jawa Barat 40132</td>
                                            <td>Tue, 14 Apr 2020</td>
                                            <td>
                                                <a href="{{route('admin.branch.show', 1)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Show Branch">
                                                    <i class="fas fa-fw fa-eye"></i>
                                                </a>
                                                <a href="{{route('admin.branch.show', 1)}}" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Show Branch">
                                                    <i class="fas fa-fw fa-check"></i>
                                                </a>
                                                <a href="{{route('admin.branch.show', 1)}}" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Show Branch">
                                                    <i class="fas fa-fw fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script>

    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush