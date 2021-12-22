<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongTypesTable extends Migration
{
    public function up(): void
    {
        Schema::create('song_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('song_type', function (Blueprint $table) {
            $table->unsignedBigInteger('song_id');
            $table->unsignedBigInteger('type_id');

            $table->primary(['song_id', 'type_id']);
            $table->foreign('song_id')->on('songs')->references('id')->cascadeOnDelete();
            $table->foreign('type_id')->on('song_types')->references('id')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_type');
        Schema::dropIfExists('song_types');
    }
}
