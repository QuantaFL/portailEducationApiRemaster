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
        Schema::table('assignments', function (Blueprint $table) {
            $table->boolean('isActive')->default(true)->after('coefficient');
            $table->string('assignment_number')->unique()->nullable()->after('isActive');
            
            $table->index('isActive');
            $table->index('assignment_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['isActive', 'assignment_number']);
        });
    }
};
