<div class="card shadow mb-4">
    {{-- <div class="card-header py-3">
    </div> --}}
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="m-0 font-weight-bold" style="color: #103C7C">
                    {{ __('list.module', ['module' => __('Sub Service')]) }}
                </h5>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{route('admin-branch.branch-configuration.sub-service.create')}}" class="btn btn-primary" style="background-color: #103C7C">
                    {{ __('create.module', ['module' => __('Sub Service')]) }}
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead style="background-color:#33A0FF4D; color: #103C7C;">
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sub_services as $sub_service)
                                <tr>
                                    <td>{{$sub_service->name}}</td>
                                    <td>

                                        <a
                                            href="{{ route('admin-branch.branch-configuration.sub-service.edit', $sub_service->id) }}"
                                            class="btn btn-warning"
                                            data-toggle="tooltip"
                                            data-placement="bottom"
                                            title="{{
                                                __('edit.module', ['module' => __('Service')])
                                            }}"
                                        >
                                            <i class="fas fa-fw fa-edit"></i>
                                        </a>

                                        <form action="{{ route('admin-branch.branch-configuration.sub-service.destroy', $sub_service->id) }}" method="post" style="display: inline">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="{{
                                                    __('remove.module', ['module' => __('Service')])
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
