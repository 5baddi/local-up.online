
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ config('app.name') }} &mdash; @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.mini.png') }}"/>
    <!-- CSS files -->
    <link href="{{ asset('assets/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/tabler-flags.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/tabler-payments.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/tabler-vendors.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/daterangepicker.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/bootstrap-tagsinput.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/baddi.services.css') }}" rel="stylesheet"/>
  </head>
  <body class="border-top-wide d-flex flex-column">
    <div class="page page-center">
      <div class="container-tight py-4">
        <div class="empty">
          <div class="empty-img">
            @yield('image')
          </div>
          <p class="empty-title">@yield('heading')</p>
          <p class="empty-subtitle text-muted">
            @yield('message')
          </p>
          <div class="empty-action">
            <a href="{{ env('SAAS_URL', 'https://local-up.online') }}" class="btn btn-clnkgo">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <polyline points="5 12 3 12 12 3 21 12 19 12"></polyline>
                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
              </svg>
              &nbsp;Go home
            </a>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>