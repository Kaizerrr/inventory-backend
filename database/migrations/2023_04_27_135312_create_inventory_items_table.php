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
        Schema::create('category', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->timestamps();
        });

        Schema::create('property', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('inputType');
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('purchased_date');
            $table->float('lifespan');
            $table->timestamps();
        });

        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('item_id');
            $table->string('value');

            $table->foreign('category_id')->references('id')->on('category');
            $table->foreign('property_id')->references('id')->on('property');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
        Schema::dropIfExists('category');
        Schema::dropIfExists('property');
        Schema::dropIfExists('items');
    }
};
