<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ __('list.module', ['module' => __('Service')]) }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <form action="" method="get">
                    <div class="d-flex">
                        <select class="form-control" name="filter">
                            <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All</option>
                            <option value="active" {{ $filter == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $filter == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <button type="submit" class="mx-2 btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
            <div class="col-md-9 text-right">
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
                                    <th>{{ __('Total Slot Time') }}</th>
                                @endif
                                <th>{{ __('Status') }}</th>
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
                                        @if ($service->is_disable)
                                            Inactive
                                        @else
                                            Active
                                        @endif
                                    </td>
                                    <td>
                                        @if ($service->is_disable)
                                        <a
                                            href="{{route('admin-branch.branch-configuration.service.enableDisable', $service->id)}}"
                                            class="btn btn-secondary"
                                            data-toggle="tooltip"
                                            data-placement="bottom"
                                            title="{{
                                                __('Enable Service')
                                            }}"
                                                >
                                                    <i class="fa fa-unlock"></i>
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
                                    @else
                                        @if (
                                            Auth::user()->Branch->BranchType->is_appointment ||
                                            Auth::user()->Branch->BranchType->is_exhibition ||
                                            (Auth::user()->Branch->BranchType->is_direct_queue && Auth::user()->Branch->BranchConfiguration->layer === 2)
                                        )
                                            <a
                                                href="{{route('admin-branch.branch-configuration.service.slot.index', $service->id)}}"
                                                class="btn btn-success"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="{{ __('Slot Time Interval') }}"
                                            >
                                                <i class="fas fa-clock"></i>
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

                                        @if (Auth::user()->Branch->FeatureSubscription->contains('feature_id', 9))    
                                            <a
                                                href="{{route('admin-branch.branch-configuration.service.assign', $service->id)}}"
                                                class="btn btn-secondary"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="{{
                                                        __('edit.module', ['module' => __('Sub Service')])
                                                        }}"
                                                >
                                                <i class="fas fa-fw fa-server"></i>
                                            </a>
                                        @endif

                                            <a
                                                href="{{route('admin-branch.branch-configuration.service.enableDisable', $service->id)}}"
                                                class="btn btn-danger"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="{{
                                                    __('Disable Service')
                                                }}"
                                            >
                                                <i class="fa fa-lock"></i>
                                            </a>
                                     @endif
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
