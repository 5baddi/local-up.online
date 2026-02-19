@extends('dashboard.posts.scheduled.partials.edit')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('form')
    <div class="row mb-3">
        <div class="col-4">
            <label class="form-label">Type</label>
            <select name="alert_type" class="form-select @if ($errors->has('alert_type')) is-invalid @endif">
                <option @if (($scheduledPost?->alert_type ?? old('alert_type')) === 'unspecified' || empty(($scheduledPost?->alert_type ?? old('alert_type')))) selected @endif value="unspecified">N'est spécifiée</option>
                <option @if (($scheduledPost?->alert_type ?? old('alert_type')) === 'covid_19') selected @endif value="covid_19">Coronavirus 2019</option>
            </select>
            @if ($errors->has('alert_type'))
                <div class="invalid-feedback">{{ $errors->first('alert_type') }}</div>
            @endif
        </div>
    </div>
@endsection