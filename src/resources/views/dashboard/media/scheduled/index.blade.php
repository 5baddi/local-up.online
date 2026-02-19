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
                    <h3 class="card-title">{{ trans('global.scheduled_media') }}</h3>
                    <div class="ms-auto">
                        <a href="{{ route('dashboard.media.new') }}" class="btn btn-clnkgo btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                <path d="M7 9l5 -5l5 5" />
                                <path d="M12 4l0 12" />
                            </svg>
                            &nbsp;{{ trans('global.upload_new_media') }}
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-mobile-md card-table">
                        <thead>
                            <tr>
                                <th>{{ trans('global.frequency') }}</th>
                                <th>{{ trans('global.media') }}</th>
                                <th>{{ trans('dashboard.event.scheduled_at') }}</th>
                                <th>État</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($scheduledMedia->count() === 0)
                                <tr>
                                    <td colspan="5" class="text-center">{{ trans('global.no_scheduled_media_found') }}
                                    </td>
                                </tr>
                            @else
                                @foreach ($scheduledMedia as $media)
                                    <tr>
                                        <td>{{ trans(sprintf('global.%s', $media->scheduled_frequency ?? 'instantly')) }}</td>
                                        <td>
                                            <div class="avatar-list avatar-list-stacked">
                                                @php($files = array_slice($media->files ?? [], 0, 5))
                                                @foreach ($files as $file)
                                                @if(($file['type'] ?? '') !== 'photo')
                                                @continue
                                                @endif
                                                <span class="avatar avatar-s rounded" style="background-image: url({{ asset($file['path'] ?? '#') }})"></span>
                                                @endforeach
                                                @php($moreImagesCount = sizeof($media->files ?? []) - sizeof($files))
                                                @if($moreImagesCount > 0)
                                                <span class="avatar avatar-s rounded">+{{ $moreImagesCount }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $media->scheduled_at?->setTimezone(session('timezone', 'UTC'))->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="flex-nowrap">
                                                @switch(Str::lower($media->state ?? '---'))
                                                    @case('rejected')
                                                        <span class="badge bg-danger text-danger-fg cursor-pointer"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ Str::ucfirst(Str::lower($media->reason ?? '---')) }}">{{ trans('global.rejected') }}</span>
                                                    @break

                                                    @case('unspecified')
                                                        <span
                                                            class="badge bg-orange text-orange-fg">{{ trans('global.pending') }}</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="badge bg-green text-green-fg">{{ trans('global.posted') }}</span>
                                                @endswitch
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                @if (in_array(Str::lower($media->state), ['unspecified', 'rejected']))
                                                    <a href="{{ route('dashboard.scheduled.media.edit', ['id' => $media->id]) }}"
                                                        class="btn btn-sm btn-default" title="{{ trans('global.edit') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                            <path d="M13.5 6.5l4 4" />
                                                        </svg>
                                                    </a>
                                                @endif
                                                <form
                                                    action="{{ route('dashboard.scheduled.media.delete', ['id' => $media->id]) }}"
                                                    method="POST" style="display: inline;">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-danger"
                                                        title="Delete"
                                                        onclick="return confirm('{{ trans('global.confirm_delete_scheduled_media') }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M4 7l16 0" />
                                                            <path d="M10 11l0 6" />
                                                            <path d="M14 11l0 6" />
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
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
                @if ($scheduledMedia->count() > 0)
                    <div class="card-footer d-flex align-items-center">
                        {!! $scheduledMedia->links('partials.dashboard.paginator') !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
