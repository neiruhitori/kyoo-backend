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

  @include('layouts.alert')

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
            <th class="text-center">Aksi</th>
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
              <td class="text-center">
                <form
                  action="{{ route('admin.corporate.branch.destroy', [
                  'corporateId' => $corporate->id,
                  'branchId' => $branch->id
                  ]) }}"
                  method="POST"
                >
                  @csrf
                  @method('DELETE')

                  <button type="submit" class="btn btn-danger">
                    <span class="fas fa-trash"></span>
                  </button>
                </form>
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
<script>
  $(function () {
    $('#dataTable').DataTable({
      ordering: false,
      "language": {
        "emptyTable": "Tidak ada data",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
        "infoFiltered": "(ter-filter dari _MAX_ total data)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Tampilkan _MENU_ data",
        "loadingRecords": "Memuat...",
        "processing": "Memproses...",
        "search": "Cari:",
        "zeroRecords": "Tidak ada data yang ditemukan",
        "paginate": {
          "first": "Awal",
          "last": "Akhir",
          "next": "Berikutnya",
          "previous": "Sebelum"
        },
        "aria": {
          "sortAscending": ": aktifkan untuk mengurutkan kolom menaik",
          "sortDescending": ": aktifkan untuk mengurutkan kolom menurun"
        }
      }
    });
  })
</script>
@endpush