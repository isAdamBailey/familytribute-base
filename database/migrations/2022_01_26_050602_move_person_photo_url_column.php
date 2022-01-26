<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MovePersonPhotoUrlColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string('photo_url')->nullable()->after('slug');
        });

        $this->movePhotoUrl();

        Schema::table('obituaries', function (Blueprint $table) {
            $table->dropColumn('main_photo_url');
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
            $table->string('main_photo_url')->nullable()->after('death_date');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('photo_url');
        });
    }

    private function movePhotoUrl()
    {
        foreach (\App\Models\Obituary::all() as $item) {
            $item->person->update(['photo_url' => $item->main_photo_url]);
        }
    }
}
