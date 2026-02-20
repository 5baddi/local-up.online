<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsExpiredToUserGoogleCredentials extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_google_credentials', function (Blueprint $table) {
            $table->boolean('is_expired')->default(false)->after('expires_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_google_credentials', function (Blueprint $table) {
            $table->dropColumn('is_expired');
        });
    }
}
