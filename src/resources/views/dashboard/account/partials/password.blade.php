<div class="row">
    <p class="text-muted">Change or set a new password</p>
    @if ($user->hasPassword())
    <div class="col-4">
        <label class="form-label">Current password</label>
        <input type="password" name="current_password" class="form-control @if ($errors->has('current_password')) is-invalid @endif" placeholder="Current password"/>
        @if ($errors->has('current_password'))
        <div class="invalid-feedback">{{ $errors->first('current_password') }}</div>
        @endif
    </div>
    @endif
    <div class="col-4">
        <label class="form-label">New password</label>
        <input type="password" name="password" class="form-control @if ($errors->has('password')) is-invalid @endif" placeholder="New password"/>
        @if ($errors->has('password'))
        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
        @endif
    </div>
    <div class="col-4">
        <label class="form-label">Confirm new password</label>
        <input type="password" name="confirm_password" class="form-control @if ($errors->has('confirm_password')) is-invalid @endif" placeholder="Confirm new password"/>
        @if ($errors->has('confirm_password'))
        <div class="invalid-feedback">{{ $errors->first('confirm_password') }}</div>
        @endif
    </div>
</div>