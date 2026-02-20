<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnlineIdFieldToAutoPosting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->string('online_id')->nullable()->after('state');
        });

        Schema::table('scheduled_media', function (Blueprint $table) {
            $table->string('account_id')->nullable()->after('user_id');
            $table->string('location_id')->nullable()->after('account_id');
            $table->string('online_id')->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->dropColumn('online_id');
        });

        Schema::table('scheduled_media', function (Blueprint $table) {
            $table->dropColumn('account_id');
            $table->dropColumn('location_id');
            $table->dropColumn('online_id');
        });
    }
}
