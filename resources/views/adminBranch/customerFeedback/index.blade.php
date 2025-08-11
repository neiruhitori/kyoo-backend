@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .accordion-toggle-custom {
                transition: padding 0.3s ease;
            }
    .accordion-toggle-custom::after {
                    font-family: "Font Awesome 5 Free";
                    font-weight: 900;
                    transition: transform 0.2s ease;
                    margin-left: auto;
                }
    .accordion-toggle-custom[aria-expanded="false"]::after {
                        content: "\f107";
                    }

    .accordion-toggle-custom[aria-expanded="true"]::after {
                        content: "\f106";
                    }
    table {
            border: 1px solid #33A0FF4D; 
        }

    table th,
    table td {
            border: 1px solid #33A0FF4D !important;
            }
    table td {
                color: black
            }
</style>
@endpush

@section('content')

@include('layouts.alert')
@csrf
<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                                <form action="" method="post">
                                <div class="form-group">
                                    <label for="survey_type">{{ __('Survey Type') }}</label>
                                    @php
                                        $selectedType = $config->type ?? 'default';
                                    @endphp
                                    <select name="survey_type" id="survey_type" class="form-control">
                                        <option value="default" {{ $selectedType === 'default' ? 'selected' : '' }}> Default </option>
                                        <option value="nps" {{ $selectedType === 'nps' ? 'selected' : '' }}> NPS </option>
                                        <option value="csat" {{ $selectedType === 'csat' ? 'selected' : '' }}> CSAT </option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 d-flex align-items-end" style="gap: 1rem">
                            <div class="form-group d-flex align-items-center">
                                <button type="submit" class="btn btn-warning">Save Changes</button>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                @if (
                                        isset($config) &&
                                        (
                                            ($config->type == 'nps' && $config->questions->count() < 1) ||
                                            ($config->type == 'csat' && $config->questions->count() < 3)
                                        )
                                    )
                                        <a href="{{ route('admin-branch.feedback.create') }}" class="btn btn-primary">Add Questions</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div id="questions-container">
                @if($config && $config->questions->count())
                    @foreach($config->questions as $index => $question)
                        <div class="card shadow mb-4 question-item">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-11">
                                        <h4 class="font-weight-bolder">Question {{ $index + 1 }}</h4>
                                        <textarea class="form-control" rows="3" readonly>{{ $question->question_text}}</textarea>
                                    </div>
                                    <div class="col-md-1 d-flex justify-content-end align-items-center flex-column" style="gap: 0.5rem">
                                        <a href="{{ route('admin-branch.feedback.edit', $question->id) }}" class="btn btn-warning">
                                            <i class="fas fa-fw fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin-branch.feedback.delete', $question->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                            <button class="btn btn-danger" type="submit">
                                                <i class="fas fa-fw fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 d-flex align-items-center justify-content-center">
                                    <p class="mb-0">No data</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

<style>
    textarea {
        resize: none;
    }
</style>
@endsection
