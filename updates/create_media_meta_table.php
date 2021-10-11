<?php namespace ApplyNowTv\JobSeeker\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateMediaMetaTable extends Migration
{
    public function up()
    {
        Schema::create('jaxwilko_media_meta', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('path')->unique();
            $table->text('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jaxwilko_media_meta');
    }
}
