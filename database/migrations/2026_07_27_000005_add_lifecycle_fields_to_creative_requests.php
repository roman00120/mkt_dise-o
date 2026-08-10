<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creative_requests', function (Blueprint $table): void {
            $table->foreignId('duplicated_from_id')->nullable()->after('requester_id')->constrained('creative_requests')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable()->after('submitted_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            $table->index(['requester_id', 'status']);
            $table->index(['requester_id', 'created_at']);
            $table->index(['requester_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::table('creative_requests', function (Blueprint $table): void {
            $table->dropForeign(['duplicated_from_id']);
            $table->dropIndex('creative_requests_requester_id_status_index');
            $table->dropIndex('creative_requests_requester_id_created_at_index');
            $table->dropIndex('creative_requests_requester_id_updated_at_index');
            $table->dropColumn(['duplicated_from_id', 'cancelled_at', 'cancellation_reason']);
        });
    }
};
