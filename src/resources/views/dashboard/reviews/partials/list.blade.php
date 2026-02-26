@php
use Carbon\Carbon;

$starRatingMap = [
    'ONE' => 1,
    'TWO' => 2,
    'THREE' => 3,
    'FOUR' => 4,
    'FIVE' => 5,
];
@endphp

@foreach(($reviews['reviews'] ?? []) as $review)
    @php
        $rating = $starRatingMap[$review['starRating'] ?? ''] ?? 0;
    @endphp
    <div class="card review-card">
        <div class="card-body">
            <div class="row g-3 align-items-start">
                <div class="col-auto">
                    <div class="avatar avatar-md avatar-rounded"
                         style="background-image: url({{ $review['reviewer']['profilePhotoUrl'] ?? asset('assets/img/no-image.svg') }})"></div>
                </div>
                <div class="col">
                    <div class="d-flex align-items-center mb-1">
                        <h3 class="mb-0">{{ $review['reviewer']['displayName'] ?? '---' }}</h3>
                        <div class="ms-auto d-flex align-items-center">
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="icon gl-star-full {{ $i <= $rating ? 'text-warning' : 'text-muted' }}" width="20" height="20"
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
                    <div class="text-muted small mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
                        &nbsp;{{ Carbon::parse($review['updateTime'] ?? now())->diffForHumans() }}
                    </div>
                    @if(!empty($review['comment']))
                        <p class="text-secondary mb-0">{{ $review['comment'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex">
                <a href="{{ route('dashboard.reviews.view', ['id' => last(explode('/', $review['name'] ?? ''))]) }}"
                   class="btn btn-sm btn-default">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M8 9h8"/>
                        <path d="M8 13h6"/>
                        <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"/>
                    </svg>
                    &nbsp;{{ trans('dashboard.reply') }}
                </a>
            </div>
        </div>
    </div>
@endforeach
