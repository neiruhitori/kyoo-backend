@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Edit Question
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="question">{{ __('Question') }}</label>
                                    <textarea name="question" id="" rows="3" class="form-control" style="resize: none" required>{{ $question->question_text ?? '' }}</textarea>
                                    @include('layouts.inputError', ['errorName' => 'question'])
                                </div>
                                <button class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
