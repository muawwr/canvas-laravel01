<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('picture_id')->constrained('pictures')->onDelete('cascade');
            $table->timestamp('added_at')->useCurrent();
            $table->unique(['user_id', 'picture_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
