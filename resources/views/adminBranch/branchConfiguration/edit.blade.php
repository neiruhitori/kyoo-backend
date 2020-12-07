@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Branch Configuration</h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('adminBranch.branchConfiguration.update')}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="maximum_recall">Max Recall</label>
                                    <input name="maximum_recall" type="number" min="0" class="form-control @error('maximum_recall') is-invalid @enderror" value="{{old('maximum_recall') ?: Auth::user()->Branch->BranchConfiguration->maximum_recall}}" required>
                                    @include('layouts.inputError', ['errorName' => 'maximum_recall'])
                                </div>

                                <div class="form-group">
                                    <label for="maximum_requeue_count">Max Re-queue</label>
                                    <input name="maximum_requeue_count" type="number" min="0" class="form-control @error('maximum_requeue_count') is-invalid @enderror" value="{{old('maximum_requeue_count') ?: Auth::user()->Branch->BranchConfiguration->maximum_requeue_count}}" required>
                                    @include('layouts.inputError', ['errorName' => 'maximum_requeue_count'])
                                </div>

                                <div class="form-group">
                                    <label for="allow_transfer">Allow Transfer</label>
                                    <select name="allow_transfer" id="allow_transfer" class="form-control @error('allow_transfer') is-invalid @enderror">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'allow_transfer'])
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
@push('js')
    <script>
        $(document).ready(function() {
            const allow_transferOldValue = '{{ old('allow_transfer') ?: Auth::user()->Branch->BranchConfiguration->allow_transfer }}';
                
            if(allow_transferOldValue !== '') {
                $('#allow_transfer').val(allow_transferOldValue);
            }
        });
    </script>
@endpush