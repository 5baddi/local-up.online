<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('dashboard') }}">
{{--                <img src="{{ asset('assets/img/logo.jpg') }}" width="110" height="32"--}}
                <img src="{{ asset('assets/img/app-logo.png') }}"
                     alt="{{ config('app.name') }}" class="navbar-brand-image"/>
            </a>
        </h1>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav pt-lg-3">
                @if(request()->routeIs(['dashboard', 'dashboard.*']))
                    <li class="nav-item {{ request()->routeIs(['dashboard', 'dashboard.posts', 'dashboard.posts.*']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-article"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M7 8h10" /><path d="M7 12h10" /><path d="M7 16h10" /></svg>
                </span>
                            <span class="nav-link-title">{{ trans('dashboard.posts') }}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs(['dashboard.scheduled.posts', 'dashboard.scheduled.posts.*']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard.scheduled.posts') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                       class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-time"><path stroke="none"
                                                                                                     d="M0 0h24v24H0z"
                                                                                                     fill="none"/><path
                              d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"/><path
                              d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M15 3v4"/><path d="M7 3v4"/><path
                              d="M3 11h16"/><path d="M18 16.496v1.504l1 1"/></svg>
                </span>
                            <span class="nav-link-title">{{ trans('dashboard.scheduled_posts') }}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs(['dashboard.media', 'dashboard.media.*', 'dashboard.scheduled.media', 'dashboard.scheduled.media.*']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard.media') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                       class="icon icon-tabler icons-tabler-outline icon-tabler-library-photo"><path stroke="none"
                                                                                                     d="M0 0h24v24H0z"
                                                                                                     fill="none"/><path
                              d="M7 3m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z"/><path
                              d="M4.012 7.26a2.005 2.005 0 0 0 -1.012 1.737v10c0 1.1 .9 2 2 2h10c.75 0 1.158 -.385 1.5 -1"/><path
                              d="M17 7h.01"/><path d="M7 13l3.644 -3.644a1.21 1.21 0 0 1 1.712 0l3.644 3.644"/><path
                              d="M15 12l1.644 -1.644a1.21 1.21 0 0 1 1.712 0l2.644 2.644"/></svg>
                </span>
                            <span class="nav-link-title">{{ trans('dashboard.photos') }}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs(['dashboard.reviews', 'dashboard.reviews.*']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard.reviews') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                       class="icon icon-tabler icons-tabler-outline icon-tabler-map-star"><path stroke="none"
                                                                                                d="M0 0h24v24H0z"
                                                                                                fill="none"/><path
                              d="M9.718 17.359l-.718 -.359l-6 3v-13l6 -3l6 3l6 -3v7.5"/><path d="M9 4v13"/><path
                              d="M15 7v4"/><path
                              d="M17.8 20.817l-2.172 1.138a.392 .392 0 0 1 -.568 -.41l.415 -2.411l-1.757 -1.707a.389 .389 0 0 1 .217 -.665l2.428 -.352l1.086 -2.193a.392 .392 0 0 1 .702 0l1.086 2.193l2.428 .352a.39 .39 0 0 1 .217 .665l-1.757 1.707l.414 2.41a.39 .39 0 0 1 -.567 .411l-2.172 -1.138z"/></svg>
                </span>
                            <span class="nav-link-title">{{ trans('dashboard.reviews') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="col-auto align-self-center mt-1 text-center">
                        <a href="{{ route('signout') }}" class="btn btn-clnkgo btn-icon w-100 mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout"
                                 width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                 fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                                <path d="M7 12h14l-3 -3m0 6l3 -3"></path>
                            </svg>
                            &nbsp;{{ trans('auth.logout') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

<header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav flex-row order-md-last">
            @if(sizeof($userAccountLocations) > 0 && $user->isGoogleAccountAuthenticated())
             <div class="d-none d-md-flex">
              <div class="nav-item dropdown d-none d-md-flex me-3">
                  <div class="form-floating">
                      <input name="preferred-location" type="text" class="form-control" list="account-locations" placeholder="{{ trans('global.set_main_location') }}"/>
                      <label for="preferred-location">{{ trans('global.main_location') }}</label>
                  </div>
                  <datalist id="account-locations">
                      @foreach($userAccountLocations as $userAccountLocation)
                      @if(empty($userAccountLocation['title'] ?? null) || empty($userAccountLocation['location_id'] ?? null))
                          @continue
                      @endif
                        <option value="{{ $userAccountLocation['title'] }}" data-location-id="{{ $userAccountLocation['location_id'] }}">
                      @endforeach
                  </datalist>
              </div>
            </div>
            @endif
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                   aria-label="Open user menu" aria-expanded="false">
                    <span class="avatar avatar-sm" style="background-image: url({{ $avatar }})"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ $user->getFullName() }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('dashboard.account') }}"
                       class="dropdown-item {{ request()->routeIs('dashboard.account') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                        </svg>
                        &nbsp;{{ trans('global.account') }}
                    </a>
                    <a href="{{ route('signout') }}" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/>
                            <path d="M9 12h12l-3 -3"/>
                            <path d="M18 15l3 -3"/>
                        </svg>
                        &nbsp;{{ trans('auth.logout') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">{{ trans('dashboard.title') }}</a>
                </li>
            </ol>
        </div>
    </div>
</header>