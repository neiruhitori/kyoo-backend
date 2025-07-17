@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.3/css/buttons.dataTables.min.css">
    <style>
        .buttons-excel {
            background-color: #48bb78 !important;
            color: white !important;
            border: 0px !important;
            font-weight: 500px !important;
        }
        .buttons-pdf {
            background-color: #e53e3e !important;
            color: white !important;
            border: 0px !important;
            font-weight: 500px !important;
        }
        .buttons-print {
            background-color: #cbd5e0 !important;
            color: #333333 !important;
            border: 0px !important;
            font-weight: 500px !important;
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
<div class="row">
    <div class="col-xl-12 col-lg-7">
        <div class="card shadow mb-4">
            {{-- <div class="card-header py-3"> --}}
                {{-- </div> --}}
                
                <div class="card-body">
                <h5 class="mb-4 font-weight-bold" style="color: #103c7c">{{ __('Customer Satisfaction Report') }}</h5>
                <form action="" method="GET" class="mb-4" style="max-width: 500px;">
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6">
                            <label>{{ __('Select Month') }}</label>
                            <select name="month" class="form-control">
                                <option disabled selected>--- {{ __('Select Month') }} ---</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month != $i ?: 'selected' }}>
                                        {{ date('F', strtotime('1999-' . $i . '-01')) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group col-xs-12 col-md-6">
                            <label>{{ __('Select Year') }}</label>
                            <select name="year" class="form-control">
                                <option disabled selected>--- {{ __('Select Year') }} ---</option>
                                @for ($i = 2000; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ $year != $i ?: 'selected' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" style="background-color: #103c7c">Filter</button>
                    </div>
                </form>

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead style="background-color:#33A0FF4D; color: #103C7C;">
                        <tr>
                            <th class="text-center">{{ __('Date') }}</th>
                            <th class="text-right">{{ __('Total Queue') }}</th>
                            <th class="text-right">{{ __('Total Feedback') }}</th>
                            <th class="text-right">{{ __('Feedback Percentage') }}</th>
                            <th>{{ __('Average Review Score') }}</th>   
                        </tr>
                    </thead>

                    <tbody>
                        @if (count($reports) > 0) 
                            @foreach ($reports as $report)
                                <tr>
                                    <td class="text-center">{{ $report->date }}</td>
                                    <td class="text-right">{{ $report->total_queue }}</td>
                                    <td class="text-right">{{ $report->total_feedback }}</td>
                                    <td class="text-right">{{ $report->feedback_percentage }}%</td>
                                    <td>
                                        <span class="fas fa-star mr-1 text-warning"></span>
                                        {{ $report->average_rate }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">{{ __('Data not Found') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.print.min.js"></script>

    <script>
        $('#dataTable').dataTable({
            "ordering": false,
            "dom": 'Bfrtip',
            "language": {
                "emptyTable": "{{ __('No Data') }}",
                "info": "{{ __('Displaying :start to :end of :total data') }}"
                            .replace(':start', '_START_')
                            .replace(':end', '_END_')
                            .replace(':total', '_TOTAL_'),
                "infoEmpty": "{{ __('No Data') }}",
                "infoFiltered": "{{ __('(filtered from :max total data)') }}".replace(':max','_MAX_'),
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "{{ __('Show :menu data') }}".replace(':menu', '_MENU_'),
                "loadingRecords": "{{ __('Loading...') }}",
                "processing": "{{ __('Processing...') }}",
                "search": "{{ __('Search:') }}",
                "zeroRecords": "{{ __('No data found') }}",
                "paginate": {
                    "first": "{{ __('First') }}",
                        "last": "{{ __('Last') }}",
                        "next": "{{ __('Next') }}",
                        "previous": "{{ __('Previous') }}"
                },
                "aria": {
                    "sortAscending": "{{ __(': enable to sort the column in ascending order') }}",
                    "sortDescending": "{{ __(': enable to sort the column in descending order') }}"
                }
            },  
            "buttons": [
                {
                    extend: 'excelHtml5',
                    title: "{{ __('Customer Satisfaction Report') }} {{ date('F', $month) }} {{ $year }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "{{ __('Customer Satisfaction Report') }} {{ date('F', $month) }} {{ $year }}"
                },
                {
                    extend: 'print',
                    text: "{{ __('Print') }}",
                    title: "{{ __('Customer Satisfaction Report') }} {{ date('F', $month) }} {{ $year }}"
                }
            ]
        })
    </script>
@endpush