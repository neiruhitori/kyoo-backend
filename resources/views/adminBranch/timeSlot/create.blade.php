@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto">
    @include('layouts.alert')

    <div class="card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Form Slot Waktu
            </h6>
        </div>

        <div class="card-body">
            <form action="{{ route('admin-branch.branch-configuration.timeslots.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="department_id">Departemen</label>

                    <select name="department_id" id="department_id" class="form-control">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="day">Hari</label>

                    <select name="day" id="day" class="form-control">
                        @foreach ($days as $key => $day)
                            <option value="{{ $key }}" {{ $key != strtolower(date('l')) ?: 'selected' }}>
                                {{ ucwords($day) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="start_time">Waktu Mulai</label>

                            <input
                                type="text"
                                id="start_time"
                                name="start_time"
                                class="form-control datetimepicker-input"
                                data-toggle="datetimepicker"
                                autocomplete="off"
                            >
                        </div>

                        <div class="col">
                            <label for="end_time">Waktu Akhir</label>

                            <input
                                type="text"
                                id="end_time"
                                name="end_time"
                                class="form-control datetimepicker-input"
                                data-toggle="datetimepicker"
                                autocomplete="off"
                            >
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <a
                        href="{{ route('admin-branch.branch-configuration.timeslots.index') }}"
                        class="btn btn-secondary mr-1"
                    >Kembali</a>

                    <button type="submit" class="btn btn-warning">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection