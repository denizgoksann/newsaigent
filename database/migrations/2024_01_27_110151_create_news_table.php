<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->text('news_title');
            $table->text('uniq_words')->nullable();
            $table->text('file')->nullable();
            $table->text('news')->nullable();
            $table->text('spot')->nullable();
            $table->text('location')->nullable();
            $table->text('editor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

