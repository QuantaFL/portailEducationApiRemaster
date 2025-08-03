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
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_model_id')->nullable()->constrained('class_models')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
            $table->timestamps();
            
            // Avoid duplicate class-subject combinations
            $table->unique(['class_model_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subject');
    }
};
