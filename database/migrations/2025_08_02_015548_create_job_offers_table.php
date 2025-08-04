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
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('location')->nullable();
            $table->string('employment_type')->default('full_time'); // full_time, part_time, contract
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('experience_level')->default('entry'); // entry, junior, senior, expert
            $table->date('application_deadline');
            $table->boolean('is_active')->default(true);
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->text('benefits')->nullable();
            $table->string('offer_number')->unique();
            $table->foreignId('posted_by')->constrained('user_models')->onDelete('cascade');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'application_deadline']);
            $table->index('subject_id');
            $table->index('offer_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
