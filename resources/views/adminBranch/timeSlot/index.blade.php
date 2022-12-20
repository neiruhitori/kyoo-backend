@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">
      Daftar Slot Waktu
    </h6>
  </div>

  <div class="card-body">
    <div class="mb-3">
      <a href="{{route('admin-branch.branch-configuration.timeslots.create')}}" class="btn btn-primary">
        Tambah Slot Waktu
      </a>
    </div>

    <table class="table table-bordered table-stripped mb-0">
      <thead>
        <tr>
          <th>Hari</th>
          <th class="text-center">Slot Waktu</th>
          <th>Layanan</th>
          <th class="text-right">Kuota</th>
        </tr>
      </thead>

      <tbody>
        @for ($i = 0; $i < sizeof($timeSlots); $i++)
          <tr>
            <td>
              @if ($i == 0 || $timeSlots[$i - 1]->day != $timeSlots[$i]->day)
                {{ $timeSlots[$i]->day }}
              @endif
            </td>
            <td class="text-center">{{ $timeSlots[$i]->time_slot }}</td>
            <td>{{ $timeSlots[$i]->service->name }}</td>
            <td class="text-right">{{ $timeSlots[$i]->max_slots }} orang</td>
          </tr>
        @endfor

        @if (sizeof($timeSlots) < 1)
          <tr>
            <td colspan="4" class="text-center">Data tidak ditemukan</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>
@endsection