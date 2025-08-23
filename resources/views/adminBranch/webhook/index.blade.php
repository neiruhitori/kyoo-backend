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
                        <div class="col-12 alert alert-dark">
                            <h6 class="font-weight-bold" style="color: black">Sandbox Url</h6>
                            <h6 class="noselect blurred" id="linkSandbox" onclick="toggleBlur('linkSandbox')">
                                {{ $sandbox ?? 'No Endpoint found' }}
                            </h6>
                            <small class="mb-3 d-block">Click to see endpoint</small>
                            <div class="d-flex align-items-center" style="gap: 0.5rem">
                                {{-- <form action="{{ route('admin-branch.branch-configuration.webhook.dummy') }}" method="post" class="mb-0">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Test with dummies</button>
                                </form> --}}
                                <button type="button" class="btn btn-primary" id="btnDummy">Test with dummies</button>
                                <h6 class="font-weight-bold mb-0" id="result"></h6>
                            </div>
                            <button class="btn btn-link p-0 mt-2" data-toggle="modal" data-target="#exampleModal">
                                <small>
                                    Click to see Dummy Payload
                                </small>
                            </button>
                        </div>
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
                                        <tr>
                                            <td colspan="5" class="text-center">{{ __('Data not Found') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Payload</h5>
        </div>
        <div class="modal-body">
            <pre>{{ json_encode($sandbox_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
document.addEventListener('DOMContentLoaded', function(){
    feedTable()
    document.getElementById('btnDummy').addEventListener('click', sendDummy);
});

async function sendDummy(){
    const hit = await axios.post('/admin-branch/branch-configuration/webhook/dummy')
            .then(res => {
                console.log(res.data)
                document.getElementById('result').innerText = `${res.data.code} ${res.data.status}`;
            })
            .catch(err => {
                console.error(err)
                if (err.response) {
                    document.getElementById('result').innerText = `${err.response.status} ${err.response.statusText}`;
                } else {
                    document.getElementById('result').innerText = 'Network Error';
                }
            })

}

async function feedTable() {
        const res = await axios.post('/admin-branch/branch-configuration/webhook/logs-webhook')
            .then(res => res.data.data)
            .catch(err => {
                console.error(err)
                
                return []
            })
        
        if (!res.length) {
            $("#table tbody").html(`<tr>
                <td colspan="5" class="text-center">{{ __('Data not Found') }}</td>
            </tr>`)
            return
        }
        
        $("#table tbody").html(
            transformResponse(res)
        )
    }

function transformResponse(data) {
    var liveUrl = "{{ route('admin-branch.branch-configuration.webhook.resend', [':id', 'live']) }}";
    var sandboxUrl = "{{ route('admin-branch.branch-configuration.webhook.resend', [':id', 'sandbox']) }}";

    return data.map(v => {
        let liveAction = liveUrl.replace(':id', v.id);
        let sandboxAction = sandboxUrl.replace(':id', v.id);
        let actionButtons = '';

        if (v.status_code != 200) {
            actionButtons = `
            <td class="d-flex" style="gap:0.5rem;">
                <form action="${liveAction}" method="post" style="display:inline-block">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-success">Resend Live</button>
                </form>
                <form action="${sandboxAction}" method="post" style="display:inline-block">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-secondary">Resend Sandbox</button>
                </form>
            </td>`;
        }

        return `<tr style="color: #000">
            <td>${v.created_at_formatted}</td>
            <td class="text-right">${v.queue?.name ?? '-'}</td>
            <td class="text-right">${v.event_type}</td>
            <td class="text-right">${v.status_code}</td>
            ${actionButtons}
        </tr>`;
    }).join('');
}

</script>