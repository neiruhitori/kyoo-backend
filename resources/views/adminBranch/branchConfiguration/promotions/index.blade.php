@extends('layouts.app')

@section('content')
  <div style="max-width: 800px; margin: 0 auto;">
    <h3 class="mb-3">Konten Promosi</h3>

    <div class="mb-5">
      <h6 class="font-weight-bold">Tambah Promosi</h6>

      <div>
        <a href="{{ route('admin-branch.branch-configuration.promotions.image.create') }}" class="btn btn-secondary mr-1">
          <span class="fas fa-image mr-1"></span>
          Gambar
        </a>

        <a href="{{ route('admin-branch.branch-configuration.promotions.text.create') }}" class="btn btn-secondary">
          <span class="fas fa-font mr-1"></span>
          Teks
        </a>
      </div>
    </div>

    <div>
      <h6 class="font-weight-bold">Daftar Promosi</h6>

      <div class="row">
        {{-- <div class="col-md-3" >
          <div class="rounded" style="height: 360px; background-color: #DDD"></div>
        </div> --}}

        <div class="col-md-12 mt-4">
          <div class="circle mb-3">
            <span class="fas fa-box-open" style="color: #AAA"></span>
          </div>

          <div style="max-width: 360px; margin: 0 auto;">
            <h4 class="text-center">Tidak ada konten</h4>
            <p class="text-center">Pilih tombol diatas untuk menambahkan promosi</p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('css')
<style>
  .circle {
    border-radius: 100%;
    background-color: #DDD;
    max-width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content:center;
    font-size: 2.25rem;
    margin: 0 auto;
  }
</style>
@endpush