@extends('layouts.app')

@push('css')
<link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div style="margin: 0 auto; max-width: 1080px">
  <h3 class="mb-3">
    <a href="{{ route('admin.corporate.index') }}" class="mr-1" style="text-decoration: none">
      <span class="fas fa-angle-left"></span>
    </a>
    Cabang {{ $corporate->name }}
  </h3>

  <div class="card">
    <div class="card-body">
      <div class="mb-3">
        <a href="" class="btn btn-primary">
          Tambah Cabang
        </a>
      </div>

      <table class="table" id="dataTable">
        <thead>
          <tr>
            <th class="text-center">Logo</th>
            <th>Cabang</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Kota</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($branches as $branch)
            <tr>
              <td class="text-center">
                @if($branch->logo)
                  <img src="{{asset('storage/'.$branch->logo)}}" style="width: 50px">
                @endif
              </td>
              <td>{{ $branch->name }}</td>
              <td>{{ $branch->email }}</td>
              <td>{{ $branch->mobile_phone }}</td>
              <td>{{ $branch->Regency->name }}</td>
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