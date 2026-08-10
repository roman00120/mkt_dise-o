<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('service')->nullable();
            $table->foreignId('request_type_id')->nullable()->constrained('request_types')->nullOnDelete();
            $table->string('metric');
            $table->unsignedInteger('target_minutes');
            $table->unsignedTinyInteger('warning_threshold_percent')->default(80);
            $table->boolean('business_hours_only')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('priority')->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['service', 'metric', 'is_active']);
        });
        Schema::create('business_calendar_holidays', function (Blueprint $table): void {
            $table->id();
            $table->date('date')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('request_status_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creative_request_id')->constrained()->cascadeOnDelete();
            $table->string('status', 40);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('duration_seconds')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['creative_request_id', 'status']);
            $table->index(['status', 'started_at']);
        });
        Schema::create('saved_report_views', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('report');
            $table->json('filters');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'name']);
        });
        Schema::create('report_schedules', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('report');
            $table->string('frequency');
            $table->json('filters')->nullable();
            $table->string('format')->default('csv');
            $table->boolean('is_active')->default(true);
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'next_run_at']);
        });
        Schema::create('report_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('report_schedule_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('format');
            $table->string('path')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
            $table->unique(['report_schedule_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_runs');
        Schema::dropIfExists('report_schedules');
        Schema::dropIfExists('saved_report_views');
        Schema::dropIfExists('request_status_periods');
        Schema::dropIfExists('business_calendar_holidays');
        Schema::dropIfExists('sla_policies');
    }
};
