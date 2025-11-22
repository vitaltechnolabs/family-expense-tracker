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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('expense_id');
            $table->foreignId('family_id')->constrained('families', 'family_id')->onDelete('cascade');
            $table->foreignId('logged_by_user_id')->constrained('users', 'user_id');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->foreignId('tag_id')->nullable()->constrained('tags', 'tag_id');
            $table->foreignId('for_member_id')->nullable()->constrained('users', 'user_id');
            $table->string('attachment_url')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('payment_method', ['Cash', 'Cheque', 'UPI', 'Net Banking'])->default('UPI');
            $table->foreignId('from_account_user_id')->constrained('users', 'user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
