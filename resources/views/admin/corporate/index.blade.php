@extends('layouts.app')

@push('css')
<link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div style="margin: 0 auto; max-width: 1080px">
  <h3 class="mb-3 ">Daftar Corporate</h3>

  <div class="card">
    <div class="card-body">
      <table class="table" id="dataTable">
        <thead>
          <tr class="table-secondary">
            <th class="text-center">Logo</th>
            <th>Corporate</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Kota</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($corporates as $corporate)
            <tr>
              <td>
                @isset($corporate->logo)
                  <img src="{{asset('storage/'.$branch->logo)}}" style="width: 100px">
                @endisset
              </td>
              <td>{{ $corporate->name }}</td>
              <td>{{ $corporate->email }}</td>
              <td>{{ $corporate->mobile_phone }}</td>
              <td>{{ $corporate->Regency->name }}</td>
              <td>
                <a
                  href="#"
                  class="btn btn-secondary"
                  data-toggle="tooltip"
                  data-placement="bottom"
                  title="Daftar Cabang"
                >
                  <span class="fas fa-list"></span>
                </button>

                <a
                  href="#"
                  class="btn btn-secondary"
                  data-toggle="tooltip"
                  data-placement="bottom"
                  title="Edit Corporate"
                >
                  <span class="fas fa-edit"></span>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('js')
<script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script>
@endpush