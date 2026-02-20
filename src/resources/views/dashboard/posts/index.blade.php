@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('content')
    @if(sizeof($posts ?? []) > 0)
        <div class="row row-cards row-deck" id="posts-container">
            @include('dashboard.posts.partials.gallery')
        </div>
        @if(! empty($posts['nextPageToken']))
            <input name="gmb_next" type="hidden" value="{{ $posts['nextPageToken'] }}"/>
            <div class="row mt-3">
                <div class="col">
                    <a id="load-more-btn" href="javascript:void(0);" onclick="loadMorePosts()"
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
                        &nbsp;{{ trans('global.load_more') }}
                    </a>
                </div>
            </div>
        @endif
    @else
    @include('dashboard.errors.empty')
    @endif
@endsection

@section('script')
    function loadMorePosts() {
    $.ajax({
    type: "GET",
    url: new URL("{{ route('dashboard') }}"),
    data: { tab: 'gmb', next: jQuery('input[name=gmb_next]').val() },
    beforeSend: function(xhr) {
    $('#load-more-btn').addClass('disabled');
    }
    }).done(function(data, textStatus, jqXHR) {
    let next = jqXHR.getResponseHeader('Gmb-Next');

    jQuery('input[name=gmb_next]').val(next);
    $('#posts-container').append(data);

    if (next.length === 0) {
    $('#load-more-btn').parents('.row').remove();
    }else {
    $('#load-more-btn').removeClass('disabled');
    }
    });
    }
@endsection