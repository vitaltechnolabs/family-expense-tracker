<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id('tag_id');
            $table->foreignId('family_id')->constrained('families', 'family_id')->onDelete('cascade');
            $table->string('tag_name');
            $table->foreignId('category_id')->nullable()->constrained('categories', 'category_id')->onDelete('set null');
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
