<div class="container-xl">
    <!-- Page title -->
    <div class="page-header d-print-none">
      <div class="row align-items-center">
        <div class="col">
          <!-- Page pre-title -->
          <div class="page-pretitle">
            &nbsp;
          </div>
          <h2 class="page-title">
              @yield('title')
          </h2>
          @if (!request()->routeIs('dashboard'))
          <div class="mt-2">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @foreach (request()->segments() as $key => $segment)
                    @if ($segment === 'dashboard')
                    @continue
                    @endif
                    <li class="breadcrumb-item {{ (sizeof(request()->segments()) - 1) === ($key) ? 'active' : '' }}"><a href="{{ url('dashboard/' . $segment) }}">{{ ucfirst($segment) }}</a></li>
                @endforeach
              </ol>
          </div>
          @endif
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <div class="nav-item dropdown d-none d-md-flex me-3">
              <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                @if (auth()->user()->unreadNotifications->count() > 0)
                <span class="badge bg-red">
                  {{ auth()->user()->unreadNotifications->count() }}
                </span>
                @endif
              </a>
              @if (auth()->user()->unreadNotifications->count() > 0 && auth()->user()->unreadNotifications->first())
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
                <div class="card">
                  <div class="card-body">
                    {{ auth()->user()->unreadNotifications->first()->data['subject'] }}
                  </div>
                  <div class="card-footer">
                    <a href="{{ route('dashboard.activity') }}" class="btn btn-ghost-dark w-100">
                      Read all
                    </a>
                  </div>
                </div>
              </div>
              @endif
            </div>
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
                  <div class="mt-1 small text-muted">{{ date('l d M Y') }}</div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <a href="{{ route('dashboard.plan') }}" class="dropdown-item">Your plan</a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('dashboard.account') }}" class="dropdown-item">Account</a>
                <a href="{{ route('dashboard.activity') }}" class="dropdown-item">Activity</a>
                <a href="{{ route('signout') }}" class="dropdown-item">Logout</a>
              </div>
            </div>
            {{-- <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-report">
              <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
              Create new report
            </a>
            <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
              <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
            </a> --}}
          </div>
        </div>
      </div>
    </div>
</div>