@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('content')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                {{ trans('global.reviews') }}
            </h2>
        </div>
        @if(sizeof($reviews['reviews'] ?? []) > 0)
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <span class="form-check-label">{{ trans('global.answered') }}</span>
                <form action="{{ route('dashboard.reviews') }}">
                <label class="form-check form-check-single form-switch p-0">
                  <input name="has_replies" value="1" onchange="this.form.submit()" class="form-check-input pointer-cursor" type="checkbox" @if(request()->boolean('has_replies')) checked @endif/>
                </label>
                </form>
            </div>
        </div>
        @endif
    </div>
    @if(sizeof($reviews['reviews'] ?? []) > 0)
    <div class="row row-cards mt-2">
        <div class="space-y" id="reviews-container">
            @include('dashboard.reviews.partials.list')
        </div>
    </div>
    @else
    @include('dashboard.errors.empty')
    @endif
    @if(! empty($reviews['nextPageToken']))
        <input name="gmb_next" type="hidden" value="{{ $reviews['nextPageToken'] }}"/>
        <div class="row mt-3">
            <div class="col">
                <a id="load-more-btn" href="javascript:void(0);" onclick="loadMoreReviews()"
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