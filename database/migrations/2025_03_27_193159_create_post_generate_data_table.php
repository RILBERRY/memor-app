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
        Schema::create('post_generate_data', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->nullable();
            $table->text('celebration_title');
            $table->text('message')->nullable();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->json('custom_img_path')->nullable();
            $table->text('url_slug')->nullable()->unique();
            $table->json('post_path')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_generate_data');
    }
};
