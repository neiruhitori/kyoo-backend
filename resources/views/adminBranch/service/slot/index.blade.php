@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="card mb-4 custom-info" data-open="open" role="alert">
        <div class="card-body">
            <div class="custom-info-head">
                <h6 class="font-weight-bold my-0">
                    <span class="fas fa-info-circle text-primary mr-1"></span>
                    Informasi
                </h6>

                <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                    Tampilkan
                </button>
            </div>

            <div class="custom-info-body">
                <p>
                    <ul style="padding-left: 2rem;">
                        <li style="margin-bottom: 0.25rem;">
                            Layanan di mode appointment mempunyai fleksibilitas untuk mengatur Slot Hari Layanan, Slot Waktu Jam Layanan dan maksimal jumlah antrian yang disediakan dalam slot tersebut.
                        </li>
                        <li>
                            Untuk melihat panduan penggunaan slot waktu layanan, silahkan kunjungi
                            <a href="{{ route('adminBranch.slotTimeGuide') }}" class="font-weight-bold">
                                <u>disini</u>
                            </a>
                        </li>
                    </ul>
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('list.module', ['module' => __('Slot')]) }}: {{ $service->name }}
                    </h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{route('adminBranch.service.slot.create', $service->id)}}" class="btn btn-primary"">
                                {{ __('create.module', ['module' => __('Slot')]) }}
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Day') }}</th>
                                            <th>{{ __('Maximum Slot') }}</th>
                                            <th>{{ __('Start Time') }}</th>
                                            <th>{{ __('End Time') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($service->Slot as $slot)
                                            <tr>
                                                <td>{{ __(ucfirst($slot->day)) }}</td>
                                                <td>{{$slot->max_slots}}</td>
                                                <td>{{$slot->start_time}}</td>
                                                <td>{{$slot->end_time}}</td>
                                                <td>
                                                    <a
                                                        href="{{route('adminBranch.slot.edit', $slot->id)}}"
                                                        class="btn btn-warning"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="{{
                                                            __('edit.module', ['module' => __('Slot')])
                                                        }}"
                                                    >
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                                    <form action="{{route('adminBranch.slot.destroy', $slot->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="btn btn-danger"
                                                            data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{
                                                                __('remove.module', ['module' => __('Slot')])
                                                            }}"
                                                        >
                                                            <i class="fas fa-fw fa-trash"></i>
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