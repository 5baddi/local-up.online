<?php

namespace App\Console\Commands;

use Throwable;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\PullAccountLocations;
use BADDIServices\ClnkGO\AppLogger;
use Illuminate\Database\Eloquent\Collection;
use BADDIServices\ClnkGO\Domains\GoogleService;
use BADDIServices\ClnkGO\Models\AccountLocation;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleCredentialsObjectValue;

class RefreshGoogleAccessTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:refresh-google-access-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh google access token for authenticated users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(private readonly GoogleService $googleService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Start refreshing users google access tokens");
        $startTime = microtime(true);

        try {
            UserGoogleCredentials::query()
                ->whereNotNull(UserGoogleCredentials::REFRESH_TOKEN_COLUMN)
                ->where(UserGoogleCredentials::IS_EXPIRED_COLUMN, '=', false)
                ->Where(
                    UserGoogleCredentials::CREATED_COLUMN,
                    '<=',
                    Carbon::now()->subHour()->getTimestamp()
                )
                ->chunkById(10, function (Collection $usersGoogleCredentials) {
                    $usersGoogleCredentials->each(function (UserGoogleCredentials $userGoogleCredentials) {
                        $this->googleService->refreshAccessToken($userGoogleCredentials);

                        $accountLocations = AccountLocation::query()
                            ->where(AccountLocation::USER_ID_COLUMN, $userGoogleCredentials->getUserId())
                            ->get()
                            ->toArray();

                        if (sizeof($accountLocations) > 0) {
                            return true;
                        }

                        PullAccountLocations::dispatch(
                            $userGoogleCredentials->getUserId(),
                            GoogleCredentialsObjectValue::fromArray($userGoogleCredentials->toArray())
                        );

                        return true;
                    });
                });
        } catch (Throwable $e) {
            AppLogger::error(
                $e,
                sprintf('command:%s', $this->signature),
                ['execution_time' => (microtime(true) - $startTime)]
            );

            $this->error(sprintf("Error while refreshing users google access tokens: %s", $e->getMessage()));

            return 0;
        }

        $this->info("Done refreshing users google access tokens");

        return 0;
    }
}
