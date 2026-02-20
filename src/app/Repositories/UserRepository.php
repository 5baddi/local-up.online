<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Repositories;

use Carbon\Carbon;
use App\Models\User;
use BADDIServices\ClnkGO\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use BADDIServices\ClnkGO\Http\Filters\QueryFilter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

class UserRepository
{
    public function paginate(QueryFilter $queryFilter): LengthAwarePaginator
    {
        return User::query()
            ->with([])
            ->filter($queryFilter)
            ->paginate(App::PAGINATION_LIMIT, ['*'], "page", $queryFilter->getPage());
    }
    
    public function paginateWithRelations(?int $page = null): LengthAwarePaginator
    {
        return User::query()
                ->with(['store'])
                ->where(User::ID_COLUMN, '!=', Auth::id())
                ->paginate(10, ['*'], 'ap', $page);
    }
    
    public function findById(string $id): ?User
    {
        return User::query()
                    ->with(['googleCredentials'])
                    ->find($id);
    }
    
    public function findByEmail(string $email): ?User
    {
        return User::query()
                    ->with(['googleCredentials'])
                    ->where([
                        User::EMAIL_COLUMN => strtolower($email)
                    ])
                    ->first();
    }
    
    public function findByToken(string $token): ?User
    {
        return User::query()
            ->where(User::CONFIRMATION_TOKEN_COLUMN, $token)
            ->first();
    }
    
    public function confirmEmail(string $id): bool
    {
        return User::query()
            ->where(User::ID_COLUMN, $id)
            ->update([
                User::VERIFIED_AT_COLUMN        => Carbon::now(),
                User::CONFIRMATION_TOKEN_COLUMN => null,
            ]) === 1;
    }
    
    public function findByCustomerId(int $customerId): ?User
    {
        return User::query()
                    ->with(['store'])
                    ->where([
                        User::CUSTOMER_ID_COLUMN => $customerId
                    ])
                    ->first();
    }

    public function create(array $attributes): User
    {
        Arr::set($attributes, User::EMAIL_COLUMN, strtolower($attributes[User::EMAIL_COLUMN]));
        
        return User::query()
                    ->create($attributes);
    }

    /**
     * @return User|false
     */
    public function update(User $user, array $attributes)
    {
        $userUpdated = User::query()
                            ->where(
                                [
                                    User::ID_COLUMN => $user->id
                                ]
                            )
                            ->update($attributes);

        if ($userUpdated) {
            return $user->refresh();
        }

        return false;
    }
    
    public function delete(string $id): bool
    {
        return (bool) User::query()
                    ->find($id)
                    ->delete();
    }

    public function countByPeriod(Carbon $startDate, carbon $endDate, array $conditions = []): int
    {
        return User::query()
                    ->whereDate(
                        User::CREATED_AT,
                        '>=',
                        $startDate
                    )
                    ->whereDate(
                        User::CREATED_AT,
                        '<=',
                        $endDate
                    )
                    ->where($conditions)
                    ->count();
    }

    public function generateResetPasswordToken(string $email): ?string
    {
        DB::table('password_resets')
            ->where('email', $email)
            ->delete();

        DB::table('password_resets')
            ->insert([
                'email'         => $email,
                'token'         => Str::random(60),
                'created_at'    => Carbon::now()
            ]);

        $tokenData = DB::table('password_resets')
            ->where('email', $email)
            ->select('token')
            ->first();

        return $tokenData->token ?? null;
    }

    public function verifyResetPasswordToken(string $token): ?User
    {
        $token = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        if ($token === null || $token->email === null) {
            return null;
        }

        return $this->findByEmail($token->email);
    }

    public function removeResetPasswordToken(string $token): bool
    {
        return DB::table('password_resets')
            ->where('token', $token)
            ->delete() > 0;
    }

    public function saveGoogleCredentials(string $userId, array $credentials): Builder|Model
    {
        return UserGoogleCredentials::query()
            ->updateOrCreate(
                [UserGoogleCredentials::USER_ID_COLUMN => $userId],
                array_merge($credentials, [UserGoogleCredentials::USER_ID_COLUMN => $userId])
            );
    }

    public function markGoogleCredentialsAsExpired(string $userId, bool $expired = true): bool
    {
        return (bool) UserGoogleCredentials::query()
            ->where(UserGoogleCredentials::USER_ID_COLUMN, $userId)
            ->update([UserGoogleCredentials::IS_EXPIRED_COLUMN => $expired]);
    }

    public function deleteGoogleCredentials(string $userId): void
    {
        UserGoogleCredentials::query()
            ->where([UserGoogleCredentials::USER_ID_COLUMN => $userId])
            ->delete();
    }
}