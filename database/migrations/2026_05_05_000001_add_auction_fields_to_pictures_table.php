<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pictures', function (Blueprint $table) {
            $table->enum('listing_type', ['gallery', 'auction'])->default('gallery')->after('price');
            $table->integer('auction_start_price')->nullable()->after('listing_type');
            $table->integer('auction_current_price')->nullable()->after('auction_start_price');
            $table->integer('auction_min_step')->nullable()->after('auction_current_price');
            $table->integer('auction_buyout_price')->nullable()->after('auction_min_step');
            $table->dateTime('auction_starts_at')->nullable()->after('auction_buyout_price');
            $table->dateTime('auction_ends_at')->nullable()->after('auction_starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('pictures', function (Blueprint $table) {
            $table->dropColumn([
                'listing_type',
                'auction_start_price',
                'auction_current_price',
                'auction_min_step',
                'auction_buyout_price',
                'auction_starts_at',
                'auction_ends_at',
            ]);
        });
    }
};
