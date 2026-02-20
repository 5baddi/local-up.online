@php use Illuminate\Support\Str; @endphp
<div class="row">
    @if ($user->isGoogleAccountAuthenticated())
        <p class="text-muted">{!! trans('dashboard.choose_main_gmb_account') !!}</p>
        <div class="col-6 mb-3">
            <div class="row">
                <div class="col-auto">
                    <a class="btn btn-icon btn-outline-danger" title="Delete"
                       onclick="return confirm('{{ trans('dashboard.disconnect_gmb_question') }}');"
                       href="{{ route('dashboard.account.gmb.disconnect') }}" class="btn btn-outline-danger btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round"
                             class="icon icon-tabler icons-tabler-outline icon-tabler-plug-connected">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 12l5 5l-1.5 1.5a3.536 3.536 0 1 1 -5 -5l1.5 -1.5z"/>
                            <path d="M17 12l-5 -5l1.5 -1.5a3.536 3.536 0 1 1 5 5l-1.5 1.5z"/>
                            <path d="M3 21l2.5 -2.5"/>
                            <path d="M18.5 5.5l2.5 -2.5"/>
                            <path d="M10 11l-2 2"/>
                            <path d="M13 14l-2 2"/>
                        </svg>
                        &nbsp;{{ trans('dashboard.disconnect_from_gmb') }}
                    </a>
                </div>
            </div>
        </div>
    @else
        <p class="text-muted">{!! trans('dashboard.link_gmb_account') !!}</p>
        <div class="col-6 mb-3">
            <div class="row">
                <div class="col-auto">
                    <a href="{{ $callbackURL ?? '#' }}" type="submit" class="btn btn-white btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round"
                             class="icon icon-tabler icons-tabler-outline icon-tabler-plug-connected">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 12l5 5l-1.5 1.5a3.536 3.536 0 1 1 -5 -5l1.5 -1.5z"/>
                            <path d="M17 12l-5 -5l1.5 -1.5a3.536 3.536 0 1 1 5 5l-1.5 1.5z"/>
                            <path d="M3 21l2.5 -2.5"/>
                            <path d="M18.5 5.5l2.5 -2.5"/>
                            <path d="M10 11l-2 2"/>
                            <path d="M13 14l-2 2"/>
                        </svg>
                        &nbsp;Connect with Google My Business
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
