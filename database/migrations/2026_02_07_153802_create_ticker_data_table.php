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
         Schema::create('ticker_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('ask', 20, 8);
            $table->decimal('bid', 20, 8);
            $table->decimal('volume', 20, 8);
            $table->string('trade_id');
            $table->decimal('price', 20, 8); // lastPrice
            $table->decimal('size', 20, 8);
            $table->timestamp('time');
            
            // Calculated fields
            $table->decimal('ask_bid_diff', 20, 8)->nullable();
            $table->decimal('ask_last_diff', 20, 8)->nullable();
            $table->decimal('bid_last_diff', 20, 8)->nullable();
            
            $table->timestamps();
            $table->index(['time']); // For efficient date range queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticker_data');
    }
};
