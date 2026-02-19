<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use BADDIServices\ClnkGO\Models\AccountLocation;
use BADDIServices\ClnkGO\Domains\GoogleMyBusinessService;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleCredentialsObjectValue;

class PullAccountLocations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private GoogleMyBusinessService $googleMyBusinessService;

    public function __construct(
        private readonly string $userId,
        private readonly GoogleCredentialsObjectValue $googleCredentials
    ) {}

    public function handle(): void
    {
        $this->googleMyBusinessService = new GoogleMyBusinessService($this->googleCredentials->getAccessToken());

        do {
            $response = $this->googleMyBusinessService->getBusinessAccounts(
                $response['nextPageToken'] ?? null
            );

            foreach ($response['accounts'] ?? [] as $account) {
                $this->pullAccountLocations(Str::remove('accounts/', $account['name'] ?? ''));
            }
        } while (! empty($response['nextPageToken']));
    }

    private function pullAccountLocations(string $accountId): void
    {
        do {
            $this->googleMyBusinessService->setAccountId($accountId);

            $response = $this->googleMyBusinessService->getBusinessAccountLocations(
                $response['nextPageToken'] ?? null
            );

            foreach ($response['locations'] ?? [] as $location) {
                $locationIds = explode('/', $location['name'] ?? '');
                $locationId = end($locationIds);
                $locationId = $locationId !== false ? $locationId : '';

                AccountLocation::query()
                    ->updateOrCreate(
                        [
                            AccountLocation::USER_ID_COLUMN     => $this->userId,
                            AccountLocation::ACCOUNT_ID_COLUMN  => $this->googleCredentials->getAccountId(),
                            AccountLocation::LOCATION_ID_COLUMN => $locationId,
                        ],
                        [
                            AccountLocation::USER_ID_COLUMN     => $this->userId,
                            AccountLocation::ACCOUNT_ID_COLUMN  => $this->googleCredentials->getAccountId(),
                            AccountLocation::LOCATION_ID_COLUMN => $locationId,
                            AccountLocation::TITLE_COLUMN       => $location['title'],
                            AccountLocation::DESCRIPTION_COLUMN => $location['profile']['description'] ?? null,
                        ]
                    );
            }
        } while (! empty($response['nextPageToken']));
    }
}
