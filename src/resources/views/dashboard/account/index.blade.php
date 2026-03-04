@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@php
    $tab = isset($tab) ? $tab : 'settings';
@endphp

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ trans('global.account') }}
                </h2>
                <div class="text-muted mt-1">{{ $user->getFullName() }}</div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a href="{{ route('dashboard.account', ['tab' => 'gmb']) }}"
                                class="nav-link {{ $tab === 'gmb' ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-google me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20.945 11a9 9 0 1 1 -3.284 -5.997l-2.655 2.392a5.5 5.5 0 1 0 2.119 6.605h-4.125v-3h7.945z"/></svg>
                                Google My Business
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard.account', ['tab' => 'settings']) }}"
                                class="nav-link {{ $tab === 'settings' ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>
                                General info
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard.account', ['tab' => 'password']) }}"
                                class="nav-link {{ $tab === 'password' ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-lock me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"/><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"/><path d="M8 11v-4a4 4 0 1 1 8 0v4"/></svg>
                                Account Password
                            </a>
                        </li>
                    </ul>
                </div>

                <form action="{{ route('dashboard.account.save', ['tab' => $tab]) }}" method="POST" id="main-form">
                    @csrf
                    <input type="hidden" id="emails" name="emails" />

                    <div class="card-body">
                        @switch ($tab)
                            @case('settings')
                                @include('dashboard.account.partials.info')
                            @break

                            @case('password')
                                @include('dashboard.account.partials.password')
                            @break

                            @default
                                @include('dashboard.account.partials.gmb')
                        @endswitch
                    </div>

                    @if ($tab !== 'gmb')
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-clnkgo">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2">
                                    </path>
                                    <circle cx="12" cy="14" r="2"></circle>
                                    <polyline points="14 4 14 8 8 8 8 4"></polyline>
                                </svg>
                                &nbsp;Enregistrer
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('partials.dashboard.scripts.form')
@endsection

@section('script')
    $('document').ready(function() {
    $('#tags-input').tagsinput({
    cancelConfirmKeysOnEmpty: false,
    trimValue: true,
    allowDuplicates: false,
    });

    var tags = $('#tags-input').tagsinput('items');
    if (typeof tags !== 'undefined') {
    $('#tags-count').text(tags.length || 0);

    $('#tags-input').on('itemAdded', function(event) {
    var tags = $('#tags-input').tagsinput('items');

    $('#tags-count').text(tags.length || 0);
    $('#emails').val(tags.join(','));
    });

    $('#tags-input').on('itemRemoved', function(event) {
    var tags = $('#tags-input').tagsinput('items');

    $('#tags-count').text(tags.length || 0);
    $('#emails').val(tags.join(','));
    });
    }
    });
@endsection
