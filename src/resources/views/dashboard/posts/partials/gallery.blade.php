@php use Illuminate\Support\Str; @endphp

@foreach(($posts['localPosts'] ?? []) as $post)
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm">
            <div class="d-block">
                @switch($post['media'][0]['mediaFormat'] ?? '---')
                    @case('VIDEO')
                        {{--                        TODO: implement view preview--}}
                        @break
                    @default
                        <img src="{{ $post['media'][0]['googleUrl'] ?? asset('assets/img/no-image.svg') }}" class="card-img-top">
                @endswitch
            </div>
            <div class="card-body">
                <div class="mb-2">
                    @switch($post['state'] ?? '---')
                        @case('PROCESSING')
                            <span class="badge bg-yellow text-yellow-fg">{{ Str::ucfirst(Str::lower($post['state'] ?? '---'))  }}</span>
                            @break
                        @case('LIVE')
                            <span class="badge bg-green text-green-fg">{{ Str::ucfirst(Str::lower($post['state'] ?? '---'))  }}</span>
                            @break
                        @default
                            <span class="badge bg-red text-red-fg">{{ Str::ucfirst(Str::lower($post['state'] ?? '---'))  }}</span>
                    @endswitch
                </div>
                {{ Str::length($post['summary'] ?? '---') > 255 ? (Str::substr($post['summary'] ?? '---', 0, 255) . '...') : ($post['summary'] ?? '---') }}
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center">
                    @php($onlineId = explode('/', $post['name']))
                    <a href="{{ route('dashboard.posts.view', ['accountId' => $onlineId[1], 'locationId' => $onlineId[3], 'postId' => $onlineId[5]]) }}" title="{{ trans('global.view') }}" class="btn btn-default">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                    </a>
                    <div></div>
                    <div class="ms-auto">
                        <a href="{{ $post['callToAction']['url'] ?? '#' }}" title="{{ Str::ucfirst(Str::lower(Str::replace('_', ' ', $post['callToAction']['actionType'] ?? 'LEARN_MORE'))) }}" target="_blank" class="btn btn-default">
                            {{ Str::ucfirst(Str::lower(Str::replace('_', ' ', $post['callToAction']['actionType'] ?? 'LEARN_MORE'))) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach