<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackgroundImageColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('obituaries', function (Blueprint $table) {
            if (Schema::hasColumn('obituaries', 'headstone_url')) {
                $table->renameColumn('headstone_url', 'main_photo_url');
            }
            $table->string('background_photo_url')->nullable()->after('main_photo_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('obituaries', function (Blueprint $table) {
            if (Schema::hasColumn('obituaries', 'main_photo_url')) {
                $table->renameColumn('main_photo_url', 'headstone_url');
            }
            $table->dropColumn('background_photo_url');
        });
    }
}