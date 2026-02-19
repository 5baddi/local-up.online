<div class="container-xl">
    <!-- Page title -->
    <div class="page-header d-print-none" style="margin: 4rem 0 !important;">
      <div class="row align-items-center">
        <div class="col-md-10 d-flex justify-content-start">
          <!-- Page pre-title -->
          <div class="page-pretitle">
            &nbsp;
          </div>
          <h2 class="page-title">
              @yield('title')
          </h2>
          @if (! request()->routeIs(['dashboard', 'dashboard.*']) && ! request()->routeIs(['admin', 'admin.*']))
          <div class="mt-2">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item">
                  <a href="{{ route('dashboard') }}">{{ trans('dashboard.title') }}</a>
                </li>
              </ol>
          </div>
          @endif
        </div>
        <div class="col-md-2 d-flex justify-content-end">
          <div class="nav-item dropdown">
            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu" aria-expanded="false">
              <span class="avatar avatar-sm" style="background-image: url({{ $avatar }})"></span>
              <div class="d-none d-xl-block ps-2">
                <div>{{ $user->getFullName() }}</div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <a href="{{ route('dashboard.account') }}" class="dropdown-item {{ request()->routeIs('dashboard.account') ? 'active' : '' }}">{{ trans('global.account') }}</a>
              <a href="{{ route('signout') }}" class="dropdown-item">{{ trans('auth.logout') }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>