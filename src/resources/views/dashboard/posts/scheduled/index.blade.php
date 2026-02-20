@php
    use Illuminate\Support\Str;
    use BADDIServices\ClnkGO\Models\ScheduledPost;
@endphp

@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title">{{ trans('dashboard.scheduled_posts') }}</h3>
                    <div class="ms-auto">
                        <button data-bs-toggle="dropdown" type="button" class="btn btn-clnkgo dropdown-toggle"
                                aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14"/>
                                <path d="M5 12l14 0"/>
                            </svg>
                            &nbsp;{{ trans('global.create_new') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" style="">
                            <a class="dropdown-item"
                               href="{{ route('dashboard.scheduled.posts.edit', ['type' => 'standard']) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round"
                                     class="icon icon-tabler icons-tabler-outline icon-tabler-article">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/>
                                    <path d="M7 8h10"/>
                                    <path d="M7 12h10"/>
                                    <path d="M7 16h10"/>
                                </svg>
                                &nbsp;{{ trans('global.standard') }}
                            </a>
                            <a class="dropdown-item"
                               href="{{ route('dashboard.scheduled.posts.edit', ['type' => 'event']) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round"
                                     class="icon icon-tabler icons-tabler-outline icon-tabler-ticket">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M15 5l0 2"/>
                                    <path d="M15 11l0 2"/>
                                    <path d="M15 17l0 2"/>
                                    <path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2"/>
                                </svg>
                                &nbsp;{{ trans('global.event') }}
                            </a>
                            <a class="dropdown-item"
                               href="{{ route('dashboard.scheduled.posts.edit', ['type' => 'offer']) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round"
                                     class="icon icon-tabler icons-tabler-outline icon-tabler-discount">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 15l6 -6"/>
                                    <circle cx="9.5" cy="9.5" r=".5" fill="currentColor"/>
                                    <circle cx="14.5" cy="14.5" r=".5" fill="currentColor"/>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                </svg>
                                &nbsp;{{ trans('global.offer') }}
                            </a>
                            <a class="dropdown-item"
                               href="{{ route('dashboard.scheduled.posts.edit', ['type' => 'alert']) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round"
                                     class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                                    <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                                </svg>
                                &nbsp;{{ trans('global.alert') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-mobile-md card-table">
                        <thead>
                        <tr>
                            <th>{{ trans('dashboard.event.type') }}</th>
                            <th>{{ trans('dashboard.event.summary') }}</th>
                            <th>{{ trans('dashboard.event.cta') }}</th>
                            <th>{{ trans('dashboard.event.scheduled_at') }}</th>
                            <th>État</th>
                            <th class="w-1"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($scheduledPosts->count() === 0)
                            <tr>
                                <td colspan="5"
                                    class="text-center">{{ trans('dashboard.no_scheduled_posts_found') }}</td>
                            </tr>
                        @else
                            @foreach($scheduledPosts as $scheduledPost)
                                @if(blank($scheduledPost->topic_type ?? ''))
                                    @continue
                                @endif
                                <tr>
                                    <td>{{ Str::ucfirst(Str::lower(($scheduledPost->topic_type ?? '---'))) }}</td>
                                    <td>{{ substr($scheduledPost->summary ?? '---', 0, 50) }}</td>
                                    <td>{{ Str::ucfirst(Str::lower(Str::replace('_', ' ', $scheduledPost->action_type ?? 'LEARN_MORE'))) }}</td>
                                    <td>{{ $scheduledPost->scheduled_at?->setTimezone(session('timezone', 'UTC'))->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="flex-nowrap">
                                            @switch(Str::lower($scheduledPost->state ?? '---'))
                                                @case('rejected')
                                                    <span class="badge bg-danger text-danger-fg cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ Str::ucfirst(Str::lower($scheduledPost->reason ?? '---'))  }}">{{ trans('global.rejected') }}</span>
                                                    @break
                                                @case('unspecified')
                                                    <span class="badge bg-orange text-orange-fg">{{ trans('global.pending') }}</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-green text-green-fg">{{ trans('global.posted') }}</span>
                                            @endswitch
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            @if(! blank($scheduledPost->online_id))
                                                @php($onlineId = explode('/', $scheduledPost->online_id))
                                                <a href="{{ route('dashboard.posts.view', ['accountId' => $onlineId[1], 'locationId' => $onlineId[3], 'postId' => $onlineId[5]]) }}" class="btn btn-sm btn-outline-green" title="{{ trans('global.view') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path></svg>
                                                </a>
                                            @endif
                                            @if(in_array(Str::lower($scheduledPost->state), ['unspecified', 'rejected']))
                                                <a href="{{ route('dashboard.scheduled.posts.edit', ['type' => Str::lower($scheduledPost->topic_type ?? ScheduledPost::STANDARD_TYPE), 'id' => $scheduledPost->id]) }}" class="btn btn-sm btn-default" title="{{ trans('global.edit') }}">
                                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-pencil"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                                </a>
                                            @endif
                                            <form action="{{ route('dashboard.scheduled.posts.delete', ['id' => $scheduledPost->id]) }}"
                                                  method="POST" style="display: inline;">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon btn-danger"
                                                        title="Delete"
                                                        onclick="return confirm('{{ trans('global.confirm_delete_scheduled_post') }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                         class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0"/>
                                                        <path d="M10 11l0 6"/>
                                                        <path d="M14 11l0 6"/>
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                @if ($scheduledPosts->count() > 0)
                    <div class="card-footer d-flex align-items-center">
                        {!! $scheduledPosts->links('partials.dashboard.paginator') !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection