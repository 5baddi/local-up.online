<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScheduledPostMedia extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scheduled_post_media', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('scheduled_post_id')->unique();
            $table->string('path');
            $table->string('type')->default('photo');

            $table->timestamps();

            $table->foreign('scheduled_post_id')->references('id')->on('scheduled_posts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_post_media');
    }
}
