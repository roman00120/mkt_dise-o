<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creative_requests', function (Blueprint $table): void {
            $table->foreignId('assignee_id')->nullable()->after('requester_id')->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->after('assignee_id')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->string('operational_priority', 20)->nullable()->index();
            $table->date('internal_due_date')->nullable()->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('waiting_information_since')->nullable();
            $table->timestamp('last_status_changed_at')->nullable()->index();
            $table->index(['service', 'status']);
            $table->index(['assignee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('creative_requests', function (Blueprint $table): void {
            foreach (['assignee_id', 'assigned_by', 'validated_by'] as $foreign) {
                $table->dropForeign([$foreign]);
            }
            $table->dropColumn(['assignee_id', 'assigned_by', 'assigned_at', 'validated_by', 'validated_at', 'operational_priority', 'internal_due_date', 'started_at', 'completed_at', 'waiting_information_since', 'last_status_changed_at']);
        });
    }
};
