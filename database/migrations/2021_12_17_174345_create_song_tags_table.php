<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongTagsTable extends Migration
{
    public function up(): void
    {
        Schema::create('song_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('song_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('song_id');
            $table->unsignedBigInteger('tag_id');

            $table->primary(['song_id', 'tag_id']);
            $table->foreign('song_id')->on('songs')->references('id')->cascadeOnDelete();
            $table->foreign('tag_id')->on('song_tags')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('song_tags');
        Schema::dropIfExists('song_tag');
    }
}
