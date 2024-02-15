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
                            Berikut adalah halaman untuk melihat dan mengatur petugas layanan di antrian kantor Cabang Anda.
                        </li>
                        <li style="margin-bottom: 0.25rem;">
                            Untuk versi gratis, hanya akan tersedia 1 petugas saja. Untuk penggunaan pertamakali, perlu disetting password user petugas agar dapat login sebagai petugas layanan.
                        </li>
                        <li>
                            Jika lupa password user petugas, dapat menekan tombol reset password dibawah dan akan dikirimkan link melalui email admin cabang untuk melakukan proses reset password.
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
                        {{ __('list.module', ['module' => __('Virtual Counter')]) }}
                    </h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    @if (Auth::user()->Branch->BranchType->is_premium || count(Auth::user()->Branch->CS) < 1)
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{route('admin-branch.branch-configuration.user.create')}}" class="btn btn-primary"">
                                    {{ __('create.module', ['module' => __('Virtual Counter')]) }}
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Workstation') }}</th>
                                            <th>{{ __('Username') }}</th>
                                            <th>{{ __('Role') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>
                                                    {{
                                                        $user->WorkstationVct && $user->WorkstationVct->Workstation
                                                            ? $user->WorkstationVct->Workstation->name
                                                            : '-'
                                                    }}
                                                </td>
                                                <td>{{ $user->username }}</td>
                                                <td>{{ __('Counter') }}</td>
                                                <td>
                                                    @if ($user->deleted_at)
                                                        <span class="badge badge-danger">{{ __('Non Active') }}</span>
                                                    @else
                                                        <span class="badge badge-primary">{{ __('Active') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!$user->deleted_at)
                                                        <a
                                                            href="{{ route('admin-branch.branch-configuration.user.edit-workstation', $user->id) }}"
                                                            class="btn
                                                            btn-warning" data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{
                                                                __('edit.module', ['module' => __('Workstation')])
                                                            }}"
                                                        >
                                                            <i class="fas fa-fw fa-edit"></i>
                                                        </a>
                                                        <a
                                                            href="{{ route('admin-branch.branch-configuration.user.edit', $user->id) }}"
                                                            class="btn
                                                            btn-warning" data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{
                                                                __('Change Password')
                                                            }}"
                                                        >
                                                            <i class="fas fa-fw fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin-branch.branch-configuration.user.destroy', $user->id) }}" method="post" style="display: inline">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="btn btn-danger"
                                                                data-toggle="tooltip"
                                                                data-placement="bottom"
                                                                title="{{
                                                                    __('remove.module', ['module' => __('Virtual Counter')])
                                                                }}"
                                                            >
                                                                <i class="fas fa-fw fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{route('admin-branch.branch-configuration.user.restore')}}" method="post" style="display: inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="user_id" value="{{$user->id}}">
                                                            <button
                                                                type="submit"
                                                                class="btn btn-primary"
                                                                data-toggle="tooltip"
                                                                data-placement="bottom"
                                                                title="{{ __('Restore User') }}"
                                                            >
                                                                <i class="fas fa-fw fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{route('admin-branch.branch-configuration.user.reset-password', $user->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="btn btn-info"
                                                            data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{  __('Reset Password') }}"
                                                        >
                                                            <i class="fas fa-fw fa-lock"></i>
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
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script>

    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush
