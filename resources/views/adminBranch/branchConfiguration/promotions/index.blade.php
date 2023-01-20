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
        @forelse ($promotions as $promotion)
          @if ($promotion->type == $promotion_types['text'])
            <div class="col-md-3">
              <div class="promotion-card text-promotion-card" style="background-color: {{ $promotion->color }};">
                <p class="text-promotion-content" style="font-size: {{ $promotion->font_size }}">
                  {!! $promotion->text !!}
                </p>
              </div>
            </div>
          @elseif ($promotion->type == $promotion_types['image'])
            <div class="col-md-3">
              <div class="promotion-card image-promotion-card">
                <img src="{{ asset('storage/' . $promotion->image_url) }}" alt="{{ $promotion->title }}">
              </div>
            </div>
          @endif
        @empty
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <div class="circle mb-3">
                  <span class="fas fa-box-open" style="color: #AAA"></span>
                </div>
      
                <div style="max-width: 380px; margin: 0 auto;">
                  <h4 class="text-center">Tidak ada konten</h4>
                  <p class="text-center">Tambahkan promosi dengan memilih tombol diatas</p>
                </div>
              </div>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </div>
@endsection

@push('css')
<style>
  .promotion-card {
    height: 360px;
    border-radius: 8px;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
  }

  .text-promotion-card {
    padding: .5rem;
  }

  .text-promotion-content {
    margin: 0;
    text-align: center;
    color: #FFF;
    overflow-wrap: anywhere;
  }

  .image-promotion-card {
    background-color: #0D1117;
  }

  .image-promotion-card img {
    width: auto;
    height: auto;
    max-height: 360px;
    max-width: 182px;
  }

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