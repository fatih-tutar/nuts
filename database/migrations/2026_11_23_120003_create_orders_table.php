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
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_kg', 10, 2);
            $table->string('status', 32)->default('ordered');
            $table->date('ordered_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['season_id', 'status']);
            $table->index(['customer_id', 'season_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
