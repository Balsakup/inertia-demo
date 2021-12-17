<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongAuthorsTable extends Migration
{
    public function up(): void
    {
        Schema::create('song_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('website');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_authors');
    }
}
