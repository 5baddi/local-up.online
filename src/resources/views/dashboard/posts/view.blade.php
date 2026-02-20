@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('styles')
    <link href="{{ asset('assets/css/splide.min.css') }}" rel="stylesheet"/>
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-sm-6 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <div class="d-flex align-items-center">
                        <span class="avatar me-3 rounded">
                @switch(Str::lower($post['topicType']))
                                @case('offer')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount"><path stroke="none"
                                                                                                                          d="M0 0h24v24H0z"
                                                                                                                          fill="none"/><path
                                                d="M9 15l6 -6"/><circle cx="9.5" cy="9.5" r=".5" fill="currentColor"/><circle cx="14.5"
                                                                                                                              cy="14.5"
                                                                                                                              r=".5"
                                                                                                                              fill="currentColor"/><path
                                                d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7a2.2 2.2 0 0 0 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1a2.2 2.2 0 0 0 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1"/></svg>
                                    @break
                                @case('event')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-ticket"><path stroke="none"
                                                                                                                d="M0 0h24v24H0z"
                                                                                                                fill="none"/><path
                                                d="M15 5l0 2"/><path d="M15 11l0 2"/><path d="M15 17l0 2"/><path
                                                d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2"/></svg>
                                    @break
                                @case('alert')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-speakerphone"><path stroke="none"
                                                                                                                      d="M0 0h24v24H0z"
                                                                                                                      fill="none"/><path
                                                d="M18 8a3 3 0 0 1 0 6"/><path d="M10 8v11a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-5"/><path
                                                d="M12 8h0l4.524 -3.77a.9 .9 0 0 1 1.476 .692v12.156a.9 .9 0 0 1 -1.476 .692l-4.524 -3.77h-8a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h8"/></svg>
                                    @break
                                @default
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="icon icon-tabler icons-tabler-outline icon-tabler-baseline-density-medium"><path
                                                stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h16"/><path d="M4 12h16"/><path
                                                d="M4 4h16"/></svg>
                            @endswitch
              </span>
                        &nbsp;<strong>Topic type:</strong>&nbsp;{{ Str::lower($post['topicType'])  }}&nbsp;
                            @switch($post['state'] ?? '---')
                                @case('PROCESSING')
                                    <span class="badge bg-yellow text-yellow-fg">{{ Str::ucfirst(Str::lower($post['state'] ?? '---'))  }}</span>
                                    @break
                                @case('LIVE')
                                    <span class="badge bg-green text-green-fg">{{ Str::ucfirst(Str::lower($post['state'] ?? '---'))  }}</span>
                                    @break
                                @default
                                    <span class="badge bg-red text-red-fg">{{ Str::ucfirst(Str::lower($post['state'] ?? '---'))  }}</span>
                        @endswitch
                        </div>
                    </h3>
                </div>
                <div class="card-body">{{ $post['summary'] ?? '---' }}</div>
                <div class="card-footer">
                    <div class="d-flex align-items-center">
                        {{ \Carbon\Carbon::parse($post['updateTime'] ?? now())->diffForHumans() }}
                        <div class="ms-auto">
                            <a href="{{ $post['callToAction']['url'] ?? '#' }}" title="{{ Str::ucfirst(Str::lower(Str::replace('_', ' ', $post['callToAction']['actionType'] ?? 'LEARN_MORE'))) }}" target="_blank" class="btn btn-default">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-target-arrow"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 7a5 5 0 1 0 5 5" /><path d="M13 3.055a9 9 0 1 0 7.941 7.945" /><path d="M15 6v3h3l3 -3h-3v-3z" /><path d="M15 9l-3 3" /></svg>
                                &nbsp;Appel à l'action
                            </a>
                            <a href="{{ $post['searchUrl'] ?? '#' }}" title="View on google search" target="_blank" class="btn btn-default">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-brand-google-big-query"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17.73 19.875a2.225 2.225 0 0 1 -1.948 1.125h-7.283a2.222 2.222 0 0 1 -1.947 -1.158l-4.272 -6.75a2.269 2.269 0 0 1 0 -2.184l4.272 -6.75a2.225 2.225 0 0 1 1.946 -1.158h7.285c.809 0 1.554 .443 1.947 1.158l3.98 6.75a2.33 2.33 0 0 1 0 2.25l-3.98 6.75v-.033z" /><path d="M11.5 11.5m-3.5 0a3.5 3.5 0 1 0 7 0a3.5 3.5 0 1 0 -7 0" /><path d="M14 14l2 2" /></svg>
                                &nbsp;Voir sur la recherche Google
                            </a>
                            <form action="{{ route('dashboard.posts.delete', ['id' => last(explode('/', $post['name'] ?? ''))]) }}" method="POST" style="display: inline-block;">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-icon btn-danger" title="Delete" onclick="return confirm('Voulez-vous vraiment supprimer cet article ?')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    &nbsp;Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            @if(sizeof($post['media'] ?? []) > 0)
            <div class="card">
                <div class="card-body">
                    <section id="post-media-carousel" class="splide">
                        <div class="splide__track">
                            <ul class="splide__list">
                                @foreach(($post['media'] ?? []) as $index => $media)
                                    <li class="splide__slide">
                                        <img src="{{ $media['googleUrl'] ?? '#' }}" alt=""/>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection


@section('scripts')
    <script src="{{ asset('assets/js/splide.min.js') }}"></script>
@endsection

@section('script')
    document.addEventListener( 'DOMContentLoaded', function () {
    new Splide( '#post-media-carousel', {
    cover      : true,
    heightRatio: 1,
    } ).mount();
    } );
@endsection