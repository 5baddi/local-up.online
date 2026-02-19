<div class="row">
    <p class="text-muted">Setup your account, edit profile details</p>
    <div class="col-6">
        <label class="form-label">First name</label>
        <input type="text" name="first_name" class="form-control @if ($errors->has('first_name')) is-invalid @endif" value="{{ old('first_name') ?? ucfirst($user->first_name)  }}" placeholder="Your first name" autofocus/>
        @if ($errors->has('first_name'))
        <div class="invalid-feedback">{{ $errors->first('first_name') }}</div>
        @endif
    </div>
    <div class="col-6">
        <label class="form-label">First name</label>
        <input type="text" name="last_name" class="form-control @if ($errors->has('last_name')) is-invalid @endif" value="{{ old('last_name') ?? ucfirst($user->last_name) }}" placeholder="Your last name"/>
        @if ($errors->has('last_name'))
        <div class="invalid-feedback">{{ $errors->first('last_name') }}</div>
        @endif
    </div>
</div>
<div class="row mt-4">
    <div class="col-6">
        <label class="form-label">E-mail</label>
        <input type="email" class="form-control" value="{{ $user->email }}" placeholder="E-mail" readonly/>
    </div>
    <div class="col-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control @if ($errors->has('phone')) is-invalid @endif" value="{{ old('phone') ?? $user->phone }}" placeholder="Your phone number"/>
        @if ($errors->has('phone'))
        <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
        @endif
    </div>
</div>