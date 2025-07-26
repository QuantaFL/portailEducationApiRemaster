<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_sessions', function (Blueprint $table) {
            $table->string('justificatif_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('student_sessions', function (Blueprint $table) {
            $table->dropColumn('justificatif_path');
        });
    }
};

