@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('content')
    <div class="row align-items-center mb-3">
        <div class="col"></div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('dashboard.media.new') }}" class="btn btn-clnkgo btn-icon">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                    &nbsp;{{ trans('global.upload_new_media') }}
                </a>
                <a href="{{ route('dashboard.scheduled.media') }}" class="btn btn-default btn-icon">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-time"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /><path d="M18 16.496v1.504l1 1" /></svg>
                    &nbsp;{{ trans('global.view_scheduled_media') }}
                </a>
            </div>
        </div>
    </div>
    @if(sizeof(($media['mediaItems'] ?? [])) > 0)
        <div class="row row-cards row-deck" id="media-container">
            @include('dashboard.media.partials.gallery')
        </div>
        @if(! empty($media['nextPageToken']))
            <input name="gmb_next" type="hidden" value="{{ $media['nextPageToken'] }}"/>
            <div class="row mt-3">
                <div class="col">
                    <a id="load-more-btn" href="javascript:void(0);" onclick="loadMoreMedia()"
                       class="btn btn-icon btn-clnkgo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round"
                             class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14"/>
                            <path d="M5 12l14 0"/>
                        </svg>
                        &nbsp;Charger plus
                    </a>
                </div>
            </div>
        @endif
    @else
    @include('dashboard.errors.empty')
    @endif
@endsection

@section('script')
    function loadMoreMedia() {
    $.ajax({
    type: "GET",
    url: new URL("{{ route('dashboard.media') }}"),
    data: { tab: 'gmb', next: jQuery('input[name=gmb_next]').val() },
    beforeSend: function(xhr) {
    $('#load-more-btn').addClass('disabled');
    }
    }).done(function(data, textStatus, jqXHR) {
    let next = jqXHR.getResponseHeader('Gmb-Next');

    jQuery('input[name=gmb_next]').val(next);
    $('#media-container').append(data);

    if (next.length === 0) {
    $('#load-more-btn').parents('.row').remove();
    }else {
    $('#load-more-btn').removeClass('disabled');
    }
    });
    }
@endsection