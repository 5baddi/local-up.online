@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/libs/dropzone/dist/dropzone.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <form id="upload-media-form" class="card" method="POST" action="{{ route('dashboard.media.upload') }}" enctype="multipart/form-data">
                @csrf
                <input name="id" value="{{ $scheduledMedia?->id ?? null }}" hidden/>
                <div class="card-header">
                    <h3 class="card-title">{{ $title }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3 dropzone" id="upload-media">
                        <div class="dz-default dz-message">
                            <button class="dz-button" type="button">{{ trans('global.drop_media_here') }}</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary">
                            {{ trans('global.media_allowed_mimetypes') }}
                        </small>
                        <br/>
                        <small class="text-secondary">
                            {{ trans('global.media_allowed_file_size') }}
                        </small>
                    </div>
                    <div class="accordion">
                        <div class="accordion-item" id="accordion-auto-posting">
                            <h2 class="accordion-header" id="heading-1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1" aria-expanded="true">
                                    {{ trans('global.auto_posting') }}
                                </button>
                            </h2>
                            <div id="collapse-1" class="accordion-collapse collapse" data-bs-parent="#accordion-auto-posting" style="">
                                <div class="accordion-body pt-3">
                                    <div class="row">
                                        <div class="col-4">
                                            <label class="form-label">{{ trans('global.scheduled_date') }}</label>
                                            <input type="date" name="scheduled_date" min="{{ date('Y-m-d', time()) }}" class="form-control @if ($errors->has('scheduled_date')) is-invalid @endif" value="{{ $scheduledMedia?->scheduled_at?->format('Y-m-d') ?? ('scheduled_date') }}"/>
                                            @if ($errors->has('scheduled_date'))
                                                <div class="invalid-feedback">{{ $errors->first('scheduled_date') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">{{ trans('global.scheduled_time') }}</label>
                                            <input type="time" name="scheduled_time" class="form-control @if ($errors->has('scheduled_time')) is-invalid @endif" value="{{ $scheduledMedia?->scheduled_at?->format('H:i') ?? old('scheduled_time') }}"/>
                                            @if ($errors->has('scheduled_time'))
                                                <div class="invalid-feedback">{{ $errors->first('scheduled_time') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">{{ trans('global.frequency') }}</label>
                                            <select name="scheduled_frequency" class="form-select @if ($errors->has('scheduled_frequency')) is-invalid @endif">
                                                <option @if (($scheduledMedia?->scheduled_frequency ?? old('scheduled_frequency')) === 'daily' || empty(($scheduledMedia?->scheduled_frequency ?? old('scheduled_frequency')))) selected @endif value="daily">{{ trans('global.daily') }}</option>
                                                <option @if (($scheduledMedia?->scheduled_frequency ?? old('scheduled_frequency')) === '3_days') selected @endif value="3_days">{{ trans('global.3_days') }}</option>
                                                <option @if (($scheduledMedia?->scheduled_frequency ?? old('scheduled_frequency')) === 'weekly') selected @endif value="weekly">{{ trans('global.weekly') }}</option>
                                            </select>
                                            @if ($errors->has('scheduled_frequency'))
                                                <div class="invalid-feedback">{{ $errors->first('scheduled_frequency') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-clnkgo" id="upload-media-btn">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-cloud-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" /><path d="M9 15l3 -3l3 3" /><path d="M12 12l0 9" /></svg>
                        &nbsp;Télécharger
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/libs/dropzone/dist/dropzone-min.js"></script>
@endsection

@section('script')
    document.addEventListener("DOMContentLoaded", async () => {
        let dropzoneInstance = new Dropzone("#upload-media", {
            url: '{{ route('dashboard.media.upload') }}',
            dictRemoveFile: '{{ trans('global.remove_file') }}',
            dictCancelUpload: '{{ trans('global.cancel_upload') }}',
            dictCancelUploadConfirmation: '{{ trans('global.confirm_cancel_upload') }}',
            addRemoveLinks: true,
            uploadMultiple: true,
            parallelUploads: 100,
            maxFiles: 100,
            autoProcessQueue: false,
            acceptedFiles: 'image/jpeg, image/png, image/gif, image/bmp, image/tiff, image/webp, video/mp4, video/quicktime, video/x-msvideo, video/mpeg, video/x-ms-wmv',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            init: function() {
                let myDropzone = this;

                $('#upload-media-btn').on("click", function (e) {
                    e.preventDefault();

                    $('#upload-media-btn').attr('disabled', true);

                    myDropzone.processQueue();
                });

                this.on('sendingmultiple', function(file, xhr, formData) {
                    let data = $('#upload-media-form').serializeArray();

                    $.each(data, function(key, el) {
                        formData.append(el.name, el.value);
                    });
                });
                
                this.on('successmultiple', function(files, response) {
                    $('#upload-media-btn').attr('disabled', false);

                    // Check if all files have been uploaded
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        // Clear all files from the form
                        this.removeAllFiles();
                    }

                    let scheduledDate = $('input[name=scheduled_date]').val();

                    $('#upload-media-form').trigger('reset');

                    if (scheduledDate.length > 0) {
                        alert("{{ trans('global.scheduled_media_saved') }}");
                        window.location.replace('{{ route('dashboard.scheduled.media') }}');

                        return;
                    }
                    
                    alert("{{ trans('global.scheduled_media_posted') }}");
                });
                
                this.on('errormultiple', function(files, response) {
                    $('#upload-media-btn').attr('disabled', false);

                    alert("{{ trans('global.unsupported_file_format') }}");
                });
            }
        });

        let urls = {!! json_encode(array_map(fn ($media) => asset($media['path'] ?? ''), $scheduledMedia?->files ?? [])) !!};

        for (const url of urls) {
            if (typeof url !== 'string' || url.length === 0) {
                continue;
            }

            try {
                let response = await fetch(url);
                let blob = await response.blob();
                let fileName = url.split('/').pop();
                let file = new File([blob], fileName, { type: blob.type });

                dropzoneInstance.addFile(file);
            } catch {}
        }
    })
@endsection