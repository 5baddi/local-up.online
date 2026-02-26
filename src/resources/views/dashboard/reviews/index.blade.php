@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ trans('global.reviews') }}
                </h2>
                <div class="text-muted mt-1">{{ sizeof($reviews['reviews'] ?? []) }} {{ Str::lower(trans('global.reviews')) ?? 'reviews' }}</div>
            </div>
            @if(sizeof($reviews['reviews'] ?? []) > 0)
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex align-items-center gap-2">
                    <span class="form-check-label text-muted">{{ trans('global.answered') }}</span>
                    <form action="{{ route('dashboard.reviews') }}">
                    <label class="form-check form-check-single form-switch p-0 mb-0">
                      <input name="has_replies" value="1" onchange="this.form.submit()" class="form-check-input pointer-cursor" type="checkbox" @if(request()->boolean('has_replies')) checked @endif/>
                    </label>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(sizeof($reviews['reviews'] ?? []) > 0)
    <div class="row row-cards">
        <div class="col-12">
            <div class="space-y" id="reviews-container">
                @include('dashboard.reviews.partials.list')
            </div>
        </div>
    </div>
    @else
    @include('dashboard.errors.empty')
    @endif
    @if(! empty($reviews['nextPageToken']))
        <input name="gmb_next" type="hidden" value="{{ $reviews['nextPageToken'] }}"/>
        <div class="row mt-4">
            <div class="col text-center">
                <a id="load-more-btn" href="javascript:void(0);" onclick="loadMoreReviews()"
                   class="btn btn-clnkgo">
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
@endsection

@section('script')
    function loadMoreReviews() {
    $.ajax({
    type: "GET",
    url: new URL(`{{ route('dashboard.reviews', ['has_replies' => '']) }}${$('input[name=has_replies]').is(':checked') ? 1 : 0}`),
    data: { tab: 'gmb', next: jQuery('input[name=gmb_next]').val() },
    beforeSend: function(xhr) {
    $('#load-more-btn').addClass('disabled');
    }
    }).done(function(data, textStatus, jqXHR) {
    let next = jqXHR.getResponseHeader('Gmb-Next');

    jQuery('input[name=gmb_next]').val(next);
    $('#reviews-container').append(data);

    if (next.length === 0) {
    $('#load-more-btn').parents('.row').remove();
    }else {
    $('#load-more-btn').removeClass('disabled');
    }
    });
    }
@endsection
