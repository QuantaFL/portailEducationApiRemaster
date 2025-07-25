<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_sessions', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('students');
            $table->foreignUuid('academic_year_id')->constrained('academic_years');
            $table->foreignId('class_model_id')->constrained('classes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_session');
    }
};
