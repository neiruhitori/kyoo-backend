@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Template Hari Libur</h6>
                </div>

                <div class="card-body">
                    <div id="accordion">
                        @foreach ($scheduleTemplates as $index => $scheduleTemplate)
                            <div class="card">
                                <div class="card-header" id="heading{{$index}}">
                                    <h2 class="mb-0">
                                        <button
                                            class="btn btn-link"
                                            type="button"
                                            data-toggle="collapse"
                                            data-target="#collapse{{$index}}"
                                            aria-expanded="true"
                                            aria-controls="collapse{{$index}}"
                                        >
                                            {{$scheduleTemplate->name}}
                                        </button>
                                    </h2>
                                </div>

                                <div
                                    id="collapse{{$index}}"
                                    class="collapse"
                                    aria-labelledby="heading{{$index}}"
                                    data-parent="#accordion"
                                >
                                    <div class="card-body">
                                        <form
                                            action="{{route('admin-branch.branch-configuration.holiday.template.store')}}"
                                            method="POST"
                                        >
                                            @csrf

                                            <input type="hidden" name="schedule_template_id" value="{{ $scheduleTemplate->id }}">
                                            <button class="btn btn-primary mb-3">Pilih {{$scheduleTemplate->name}}</button>
                                        </form>

                                        <table class="table" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th class="text-right">ID</th>
                                                    <th class="text-center">Tanggal</th>
                                                    <th>Deskripsi</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($scheduleTemplate->ScheduleTemplateDetail as $scheduleTemplateDetail)
                                                    <tr>
                                                        <td class="text-right">{{ $scheduleTemplateDetail->id }}</td>
                                                        <td class="text-center">{{ $scheduleTemplateDetail->date }}</td>
                                                        <td>{{ $scheduleTemplateDetail->description }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a
                        href="{{route('admin-branch.branch-configuration.schedule.index')}}"
                        class="btn btn-secondary mt-3"
                    >
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush