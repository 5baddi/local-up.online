@extends('dashboard.posts.scheduled.partials.edit')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('form')
    <div class="row mb-3">
        <div class="col-4">
            <label class="form-label">Code promo</label>
            <input name="offer_coupon_code" class="form-control @if ($errors->has('offer_coupon_code')) is-invalid @endif" value="{{ old('offer_coupon_code') }}" placeholder="Code coupon offre"/>
            @if ($errors->has('offer_coupon_code'))
                <div class="invalid-feedback">{{ $errors->first('offer_coupon_code') }}</div>
            @endif
        </div>
        <div class="col-8">
            <label class="form-label">Utiliser l’URL en ligne</label>
            <input name="offer_redeem_online_url" type="url" class="form-control @if ($errors->has('offer_redeem_online_url')) is-invalid @endif" value="{{ old('offer_redeem_online_url') }}" placeholder="URL de l’offre"/>
            @if ($errors->has('offer_redeem_online_url'))
                <div class="invalid-feedback">{{ $errors->first('offer_redeem_online_url') }}</div>
            @endif
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Conditions Générales&nbsp;<span class="form-label-description" id="offer-terms-length">0/1000</span></label>
        <textarea name="offer_terms_conditions" rows="3" maxlength="1000" onkeyup="calculateTextLength(event, '#offer-terms-length', '/1000')" class="form-control @if ($errors->has('offer_terms_conditions')) is-invalid @endif" placeholder="Conditions de l’offre">{{ old('offer_terms_conditions') }}</textarea>
        @if ($errors->has('offer_terms_conditions'))
            <div class="invalid-feedback">{{ $errors->first('offer_terms_conditions') }}</div>
        @endif
    </div>
@endsection