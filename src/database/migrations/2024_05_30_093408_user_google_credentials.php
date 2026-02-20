<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserGoogleCredentials extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_google_credentials', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('user_id')->unique();
            $table->longText('id_token');
            $table->string('account_id');
            $table->longText('access_token');
            $table->longText('refresh_token');
            $table->mediumText('scope');
            $table->string('token_type');
            $table->integer('expires_in');
            $table->bigInteger('created');
            $table->string('main_location_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_google_credentials');
    }
}
