<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Bugsnag\BugsnagLaravel\OomBootstrapper;
use App\Console\Commands\AutoPostScheduledPostsCommand;
use App\Console\Commands\AutoPostScheduledMediaCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RefreshGoogleAccessTokenCommand;
use App\Console\Commands\RemoveOutdatedDraftScheduledPostsCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AutoPostScheduledPostsCommand::class,
        AutoPostScheduledMediaCommand::class,
        RemoveOutdatedDraftScheduledPostsCommand::class,
        RefreshGoogleAccessTokenCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->command('auto-post:scheduled-posts')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule
            ->command('auto-post:scheduled-media')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule
            ->command('remove:outdated-draft-scheduled-posts')
            ->dailyAt('00:00:00')
            ->withoutOverlapping();

        $schedule
            ->command('user:refresh-google-access-token')
            ->everyMinute()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function bootstrappers(): array
    {
        return array_merge([OomBootstrapper::class], parent::bootstrappers());
    }
}
