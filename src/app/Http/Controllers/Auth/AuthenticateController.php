<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Auth;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use BADDIServices\ClnkGO\AppLogger;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use BADDIServices\ClnkGO\Services\UserService;
use BADDIServices\ClnkGO\Http\Requests\SignInRequest;

class AuthenticateController extends Controller
{
    /** @var UserService */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(SignInRequest $request)
    {
        try {
            $user = $this->userService->findByEmail($request->input(User::EMAIL_COLUMN));
            if (! $user) {
                return redirect()
                    ->route('signin')
                    ->withInput($request->only([User::EMAIL_COLUMN]))
                    ->with('error', trans('auth.no_account_registered'));
            }

            if (! $user->isEmailConfirmed()) {
                return redirect()
                    ->route('signin')
                    ->withInput($request->only([User::EMAIL_COLUMN]))
                    ->with('error', trans('auth.confirm_your_email'));
            }

            if (! $this->userService->verifyPassword($user, $request->input(User::PASSWORD_COLUMN))) {
                return redirect()
                    ->route('signin')
                    ->withInput($request->only([User::EMAIL_COLUMN]))
                    ->with('error', trans('auth.incorrect_credentials'));
            }
            
            if ($user->isBanned()) {
                return redirect()
                    ->route('signin')
                    ->with('error', trans('auth.account_banned'));
            }

            $authenticateUser = Auth::attempt(['email' => $user->email, 'password' => $request->input(User::PASSWORD_COLUMN)]);
            if (! $authenticateUser) {
                return redirect()
                    ->route('signin')
                    ->withInput($request->only([User::EMAIL_COLUMN]))
                    ->with('error', trans('auth.error_while_authentication'));
            }

            $this->userService->update($user, [
                User::LAST_LOGIN_COLUMN    =>  Carbon::now()
            ]);

            Session::put('timezone', $request->input('timezone', 'UTC'));
            
            return redirect()
                ->route('dashboard')
                ->with('success', trans('dashboard.welcome_back', ['name' => strtoupper($user->first_name)]));
        } catch (Throwable $e) {
            AppLogger::error($e, 'auth:signin', ['playload' => $request->all()]);

            return redirect()
                ->route('signin')
                ->withInput($request->only([User::EMAIL_COLUMN]))
                ->with('error', trans('global.server_error'));
        }
    }
}