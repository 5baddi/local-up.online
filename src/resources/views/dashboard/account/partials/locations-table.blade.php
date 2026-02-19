@php use Illuminate\Support\Str; @endphp
@foreach ($accountLocations as $accountLocation)
    @php($isMainLocation =  Str::endsWith($accountLocation['name'], $user->googleCredentials?->main_location_id))
    <tr>
        <td>{{ $accountLocation['title'] }}</td>
        <td>{{ $accountLocation['profile']['description'] ?? '---' }}</td>
        <td>
            <a href="{{ $isMainLocation ? '#' : route('dashboard.account.locations.main', ['name' => $accountLocation['name']]) }}"
               class="btn btn-green btn-sm {{ $isMainLocation ? 'disabled' : '' }}"
               title="Définir comme emplacement principal"
               @if($isMainLocation)onclick="return confirm('Voulez-vous vraiment définir comme emplacement principal ?')"@endif>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round"
                     class="icon icon-tabler icons-tabler-outline icon-tabler-checks">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M7 12l5 5l10 -10"/>
                    <path d="M2 12l5 5m5 -5l5 -5"/>
                </svg>
            </a>
        </td>
    </tr>
@endforeach