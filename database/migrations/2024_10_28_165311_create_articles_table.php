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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->nullable();
            $table->string('source_name')->nullable();
            $table->string('author')->nullable();
            $table->text('title');
            $table->text('description')->nullable();
            $table->text('url')->nullable();
            $table->text('url_to_image')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->text('content')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
