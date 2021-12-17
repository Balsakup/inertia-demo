<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('duration')->nullable();
            $table->text('description');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('author_id');
            $table->timestamps();

            $table->foreign('author_id')->on('song_authors')->references('id')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
}
