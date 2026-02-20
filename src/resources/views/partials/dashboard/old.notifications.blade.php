  <div class="row justify-content-center">
    <div class="col-8">
      @if ($unreadNotifications->count() > 0)
      <div class="card">
        <div class="card-body">
          <div class="divide-y">
              @foreach ($unreadNotifications as $notification)
              <div>
                  <div class="row">
                      <div class="col">
                          <div class="text-truncate">
                              {{ $notification->data['subject'] }}
                          </div>
                          <div class="text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                      </div>
                      <div class="col-auto align-self-center">
                          <a href="{{ route('dashboard.activity.read', ['notification' => $notification->id]) }}" class="btn btn-ghost-dark w-100">
                            Mark as read 
                          </a>
                      </div>
                      @if (isset($notification->data['link']))
                      <div class="col-auto align-self-center">
                          <a href="{{ $notification->data['link']['url'] ?? '#' }}" class="btn btn-ghost-dark w-100">
                            {{ $notification->data['link']['label'] }}
                          </a>
                      </div>
                      @endif
                  </div>
              </div>
              @endforeach
          </div>
        </div>
      </div>
      @endif
      
      @if ($markAsReadNotifications->count() > 0)
      <div class="card">
        <div class="card-body">
          <div class="divide-y">
              @foreach ($markAsReadNotifications as $notification)
              <div>
                  <div class="row">
                      <div class="col">
                          <div class="text-truncate">
                              {{ $notification->data['subject'] }}
                          </div>
                          <div class="text-muted">Mark as read {{ $notification->read_at->diffForHumans() }}</div>
                      </div>
                      @if (isset($notification->data['link']))
                      <div class="col-auto align-self-center">
                          <a href="{{ $notification->data['link']['url'] ?? '#' }}" class="btn btn-ghost-dark w-100">
                            {{ $notification->data['link']['label'] }}
                          </a>
                      </div>
                      @endif
                  </div>
              </div>
              @endforeach
          </div>
        </div>
      </div>
      @endif
    </div>

    @if ($unreadNotifications->count() > 0)
    <div class="col-8 mt-4">
      <div class="text-end">
          <div class="d-flex justify-content-end">
              <a href="{{ route('dashboard.activity.read.all') }}" class="btn btn-ghost-dark">
                Mark all as read
              </a>
          </div>
      </div>
    </div>
    @endif
  </div>