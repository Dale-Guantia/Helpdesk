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
            $table->unsignedBigInteger('office_id')->nullable()->index('office_id');
            $table->unsignedBigInteger('category_id')->nullable()->index('category_id');
            $table->unsignedBigInteger('priority_id')->nullable()->index('priority_id');
            $table->unsignedBigInteger('status_id')->nullable()->index('status_id');
            $table->string('title');
            $table->text('description');
            $table->softDeletes();
            $table->timestamps();
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
