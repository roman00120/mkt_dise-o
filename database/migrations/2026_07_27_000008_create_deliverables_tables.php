<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliverables', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('creative_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->unsignedBigInteger('approved_version_id')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('submitted_to_marketing_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('creative_request_id');
        });
        Schema::create('deliverable_versions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('deliverable_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('status')->default('draft')->index();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('submitted_for_internal_review_at')->nullable();
            $table->timestamp('submitted_to_marketing_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['deliverable_id', 'version_number']);
            $table->index(['deliverable_id', 'status']);
        });
        Schema::create('deliverable_files', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('deliverable_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('mime_type');
            $table->string('extension', 12);
            $table->unsignedBigInteger('size');
            $table->string('checksum')->nullable();
            $table->string('category', 20);
            $table->string('description')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['deliverable_version_id', 'category']);
        });
        Schema::create('deliverable_reviews', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('deliverable_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->string('review_type', 20);
            $table->string('decision', 30);
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->index(['deliverable_version_id', 'review_type']);
        });
        Schema::create('correction_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('creative_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deliverable_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deliverable_version_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->string('type', 20);
            $table->string('status', 20)->default('open')->index();
            $table->string('summary', 200);
            $table->text('details');
            $table->string('category')->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('resolved_by_version_id')->nullable()->constrained('deliverable_versions')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['creative_request_id', 'type', 'status']);
            $table->index(['deliverable_version_id', 'status']);
        });
        Schema::table('deliverables', function (Blueprint $table): void {
            $table->foreign('current_version_id')->references('id')->on('deliverable_versions')->nullOnDelete();
            $table->foreign('approved_version_id')->references('id')->on('deliverable_versions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('deliverables', function (Blueprint $table): void {
            $table->dropForeign(['current_version_id']);
            $table->dropForeign(['approved_version_id']);
        });
        Schema::dropIfExists('correction_requests');
        Schema::dropIfExists('deliverable_reviews');
        Schema::dropIfExists('deliverable_files');
        Schema::dropIfExists('deliverable_versions');
        Schema::dropIfExists('deliverables');
    }
};
