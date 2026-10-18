<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->string('model', 80);
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('storage_gb');
            $table->unsignedSmallInteger('ram_gb')->nullable();
            $table->string('color', 40)->nullable();
            $table->char('grade', 1)->default('A');
            $table->unsignedTinyInteger('battery_health')->nullable();
            $table->unsignedInteger('price_sell');
            $table->unsignedInteger('price_cost')->default(0);
            $table->string('status', 12)->default('available');
            $table->string('imei', 20)->nullable();
            $table->string('accessories', 300)->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('warranty_days')->default(0);
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['status', 'price_sell']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
