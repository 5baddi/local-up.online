<?php

namespace App\Console\Commands;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use BADDIServices\ClnkGO\AppLogger;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Collection;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Services\UserService;
use BADDIServices\ClnkGO\Domains\GoogleService;
use BADDIServices\ClnkGO\Models\ScheduledPostMedia;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;
use BADDIServices\ClnkGO\Domains\GoogleMyBusinessService;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleBusinessLocalPostObjectValue;

class AutoPostScheduledPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-post:scheduled-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto post scheduled posts';

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
        $this->info("Start posting scheduled posts");
        $startTime = microtime(true);

        try {
            ScheduledPost::query()
                ->where(ScheduledPost::SCHEDULED_AT_COLUMN, '<=', Carbon::now()->format('Y-m-d H:i:s'))
                ->where(ScheduledPost::STATE_COLUMN, ScheduledPost::UNSPECIFIED_STATE)
                ->orderBy(ScheduledPost::USER_ID_COLUMN)
                ->chunkById(10, function (Collection $scheduledPosts) {
                    $scheduledPosts->each(function (ScheduledPost $scheduledPost) {
                        try {
                            $user = $this->userService->findById($scheduledPost->getAttribute(ScheduledPost::USER_ID_COLUMN));

                            if (
                                ! $user instanceof User
                                || ! $user->googleCredentials instanceof UserGoogleCredentials
                                || empty($user->googleCredentials?->getAccessToken())
                                || empty($scheduledPost->getAttribute(ScheduledPost::ACCOUNT_ID_COLUMN))
                                || empty($scheduledPost->getAttribute(ScheduledPost::LOCATION_ID_COLUMN))
                            ) {
                                return true;
                            }

                            $this->googleService->refreshAccessToken($user->googleCredentials);
                            $user->load(['googleCredentials']);

                            $googleMyBusinessService = new GoogleMyBusinessService(
                                $user->googleCredentials->getAccessToken(),
                                $scheduledPost->getAttribute(ScheduledPost::ACCOUNT_ID_COLUMN),
                                $scheduledPost->getAttribute(ScheduledPost::LOCATION_ID_COLUMN)
                            );

                            $files = ScheduledPostMedia::query()
                                ->where([ScheduledPostMedia::SCHEDULED_POST_ID_COLUMN => $scheduledPost->getId()])
                                ->get();

                            $media = array_map(function ($file) {
                                if (
                                    ! Arr::has($file ?? [], [ScheduledPostMedia::TYPE_COLUMN, ScheduledPostMedia::PATH_COLUMN])
                                    || ! File::exists(public_path($file[ScheduledPostMedia::PATH_COLUMN]))
                                ) {
                                    return null;
                                }

                                return [
                                    'mediaFormat'   => ScheduledPostMedia::TYPES[$file[ScheduledPostMedia::TYPE_COLUMN]],
                                    'sourceUrl'     => URL::asset($file[ScheduledPostMedia::PATH_COLUMN]),
                                ];
                            }, $files->toArray());

                            $localPost = GoogleBusinessLocalPostObjectValue::fromArray(
                                array_merge(
                                    $scheduledPost->toArray(),
                                    [
                                        'media' => array_filter($media),
                                    ]
                                )
                            );

                            $googleLocalPost = $googleMyBusinessService->createScheduledPost($localPost);

                            $scheduledPost->update([
                                ScheduledPost::STATE_COLUMN     => strtolower(
                                    $googleLocalPost[ScheduledPost::STATE_COLUMN] ?? ScheduledPost::REJECTED_STATE
                                ),
                                ScheduledPost::ONLINE_ID_COLUMN => $googleLocalPost['name'] ?? null,
                                ScheduledPost::REASON_COLUMN    => null,
                            ]);

                            $files->each(function(ScheduledPostMedia $file) {
                                $path = $file->getAttribute(ScheduledPostMedia::PATH_COLUMN);
                                if (empty($path)) {
                                    return true;
                                }

                                File::delete(public_path($path));

                                $file->forceDelete();

                                return true;
                            });
                        } catch (Throwable $e) {
                            $scheduledPost->update([
                                ScheduledPost::STATE_COLUMN     => ScheduledPost::REJECTED_STATE,
                                ScheduledPost::REASON_COLUMN    => $e->getMessage(),
                            ]);
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

            $this->error(sprintf("Error while posting scheduled posts: %s", $e->getMessage()));

            return 0;
        }

        $this->info("Done posting scheduled posts");

        return 0;
    }
}
