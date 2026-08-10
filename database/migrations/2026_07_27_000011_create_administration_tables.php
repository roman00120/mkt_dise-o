<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 30);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique('name');
            $table->unique('code');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->foreignId('department_id')->nullable()->after('role')->constrained('departments')->nullOnDelete();
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
        });

        Schema::create('request_types', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('service', 30);
            $table->string('key', 80);
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['service', 'key']);
            $table->index(['service', 'is_active', 'sort_order']);
        });

        Schema::create('system_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string');
            $table->string('group', 50)->default('general')->index();
            $table->boolean('is_sensitive')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['actor_id', 'created_at']);
            $table->index(['target_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('request_types');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['uuid', 'department_id', 'password_changed_at', 'suspended_at', 'deactivated_at']);
        });
        Schema::dropIfExists('departments');
    }
};
