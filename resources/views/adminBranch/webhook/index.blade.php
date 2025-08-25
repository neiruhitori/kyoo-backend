@extends('layouts.app')
@push('css')
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.3/css/buttons.dataTables.min.css">
    
@endpush
@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-7">
            @include('layouts.alert')

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Webhook Url') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row"> 
                        <div class="col-12 mb-3 alert alert-primary">
                            <h6 class="font-weight-bold" style="color: black">Webhook Url</h6>
                            <h6 class="noselect blurred" id="linkWebhook" onclick="toggleBlur('linkWebhook')">
                                {{ $webhook ?? 'No Endpoint found' }}
                            </h6>
                            <small>Click to see endpoint</small>
                        </div>
                        {{-- <div class="col-12 alert alert-dark">
                            <h6 class="font-weight-bold" style="color: black">Sandbox Url</h6>
                            <h6 class="noselect blurred" id="linkSandbox" onclick="toggleBlur('linkSandbox')">
                                {{ $sandbox ?? 'No Endpoint found' }}
                            </h6>
                            <small class="mb-3 d-block">Click to see endpoint</small>
                            <div class="d-flex align-items-center" style="gap: 0.5rem">
                                <button type="button" class="btn btn-primary" id="btnDummy">Test with dummies</button>
                                <h6 class="font-weight-bold mb-0" id="result"></h6>
                            </div>
                            <button class="btn btn-link p-0 mt-2" data-toggle="modal" data-target="#exampleModal">
                                <small>
                                    Click to see Dummy Payload
                                </small>
                            </button>
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Webhook List') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table" width="100%" cellspacing="0">
                                    <thead>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Event Type') }}</th>
                                        <th>{{ __('Status Code') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($logs as $log)
                                            <tr>
                                                <td>{{ $log->created_at_formatted }}</td>
                                                <td>{{ $log->queue->name }}</td>
                                                <td>
                                                    @switch($log->event_type)
                                                        @case('onsite_checkin_booking')
                                                            <h5 class="font-weight-bold text-success">Check In</h5>
                                                            @break
                                                        @case('onsite_create_booking')
                                                            <h5 class="font-weight-bold text-primary">Create Booking</h5>    
                                                            @break
                                                        @case('onsite_modify_booking')
                                                            <h5 class="font-weight-bold">Modify Booking</h5>    
                                                            @break
                                                        @default
                                                    @endswitch
                                                </td>
                                                <td>{{ $log->status_code }}</td>
                                                <td>
                                                <button type="button" class="btn btn-secondary btn-detail"
                                                    data-target="#exampleModal"
                                                    data-payload='@json($log->payload)'
                                                    data-toggle="modal">
                                                    Detail
                                                </button>
                                                    @if ($log->status_code != 200)
                                                    <form action="{{ route('admin-branch.branch-configuration.webhook.resend', ['id' => $log->id, 'live']) }}" method="post" style="display:inline-block">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">Resend</button>
                                                    </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">{{ __('Data not Found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payload Detail</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <pre style="white-space: pre-wrap; word-wrap: break-word;"></pre>
            </div>
            </div>
        </div>
        </div>

@endsection
<style>
.noselect{
    cursor: pointer;
    user-select: none;
    transition: filter 0.3s ease;
}
.blurred {
    filter: blur(5px);
}
</style>
<script>
function toggleBlur(id) {
    document.getElementById(id).classList.toggle("blurred");
}
document.addEventListener('click', function(e) {
    if (e.target.matches('.btn-detail')) {
        let rawPayload = e.target.dataset.payload;
        let content = '';
        try {
            let parsed = JSON.parse(rawPayload);
            content = JSON.stringify(parsed, null, 2);
        } catch (err) {
            content = rawPayload;
        }

        document.querySelector('#exampleModal pre').innerText = content;
    }
});
// document.addEventListener('DOMContentLoaded', function(){
//     feedTable()
// });

// async function feedTable() {
//         const res = await axios.post('/admin-branch/branch-configuration/webhook/logs-webhook')
//             .then(res => res.data.data)
//             .catch(err => {
//                 console.error(err)
                
//                 return []
//             })
        
//         if (!res.length) {
//             $("#table tbody").html(`<tr>
//                 <td colspan="5" class="text-center">{{ __('Data not Found') }}</td>
//             </tr>`)
//             return
//         }
        
//         $("#table tbody").html(
//             transformResponse(res)
//         )
//     }

// function transformResponse(data) {
//     var liveUrl = "{{ route('admin-branch.branch-configuration.webhook.resend', [':id', 'live']) }}";
//     var sandboxUrl = "{{ route('admin-branch.branch-configuration.webhook.resend', [':id', 'sandbox']) }}";

//     return data.map(v => {
//         let liveAction = liveUrl.replace(':id', v.id);
//         let sandboxAction = sandboxUrl.replace(':id', v.id);
//         let actionButtons = '';

//         if (v.status_code != 200) {
//             actionButtons = `
//                 <form action="${liveAction}" method="post" style="display:inline-block">
//                     <input type="hidden" name="_token" value="{{ csrf_token() }}">
//                     <button type="submit" class="btn btn-success">Resend</button>
//                 </form>`;
//         }

//         return `<tr style="color: #000">
//             <td>${v.created_at_formatted}</td>
//             <td >${v.queue?.name ?? '-'}</td>
//             <td>${v.event_type}</td>
//             <td>${v.status_code}</td>
//             <td class="d-flex" style="gap:0.5rem;">
//                 ${actionButtons}
//                 <div>
//                     <button type="submit" class="btn btn-secondary btn-detail" 
//                     data-toggle="modal" 
//                     data-target="#exampleModal"
//                     data-payload='${v.payload}'>
//                         Detail
//                     </button>
//                 </div>
//             </td>
//         </tr>`;
//     }).join('');
// }

// document.addEventListener('click', function(e) {
//     if (e.target.matches('.btn-detail')) {
//         let rawPayload = e.target.dataset.payload;
//         let content = '';
//         try {
//             if (typeof rawPayload === 'object') {
//                 content = JSON.stringify(rawPayload, null, 2);
//             } else {
//                 let parsed = JSON.parse(rawPayload);
//                 content = JSON.stringify(parsed, null, 2);
//             }
//         } catch (err) {
//             content = rawPayload;
//         }

//         document.querySelector('#exampleModal pre').innerText = content;
//     }
// });



</script>