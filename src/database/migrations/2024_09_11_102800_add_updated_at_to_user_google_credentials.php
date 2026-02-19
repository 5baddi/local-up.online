<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedAtToUserGoogleCredentials extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_google_credentials', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->after('main_location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_google_credentials', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
}
