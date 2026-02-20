<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScheduledPosts extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scheduled_posts', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('user_id');
            $table->longText('summary')->nullable();
            $table->string('action_type')->nullable();
            $table->string('action_url')->nullable();
            $table->string('topic_type', 50)->nullable();
            $table->string('alert_type', 50)->nullable();
            $table->string('language_code', 10)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('offer_coupon_code')->nullable();
            $table->string('offer_redeem_online_url')->nullable();
            $table->text('offer_terms_conditions')->nullable();
            $table->string('event_title')->nullable();
            $table->timestamp('event_start_datetime')->nullable();
            $table->timestamp('event_end_datetime')->nullable();
            $table->timestamp('scheduled_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_posts');
    }
}
