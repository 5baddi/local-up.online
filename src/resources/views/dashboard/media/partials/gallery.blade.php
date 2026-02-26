@php use Carbon\Carbon; @endphp
@foreach(($media['mediaItems'] ?? []) as $item)
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm media-card">
            <div class="card-img-top-wrapper">
                <img
                    src="{{ $item['googleUrl'] ?? '#' }}"
                    class="card-img-top"
                    alt=""
                    style="height: 220px; object-fit: cover;">
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="text-muted small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
                        &nbsp;{{ Carbon::parse($item['createTime'] ?? now())->diffForHumans() }}
                    </div>
                    <div class="ms-auto">
                        <form action="{{ route('dashboard.media.delete', ['id' => last(explode('/', $item['name'] ?? ''))]) }}"
                              method="POST" style="display: inline-block;">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"
                                    onclick="return confirm('Voulez-vous vraiment supprimer cet media ?')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round"
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
                </div>
            </div>
        </div>
    </div>
@endforeach
