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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offer_id')->constrained('job_offers')->onDelete('cascade');
            $table->string('applicant_first_name');
            $table->string('applicant_last_name');
            $table->string('applicant_email');
            $table->string('applicant_phone')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('cv_path'); // Path to uploaded CV
            $table->string('cv_original_name'); // Original filename
            $table->string('cover_letter_path')->nullable(); // Optional cover letter file
            $table->string('cover_letter_original_name')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, rejected, accepted
            $table->text('admin_notes')->nullable();
            $table->string('application_number')->unique();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('user_models')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['job_offer_id', 'status']);
            $table->index('application_number');
            $table->index('applicant_email');
            $table->index('applied_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
