<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->tinyInteger('role')->default(1)->comment('1=user, 2=admin');
            $table->string('rank', 50)->default('newbie');
            $table->timestamp('date_of_reg')->useCurrent();
            $table->integer('pictures_count')->default(0);
            $table->integer('orders_count')->default(0);
            $table->string('img', 255)->default('assets/images/account/mainUser.png');
            $table->integer('profile_views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
