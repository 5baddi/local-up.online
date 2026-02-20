<?php

namespace App\Console\Commands;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use BADDIServices\ClnkGO\AppLogger;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Collection;
use BADDIServices\ClnkGO\Services\UserService;
use BADDIServices\ClnkGO\Domains\GoogleService;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;
use BADDIServices\ClnkGO\Domains\GoogleMyBusinessService;

class AutoPostScheduledMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-post:scheduled-media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto post scheduled media';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private readonly UserService $userService,
        private readonly GoogleService $googleService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Start posting scheduled media");
        $startTime = microtime(true);

        try {
            ScheduledMedia::query()
                ->where(ScheduledMedia::SCHEDULED_AT_COLUMN, '<=', Carbon::now()->format('Y-m-d H:i:s'))
                ->where(ScheduledMedia::STATE_COLUMN, ScheduledMedia::UNSPECIFIED_STATE)
                ->orderBy(ScheduledMedia::USER_ID_COLUMN)
                ->chunkById(10, function (Collection $scheduledMedias) {
                    $scheduledMedias->each(function (ScheduledMedia $scheduledMedia) {
                        try {
                            DB::beginTransaction();

                            $user = $this->userService->findById(
                                $scheduledMedia->getAttribute(ScheduledMedia::USER_ID_COLUMN)
                            );

                            if (
                                ! $user instanceof User
                                || ! $user->googleCredentials instanceof UserGoogleCredentials
                                || empty($user->googleCredentials?->getAccessToken())
                                || empty($scheduledMedia->getAttribute(ScheduledMedia::ACCOUNT_ID_COLUMN))
                                || empty($scheduledMedia->getAttribute(ScheduledMedia::LOCATION_ID_COLUMN))
                            ) {
                                return true;
                            }

                            $this->googleService->refreshAccessToken($user->googleCredentials);
                            $user->load(['googleCredentials']);

                            $googleMyBusinessService = new GoogleMyBusinessService(
                                $user->googleCredentials->getAccessToken(),
                                $scheduledMedia->getAttribute(ScheduledMedia::ACCOUNT_ID_COLUMN),
                                $scheduledMedia->getAttribute(ScheduledMedia::LOCATION_ID_COLUMN)
                            );

                            $files = $scheduledMedia->getAttribute(ScheduledMedia::FILES_COLUMN);
                            if (empty($files ?? [])) {
                                $scheduledMedia->forceDelete();

                                DB::commit();

                                return true;
                            }

                            $file = Arr::first($files, null, []);
                            if (! Arr::has($file, [ScheduledMedia::PATH, ScheduledMedia::TYPE]) || ! File::exists(public_path($file[ScheduledMedia::PATH]))) {
                                Arr::forget($files, array_key_first($files));

                                if (sizeof($files) === 0) {
                                    $scheduledMedia->delete();

                                    DB::commit();

                                    return true;
                                }

                                $scheduledMedia->update([
                                    ScheduledMedia::FILES_COLUMN => array_values($files),
                                ]);

                                DB::commit();

                                return true;
                            }

                            $googleMyBusinessService->createBusinessLocationMedia([
                                'locationAssociation'   => [
                                    'category'          => 'ADDITIONAL',
                                ],
                                'mediaFormat'           => ScheduledMedia::TYPES[$file[ScheduledMedia::TYPE]] ?? ScheduledMedia::TYPES[ScheduledMedia::PHOTO_TYPE],
                                'sourceUrl'             => URL::asset($file[ScheduledMedia::PATH]),
                            ]);

                            $scheduledAt = $scheduledMedia->getAttribute(ScheduledMedia::SCHEDULED_AT_COLUMN);

                            switch ($scheduledMedia->getAttribute(ScheduledMedia::SCHEDULED_FREQUENCY_COLUMN)) {
                                case ScheduledMedia::DAILY_SCHEDULED_FREQUENCY:
                                    $scheduledAt = $scheduledAt?->addDay();
        
                                    break;
                                case ScheduledMedia::EVERY_3_DAYS_SCHEDULED_FREQUENCY:
                                    $scheduledAt = $scheduledAt?->addDays(3);
        
                                    break;
                                case ScheduledMedia::WEEKLY_SCHEDULED_FREQUENCY:
                                    $scheduledAt = $scheduledAt?->addWeek();
        
                                    break;
                            }

                            Arr::forget($files, array_key_first($files));

                            if (sizeof($files) === 0) {
                                $scheduledMedia->delete();

                                DB::commit();

                                return true;
                            }

                            $scheduledMedia->update([
                                ScheduledMedia::FILES_COLUMN        => array_values($files),
                                ScheduledMedia::SCHEDULED_AT_COLUMN => $scheduledAt,
                            ]);

                            if (sizeof($files) === 0) {
                                $scheduledMedia->delete();
                            }

                            DB::commit();
                        } catch (Throwable $e) {
                            DB::rollBack();

                            $scheduledMedia->update([
                                ScheduledMedia::STATE_COLUMN     => ScheduledMedia::REJECTED_STATE,
                                ScheduledMedia::REASON_COLUMN    => $e->getMessage(),
                            ]);

                            DB::commit();
                        }

                        return true;
                    });
                });
        } catch (Throwable $e) {
            AppLogger::error(
                $e,
                sprintf('command:%s', $this->signature),
                ['execution_time' => (microtime(true) - $startTime)]
            );

            $this->error(sprintf("Error while posting scheduled media: %s", $e->getMessage()));

            return 0;
        }

        $this->info("Done posting scheduled media");

        return 0;
    }
}
