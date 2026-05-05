<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('pictures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('img', 255);
            $table->integer('width');
            $table->integer('height');
            $table->string('name', 255);
            $table->string('technique', 255);
            $table->integer('year');
            $table->text('description');
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->foreignId('style_id')->constrained('styles')->onDelete('cascade');
            $table->foreignId('era_id')->constrained('eras')->onDelete('cascade');
            $table->integer('price');
            $table->enum('listing_type', ['gallery', 'auction'])->default('gallery');
            $table->integer('auction_start_price')->nullable();
            $table->integer('auction_current_price')->nullable();
            $table->integer('auction_min_step')->nullable();
            $table->integer('auction_buyout_price')->nullable();
            $table->dateTime('auction_starts_at')->nullable();
            $table->dateTime('auction_ends_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pictures');
    }
};
