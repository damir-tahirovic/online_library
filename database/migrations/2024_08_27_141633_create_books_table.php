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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('number_of_pages');
            $table->integer('number_of_copies');
            $table->string('isbn');
            $table->string('language');
            $table->enum('script', ['cyrilic', 'latin', 'arabic']);
            $table->enum('binding', ['hardcover', 'paperback', 'spiral-bound']);
            $table->string('dimensions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
