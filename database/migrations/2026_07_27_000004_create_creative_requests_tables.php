<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creative_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('folio')->unique();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable();
            $table->string('service', 20)->index();
            $table->string('request_type', 40);
            $table->string('other_request_type')->nullable();
            $table->string('title', 120)->nullable();
            $table->text('description')->nullable();
            $table->text('objective')->nullable();
            $table->string('target_audience', 500)->nullable();
            $table->string('channel')->nullable();
            $table->date('required_date')->nullable()->index();
            $table->string('requested_priority', 20)->default('medium');
            $table->text('urgency_reason')->nullable();
            $table->string('status', 40)->default('draft')->index();
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('last_autosaved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('creative_request_details', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creative_request_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('data')->nullable();
            $table->timestamps();
        });
        Schema::create('creative_request_files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creative_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('mime_type');
            $table->string('extension', 10);
            $table->unsignedBigInteger('size');
            $table->string('category', 20)->default('reference');
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('creative_request_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creative_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->string('event');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
        Schema::create('creative_request_sequences', function (Blueprint $table): void {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creative_request_sequences');
        Schema::dropIfExists('creative_request_events');
        Schema::dropIfExists('creative_request_files');
        Schema::dropIfExists('creative_request_details');
        Schema::dropIfExists('creative_requests');
    }
};
