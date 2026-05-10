<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pictures', 'show_sold_badge')) {
            Schema::table('pictures', function (Blueprint $table) {
                $table->boolean('show_sold_badge')->default(false)->after('status');
            });
        }

        if (!Schema::hasColumn('pictures', 'hidden_after_sale')) {
            Schema::table('pictures', function (Blueprint $table) {
                $table->boolean('hidden_after_sale')->default(false)->after('show_sold_badge');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pictures', 'hidden_after_sale')) {
            Schema::table('pictures', function (Blueprint $table) {
                $table->dropColumn('hidden_after_sale');
            });
        }

        if (Schema::hasColumn('pictures', 'show_sold_badge')) {
            Schema::table('pictures', function (Blueprint $table) {
                $table->dropColumn('show_sold_badge');
            });
        }
    }
};
