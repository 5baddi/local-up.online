@extends('dashboard.posts.scheduled.partials.edit')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('form')
    <div class="mb-3">
        <label class="form-label">Titre</label>
        <input name="event_title" class="form-control @if ($errors->has('event_title')) is-invalid @endif" value="{{ old('event_title') }}" placeholder="Event title"/>
        @if ($errors->has('event_title'))
            <div class="invalid-feedback">{{ $errors->first('event_title') }}</div>
        @endif
    </div>
    <div class="row mb-3">
        <div class="col-8">
            <label class="form-label">Date de début</label>
            <input type="date" name="event_start_date" class="form-control @if ($errors->has('event_start_date')) is-invalid @endif" value="{{ old('event_start_date') }}"/>
            @if ($errors->has('event_start_date'))
                <div class="invalid-feedback">{{ $errors->first('event_start_date') }}</div>
            @endif
        </div>
        <div class="col-4">
            <label class="form-label">Heure de début</label>
            <input type="time" name="event_start_time" class="form-control @if ($errors->has('event_start_time')) is-invalid @endif" value="{{ old('event_start_time') }}"/>
            @if ($errors->has('event_start_time'))
                <div class="invalid-feedback">{{ $errors->first('event_start_time') }}</div>
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-8">
            <label class="form-label">Date de fin</label>
            <input type="date" name="event_end_date" class="form-control @if ($errors->has('event_end_date')) is-invalid @endif" value="{{ old('event_end_date') }}"/>
            @if ($errors->has('event_end_date'))
                <div class="invalid-feedback">{{ $errors->first('event_end_date') }}</div>
            @endif
        </div>
        <div class="col-4">
            <label class="form-label">Heure de fin</label>
            <input type="time" name="event_end_time" class="form-control @if ($errors->has('event_end_time')) is-invalid @endif" value="{{ old('event_end_time') }}"/>
            @if ($errors->has('event_end_time'))
                <div class="invalid-feedback">{{ $errors->first('event_end_time') }}</div>
            @endif
        </div>
    </div>
@endsection