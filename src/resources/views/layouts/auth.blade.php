<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
        <meta name="author" content="Baddi Services"/>
        <title>{{ config('app.name') }} &mdash; @yield('title')</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.mini.png') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/css/baddiservices.main.css') }}"/>
    </head>

    <body>
        <section style="background-color: #f4f6fa !important;">
            <div class="container d-flex flex-column">
                <div class="row align-items-center justify-content-center min-vh-100">
                    <div class="col-md-8 col-lg-6 col-xl-6 py-6 py-md-0">
                        <div class="col-md-12 text-center mb-4">
                            <a href="{{ url('/') }}" class="text-center top-logo">
                                <img src="{{ asset('assets/img/app-logo.png') }}" alt="{{ config('app.name') }}"/>
                            </a>
                        </div>
                        <div class="card shadow zindex-100 mb-0">
                        @yield('form')
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/popper.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ config('baddi.hcaptcha_js_endpoint') }}" async defer></script>
        <script src="{{ asset('assets/js/baddiservices.main.js') }}"></script>
        <script type="text/javascript">
            @yield('script')
        </script>
    </body>
</html>