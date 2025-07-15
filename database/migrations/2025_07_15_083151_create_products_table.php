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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('short-description')->nullable();
            $table->text('description');
            $table->decimal('regular-price');
            $table->decimal('sale-price')->nullable();
            $table->string('SKU');
            $table->enum('stock-status', ['in-stock', 'out-of-stock'])->default('in-stock');
            $table->boolean('featured')->default(false);
            $table->unsignedInteger('quantity')->default(10);
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->bigInteger('category-id')->unsigned()->nullable();
            $table->bigInteger('brand-id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('category-id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('brand-id')->references('id')->on('brands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
