@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Schedule Template Detail</h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin.scheduleTemplateDetail.update', $schedule->id)}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="date">Template</label>
                                    <input name="" type="text" class="form-control @error('date') is-invalid @enderror" value="{{$schedule->ScheduleTemplate->name}}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input name="date" type="date" class="form-control @error('date') is-invalid @enderror" value="{{old('date') ?: $schedule->date}}" required>
                                    @include('layouts.inputError', ['errorName' => 'date'])
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input name="description" type="text" class="form-control @error('description') is-invalid @enderror" value="{{old('description') ?: $schedule->description}}" required>
                                    @include('layouts.inputError', ['errorName' => 'description'])
                                </div>
                                <button class="btn btn-warning">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection