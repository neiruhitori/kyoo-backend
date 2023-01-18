@extends('layouts.app')

@section('content')
  <div style="max-width: 800px; margin: 0 auto;">
    <h3 class="mb-3">Konten Promosi</h3>

    <div class="mb-4">
      <h6>Tambah Promosi</h6>

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
      <h6>Daftar Promosi</h6>

      <div class="row">
        <div class="col-md-3" >
          <div class="rounded" style="height: 360px; background-color: #DDD"></div>
        </div>

        <div class="col-md-3">
          <div class="rounded" style="height: 360px; background-color: #DDD"></div>
        </div>

        <div class="col-md-3">
          <div class="rounded" style="height: 360px; background-color: #DDD"></div>
        </div>

        <div class="col-md-3">
          <div class="rounded" style="height: 360px; background-color: #DDD"></div>
        </div>
      </div>
    </div>
  </div>
@endsection