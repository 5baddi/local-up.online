<aside class="navbar navbar-vertical navbar-expand-lg navbar-transparent d-print-none" style="right: 0; left: unset; padding-left: 1rem;">
    <div class="container-fluid">
        <div class="row justify-content-center mt-4">
            <div class="col-auto">
                <div class="btn-list">
                    <div class="nav-item dropdown">
                      <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                        <span class="avatar avatar-sm">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                          </svg>
                        </span>
                        <div class="d-none d-xl-block ps-2">
                          <div>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                        </div>
                      </a>
                      <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('dashboard.plan') }}" class="dropdown-item">Your plan</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('dashboard.account') }}" class="dropdown-item">Account</a>
                        {{-- <a href="{{ route('dashboard.activity') }}" class="dropdown-item">Activity</a> --}}
                        <a href="{{ route('signout') }}" class="dropdown-item">Logout</a>
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="col-auto mb-3">
            <div class="text-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                Notifications
                @if ($unreadNotifications->count() > 0)
                <a style="float: right;" href="{{ route('dashboard.activity.read.all') }}">Clear all</a>
                @endif
            </div>
        </div>
        @include('partials.dashboard.notifications')
    </div>
</aside>