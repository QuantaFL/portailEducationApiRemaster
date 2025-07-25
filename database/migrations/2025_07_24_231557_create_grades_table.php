<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->decimal('mark');
            $table->foreignId('assignement_id')->constrained('assignements');
            $table->foreignId('student_session_id')->constrained('student_sessions');
            $table->foreignId('term_id')->constrained('terms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
