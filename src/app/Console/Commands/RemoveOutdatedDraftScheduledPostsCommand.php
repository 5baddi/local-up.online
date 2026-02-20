<?php

namespace App\Console\Commands;

use Throwable;
use Carbon\Carbon;
use Illuminate\Console\Command;
use BADDIServices\ClnkGO\AppLogger;
use Illuminate\Database\Eloquent\Collection;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Models\ScheduledPostMedia;

class RemoveOutdatedDraftScheduledPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:outdated-draft-scheduled-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove outdated draft scheduled posts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Remove outdated draft scheduled posts");
        $startTime = microtime(true);

        try {
            ScheduledPost::query()
                ->where(ScheduledPost::CREATED_AT, '=>', Carbon::now()->subWeek()->format('Y-m-d H:i:s'))
                ->whereNull(ScheduledPost::TOPIC_TYPE_COLUMN)
                ->orderBy(ScheduledPost::USER_ID_COLUMN)
                ->chunkById(10, function (Collection $scheduledPosts) {
                    $scheduledPosts->each(function (ScheduledPost $scheduledPost) {
                        try {
                            ScheduledPostMedia::query()
                                ->where(ScheduledPostMedia::SCHEDULED_POST_ID_COLUMN, $scheduledPost->getId())
                                ->forceDelete();

                            $scheduledPost->forceDelete();
                        } catch (Throwable) {}
                    });
                });
        } catch (Throwable $e) {
            AppLogger::error(
                $e,
                sprintf('command:%s', $this->signature),
                ['execution_time' => (microtime(true) - $startTime)]
            );

            $this->error(sprintf("Error while removing outdated draft scheduled posts: %s", $e->getMessage()));

            return 0;
        }

        $this->info("Done removing outdated draft scheduled posts");

        return 0;
    }
}