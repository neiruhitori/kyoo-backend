@extends('layouts.app')

@section('content')
<div class="card mb-4 custom-info" data-open="open" role="alert">
    <div class="card-body">
        <div class="custom-info-head">
            <h6 class="font-weight-bold my-0">
                <span class="fas fa-info-circle text-primary mr-1"></span>
                Informasi
            </h6>

            <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                Tampilkan
            </button>
        </div>

        <div class="custom-info-body">
            <p>
                <ul style="padding-left: 2rem;">
                    <li style="margin-bottom: 0.25rem;">
                        Ini adalah konfigurasi fitur yang bisa anda sesuaikan dengan kebutuhan.
                    </li>
                    <li>
                        Untuk API Antrian KYOO hanya akan tampil Ketika cabang sudah memiliki license API. API KYOO dapat di-integrasikan ke berbagai channel layanan anda seperti WA, Telegram dll.
                    </li>
                </ul>
            </p>
            <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.alert')

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('update.module', ['module' => __('Branch Configuration')]) }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('admin-branch.branch-configuration.feature.update')}}" method="post">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="allow_transfer">Panggilan Suara Antrian</label>
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        name="queue_voice"
                                        class="form-check-input"
                                        id="queue-voice-label"
                                        {{ ($branch_config && $branch_config->queue_voice) || old('queue_voice') ? 'checked' : '' }}
                                    >

                                    <label for="queue-voice-label" class="form-check-label">Aktifkan</label>
                                </div>
                                @include('layouts.inputError', ['errorName' => 'queue_voice'])
                            </div>

                            <button type="submit" class="btn btn-warning">{{ __('Update') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('Kyoo Queue API') }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <b class="my-4">
                            {{
                                __('Branch Token API only available for premium license branch and registered for API. Please contact')
                            }}
                            <a href="mailto:support@kyoo.id"> support@kyoo.id</a>
                        </b>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection