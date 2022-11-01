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
          <tr>
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
              <td class="text-center">
                @if($corporate->logo)
                  <img src="{{asset('storage/'.$corporate->logo)}}" style="width: 50px">
                @endif
              </td>
              <td>{{ $corporate->name }}</td>
              <td>{{ $corporate->email }}</td>
              <td>{{ $corporate->mobile_phone }}</td>
              <td>{{ $corporate->Regency->name }}</td>
              <td>
                <div class="text-center">
                  <a
                    href="#"
                    class="btn btn-primary mr-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Daftar Cabang"
                  >
                    <span class="fas fa-list"></span>
                  </button>

                  <a
                    href="{{ route('admin.corporate.edit', $corporate->id) }}"
                    class="btn btn-warning"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Edit Corporate"
                  >
                    <span class="fas fa-edit"></span>
                  </a>
                </div>
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