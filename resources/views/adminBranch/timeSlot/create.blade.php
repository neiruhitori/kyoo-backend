@extends('layouts.app')

@section('content')
<div class="card" style="max-width: 550px; margin: 0 auto">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">
      Form Slot Waktu
    </h6>
  </div>

  <div class="card-body">
    <form action="{{ route('admin-branch.branch-configuration.timeslots.store') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="">Slot Waktu</label>

        <div class="row">
          <div class="col">
            <input type="text" name="start_time" class="form-control datetimepicker-input" data-toggle="datetimepicker">
          </div>

          <div class="col">
            <input type="text" name="end_time" class="form-control datetimepicker-input" data-toggle="datetimepicker">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="service_id">Layanan</label>
        
        <select name="service_id" id="service_id" class="form-control">
        </select>
      </div>

      <div class="form-group">
        <label for="quota">Kuota</label>

        <input type="number" name="max_slots" id="quota" class="form-control">
      </div>

      <div class="text-right">
        <a href="{{ route('admin-branch.branch-configuration.timeslots.index') }}" class="btn btn-secondary mr-1">Kembali</a>
        <button type="submit" class="btn btn-warning">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection