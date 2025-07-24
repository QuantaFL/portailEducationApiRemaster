<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->string('average_grade');
            $table->string('honors');
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('term_id')->constrained('terms');
            $table->string('path');
            $table->string('rank');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};
