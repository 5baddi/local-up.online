<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduledFrequencyToScheduledMedia extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scheduled_media', function (Blueprint $table) {
            $table->string('scheduled_frequency')->nullable()->after('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_media', function (Blueprint $table) {
            $table->dropColumn('scheduled_frequency');
        });
    }
}
