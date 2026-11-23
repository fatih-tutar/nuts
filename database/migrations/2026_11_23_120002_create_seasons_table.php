<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->string('label')->nullable();
            $table->date('order_start_date')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('gold_price', 12, 2)->nullable();
            $table->decimal('usd_rate', 12, 4)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
