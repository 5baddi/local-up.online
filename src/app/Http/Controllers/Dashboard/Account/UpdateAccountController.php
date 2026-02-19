<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Rules\ValidateCurrentPassword;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class UpdateAccountController extends DashboardController
{    
    public function __invoke(Request $request)
    {
        try {
            if ($request->query('tab', 'settings') === 'password') {
                return $this->updateAccountPassword($request);
            }

            return $this->updateAccountInfo($request);
        } catch (Throwable $e){
            return redirect()->route('dashboard.account', ['tab' => $request->query('tab', 'settings')])
                ->with(
                    'alert', 
                    new Alert('An occurred error while saving account settings!')
                )
                ->withInput();
        }
    }

    private function updateAccountInfo(Request $request)
    {
        $validator = Validator::make(
            $request->input(),
            [
                User::FIRST_NAME_COLUMN    => 'required|string|min:1',
                User::LAST_NAME_COLUMN     => 'required|string|min:1',
                User::PHONE_COLUMN         => 'nullable|string|max:25',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('dashboard.account', ['tab' => $request->query('tab', 'settings')])
                ->withErrors($validator->errors())
                ->withInput();
        }

        $this->user = $this->userService->update($this->user, $request->input());
        Auth::setUser($this->user);

        return redirect()->route('dashboard.account', ['tab' => $request->query('tab', 'settings')])
            ->with(
                'alert', 
                new Alert('Account settings changed successfully', 'success')
            );
    }
    
    private function updateAccountPassword(Request $request)
    {
        $validator = Validator::make(
            $request->input(),
            [
                'current_password'         => [new ValidateCurrentPassword()],
                User::PASSWORD_COLUMN      => 'nullable|string|min:8|required_with:current_password|same:confirm_password',
                'confirm_password'         => 'nullable|string|min:8',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('dashboard.account', ['tab' => $request->query('tab', 'password')])
                ->withErrors($validator->errors())
                ->withInput();
        }

        if ($request->has('current_password') && ! $this->userService->verifyPassword($this->user, $request->input('current_password'))) {
            return redirect()->route('dashboard.account')
                ->with(
                    'alert', 
                    new Alert('Current passwrod not match your credential')
                )
                ->withInput();
        }

        $this->user = $this->userService->update($this->user, $request->input());
        Auth::setUser($this->user);

        return redirect()->route('dashboard.account', ['tab' => $request->query('tab', 'password')])
            ->with(
                'alert', 
                new Alert('Account password changed successfully', 'success')
            );
    }
}