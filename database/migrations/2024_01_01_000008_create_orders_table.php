<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('picture_id')->constrained('pictures')->onDelete('cascade');
            $table->integer('price');
            $table->string('pickup_point', 255);
            $table->string('recipient_name', 255);
            $table->string('unique_code', 10);
            $table->enum('status', ['waiting_shipment', 'in_transit', 'at_pickup_point', 'delivered'])->default('waiting_shipment');
            $table->string('payment_id', 255)->nullable();
            $table->enum('payment_status', ['pending', 'succeeded', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
