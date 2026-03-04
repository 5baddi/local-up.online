@php
use Carbon\Carbon;

$starRatingMap = [
    'ONE' => 1,
    'TWO' => 2,
    'THREE' => 3,
    'FOUR' => 4,
    'FIVE' => 5,
];
$rating = $starRatingMap[$review['starRating'] ?? ''] ?? 0;
@endphp

@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="mb-1">
                    <a href="{{ route('dashboard.reviews') }}" class="text-muted text-decoration-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0"/><path d="M5 12l6 6"/><path d="M5 12l6 -6"/></svg>
                        {{ trans('global.reviews') }}
                    </a>
                </div>
                <h2 class="page-title">
                    {{ $review['reviewer']['displayName'] ?? '---' }}
                </h2>
            </div>
            <div class="col-auto">
                <div class="review-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="icon gl-star-full {{ $i <= $rating ? 'text-warning' : 'text-muted' }}" width="24" height="24"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                             stroke-linecap="round" stroke-linejoin="round"
                             style="pointer-events: none;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z"
                                  stroke-width="0" fill="currentColor"></path>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-auto">
                            <div class="avatar avatar-lg avatar-rounded" style="background-image: url({{ $review['reviewer']['profilePhotoUrl'] ?? asset('assets/img/no-image.svg') }})"></div>
                        </div>
                        <div class="col">
                            <h3 class="mb-1">{{ $review['reviewer']['displayName'] ?? '---' }}</h3>
                            <div class="text-muted small mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
                                &nbsp;{{ trans('dashboard.commented') }}&nbsp;{{ Carbon::parse($review['updateTime'] ?? now())->diffForHumans() }}
                            </div>
                            @if(!empty($review['comment']))
                                <p class="text-secondary">{{ $review['comment'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('dashboard.reviews.reply.update', ['id' => $reviewId]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="review" value="{{ $review['comment'] ?? '' }}"/>
                    <div class="card-body border-top">
                        <label class="form-label">{{ trans('dashboard.reply') }}&nbsp;<span class="form-label-description" id="review-reply-length">{{ strlen(old('reply') ?? $review['reviewReply']['comment'] ?? '') }}/1500</span></label>
                        <textarea onkeyup="calculateTextLength(event, '#review-reply-length', '/1500')" class="form-control" name="reply" rows="8" maxlength="1500" placeholder="{{ trans('dashboard.write_reply') }}">{{ old('reply') ?? $review['reviewReply']['comment'] ?? '' }}</textarea>
                    </div>
                    <div class="card-footer d-flex">
                        <button type="submit" name="action" value="generate-reply" class="btn btn-default">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wand"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 21l15 -15l-3 -3l-15 15l3 3" /><path d="M15 6l3 3" /><path d="M9 3a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2" /><path d="M19 13a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2" /></svg>
                            &nbsp;{{ trans('dashboard.generate_reply') }}
                        </button>
                        <button type="submit" class="btn btn-clnkgo ms-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                            &nbsp;{{ trans('dashboard.save_reply') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
