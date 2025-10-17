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
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('office_id')->nullable()->constrained('offices');
            $table->foreignId('problem_category_id')->nullable()->constrained('problem_categories');
            $table->string('custom_problem_category')->nullable();
            $table->foreignId('priority_id')->nullable()->constrained('priorities');
            $table->unsignedBigInteger('status_id')->nullable();
            $table->text('description');
            $table->json('attachment')->nullable();
            // $table->string('attachment')->nullable();
            // $table->string('guest_firstName')->nullable();
            // $table->string('guest_middleName')->nullable();
            // $table->string('guest_lastName')->nullable();
            $table->timestamps();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users');
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
