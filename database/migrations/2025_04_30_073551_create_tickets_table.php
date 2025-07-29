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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->foreignId('office_id')->nullable()->constrained('offices')->onDelete('cascade');
            $table->foreignId('problem_category_id')->nullable()->constrained('problem_categories')->onDelete('cascade');
            $table->string('custom_problem_category')->nullable();
            $table->foreignId('priority_id')->nullable()->constrained('priorities')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('attachment')->nullable();
            // $table->string('guest_firstName')->nullable();
            // $table->string('guest_middleName')->nullable();
            // $table->string('guest_lastName')->nullable();
            $table->timestamps();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
