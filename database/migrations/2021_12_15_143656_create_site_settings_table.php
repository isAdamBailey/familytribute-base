<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->boolean('registration');
            $table->string('registration_secret');
            $table->timestamps();
        });

        DB::table('site_settings')->insert(
            [
                'registration' => true,
                'registration_secret' => Str::lower(config('app.name')),
                'title' => config('app.name'),
                'description' => 'Welcome to '.config('app.name').'. We hope you will enjoy the history of our family.',
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_settings');
    }
}
