@extends('layouts.app')

@push('css')
<link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
  .img-size-constraint {
    width: auto;
    height: auto;
    max-height: 40px;
    max-width: 40px;
  }
</style>
@endpush

@section('content')
<div style="margin: 0 auto; max-width: 1080px">
  <div class="row mb-3">
    <h3 class="col">
      Cabang {{ $corporate->name }}
    </h3>

    <div class="col text-right">
      <a href="{{ route('admin.corporate.branch.create', $corporate->id) }}" class="btn btn-primary">
        <span class="fas fa-plus mr-1"></span>
        Tambah Cabang
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
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
                  <img src="{{asset('storage/'.$branch->logo)}}" class="img-size-constraint">
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