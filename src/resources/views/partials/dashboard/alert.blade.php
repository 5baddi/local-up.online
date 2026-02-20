@if (Session::has('alert'))
@php
    $alert = Session::get('alert');
@endphp
<div class="alert alert-@if ($alert->type == 'error')danger @elseif($alert->type == 'success')success @endif alert-dismissible mb-4" role="alert">
    <div class="d-flex">
        <div>
            @if ($alert->type == 'error')
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <circle cx="12" cy="12" r="9"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            @else
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M5 12l5 5l10 -10"></path>
            </svg>
            @endif
        </div>
        <div>
            <h4 class="alert-title">
                @if ($alert->type == 'error')
                &nbsp;{{ trans('global.something_going_wrong') }}&hellip;
                @else
                &nbsp;{{ trans('global.finished') }}
                @endif
            </h4>
            <div class="text-muted">&nbsp;{{ $alert->message }}</div>
        </div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif