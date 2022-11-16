@extends('layouts.app')

@push('css')
<style>
  .card-option:hover {
    border-color: #189DCD;
  }
</style>
@endpush

@section('content')
<div style="margin: 0 auto; max-width: 560px;">
  <div class="mb-4">
    <a href="{{ route('admin.corporate.branch.index', $corporate->id) }}" class="d-inline-block mb-1" style="text-decoration: none;">
      <span class="fas fa-angle-left mr-1"></span>
      Kembali
    </a>
    <h3 class="mb-0">Tambah Cabang Corporate</h3>
    <p class="mb-0 mt-1">Pilih bagaimana Anda menambahkan cabang <strong>{{ $corporate->name }}</strong></p>
  </div>

  <div class="d-flex">
    <a
      href="{{ route('admin.branch.create', ['corporate_id' => $corporate->id]) }}"
      class="d-block text-secondary"
      style="text-decoration: none; flex: 1 1 0%"
    >
      <div class="card card-option" style="height: 160px">
        <div class="card-body d-flex flex-column justify-content-center">
          <div class="h1 text-center mb-3">
            <span class="fas fa-plus"></span>
          </div>
  
          <p class="text-center mb-0">
            Buat cabang baru
          </p>
        </div>
      </div>
    </a>
  
    <a
      href="{{ route('admin.corporate.branch.create', $corporate->id) }}"
      class="d-block text-secondary ml-3"
      style="text-decoration: none; flex: 1 1 0%"
    >
      <div class="card card-option" style="height: 160px">
        <div class="card-body d-flex flex-column justify-content-center">
          <div class="h1 text-center mb-3">
            <span class="fas fa-exchange-alt"></span>
          </div>
  
          <p class="text-center mb-0">
            Pilih dari branch yang sudah terdaftar
          </p>
        </div>
      </div>
    </a>
  </div>
</div>
@endsection