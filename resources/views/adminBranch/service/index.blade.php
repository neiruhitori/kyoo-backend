<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ __('list.module', ['module' => __('Service')]) }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="{{route('admin-branch.branch-configuration.service.create')}}" class="btn btn-primary"">
                    {{ __('create.module', ['module' => __('Service')]) }}
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Department') }}</th>
                                @if (
                                    Auth::user()->Branch->BranchType->is_appointment ||
                                    Auth::user()->Branch->BranchType->is_exhibition
                                )
                                    <th>{{ __('Total Slot Time Interval') }}</th>
                                @endif
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $service)
                                <tr>
                                    <td>{{$service->name}}</td>
                                    <td>
                                        @if ($service->Department)
                                            {{$service->Department->name}}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if (
                                        Auth::user()->Branch->BranchType->is_appointment ||
                                        Auth::user()->Branch->BranchType->is_exhibition
                                    )
                                        <td>
                                            {{count($service->Slot)}}
                                        </td>
                                    @endif
                                    <td>
                                        @if (
                                            Auth::user()->Branch->BranchType->is_appointment ||
                                            Auth::user()->Branch->BranchType->is_exhibition
                                        )
                                            <a
                                                href="{{route('admin-branch.branch-configuration.service.slot.index', $service->id)}}"
                                                class="btn btn-success"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="{{ __('Slot Time Interval') }}"
                                            >
                                                <i class="fas fa-fw fa-th-list"></i>
                                            </a>
                                        @endif

                                        <a
                                            href="{{route('admin-branch.branch-configuration.service.edit', $service->id)}}"
                                            class="btn btn-warning"
                                            data-toggle="tooltip"
                                            data-placement="bottom"
                                            title="{{
                                                __('edit.module', ['module' => __('Service')])
                                            }}"
                                        >
                                            <i class="fas fa-fw fa-edit"></i>
                                        </a>

                                        <form action="{{route('admin-branch.branch-configuration.service.destroy', $service->id)}}" method="post" style="display: inline">
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