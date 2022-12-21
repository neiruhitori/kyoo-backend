@extends('layouts.app')

@section('content')
    @include('layouts.alert')

    <div class="card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Slot Waktu
            </h6>
        </div>

        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('admin-branch.branch-configuration.timeslots.create') }}" class="btn btn-primary">
                    Tambah Slot Waktu
                </a>
            </div>

            <table class="table table-bordered table-stripped mb-0">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th class="text-center">Slot Waktu</th>
                        <th class="text-right">Kuota</th>
                        <th class="text-center">Aksi</th>
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
                            <td class="text-center">{{ $timeSlots[$i]->start_time }} - {{ $timeSlots[$i]->end_time }}</td>
                            <td class="text-right">{{ $timeSlots[$i]->quota }} orang</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-warning mr-1" data-toggle="tooltip" data-placement="bottom"
                                    title="Daftar Layanan">
                                    <span class="fas fa-list"></span>
                                </a>

                                <form
                                    action="{{ route('admin-branch.branch-configuration.timeslots.destroy', $timeSlots[$i]->id) }}"
                                    method="POST" class="d-inline-block"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger" data-toggle="tooltip"
                                        data-placement="bottom" title="Hapus Slot Waktu">
                                        <span class="fas fa-trash"></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endfor

                    @if (sizeof($timeSlots) < 1)
                        <tr>
                            <td colspan="3" class="text-center">Data tidak ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
