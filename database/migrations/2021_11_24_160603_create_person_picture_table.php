<?php

use App\Models\Person;
use App\Models\Picture;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonPictureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person_picture', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Person::class);
            $table->foreignIdFor(Picture::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_picture');
    }
}
