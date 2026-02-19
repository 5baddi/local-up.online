  <div class="row justify-content-center">
    <div class="col-12">
      @if ($unreadNotifications->count() > 0)
      @foreach ($unreadNotifications as $notification)
      <div class="card mb-2">
        <div class="card-header">
          <h5 class="card-title">{{ $notification->data['subject'] }}</h5>
        </div>
        {{-- <div class="card-body">
          <div class="divide-y">
              <div>
                  <div class="row">
                      <div class="col">
                          <h5></h5>
                          <div class="text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                      </div>
                      
                  </div>
              </div>
          </div>
        </div> --}}
        <div class="card-footer">
          @if (isset($notification->data['link']))
          <div class="col-auto align-self-center">
              <a href="{{ url($notification->data['link']['url'] ?? '#') . '?' . http_build_query(['notification' => $notification->id]) }}" class="btn btn-dark w-100">
                {{ $notification->data['link']['label'] }}
              </a>
          </div>
          @endif
        </div>
      </div>
      @endforeach
      @endif
    </div>
  </div>