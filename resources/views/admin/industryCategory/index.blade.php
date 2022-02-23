@extends('layouts.app')

@push('css')
<link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('list.module', ['module' => __('Industry Category')]) }}
                </h6>
            </div>
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{route('admin.industryCategory.create')}}" class="btn btn-primary"">
                            {{ __('create.module', ['module' => __('Category')]) }}
                        </a>
                        </div>
                    </div>
                    <div class=" row">
                            <div class="col-md-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th>{{ __('Icon') }}</th>
                                                <th>{{ __('Show in Mobile') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $category)
                                            <tr>
                                                <td>{{$category->id}}</td>
                                                <td>{{$category->name}}</td>
                                                <td>
                                                    <img src="{{asset('storage/'.$category->icon)}}" alt="">
                                                </td>
                                                <td>
                                                    @if ($category->is_active)
                                                    <span class="badge badge-primary">{{ __('Yes') }}</span>
                                                    @else
                                                    <span class="badge badge-danger">{{ __('No') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('admin.industryCategory.edit', $category->id)}}"
                                                        class="btn btn-warning" data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="{{ __('edit.module', ['module' => __('Category')]) }}">
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                                    <form
                                                        action="{{route('admin.industryCategory.destroy', $category->id)}}"
                                                        method="post" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"
                                                            data-toggle="tooltip" data-placement="bottom"
                                                            title="{{ __('remove.module', ['module' => __('Category')]) }}">
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
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script>

    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
    @endpush