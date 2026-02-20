@php use Carbon\Carbon; @endphp
@foreach(($media['mediaItems'] ?? []) as $item)
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm">
            <div class="d-block" style="max-height: 250px;">
                <img
                        style="max-height: 100%; object-fit: cover;"
                        src="{{ $item['googleUrl'] ?? '#' }}"
                        class="card-img-top">
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <div class="text-secondary">{{ Carbon::parse($item['createTime'] ?? now())->diffForHumans() }}</div>
                    </div>
                    <div class="ms-auto">
                        <form action="{{ route('dashboard.media.delete', ['id' => last(explode('/', $item['name'] ?? ''))]) }}"
                              method="POST" style="display: inline-block;">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Delete"
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