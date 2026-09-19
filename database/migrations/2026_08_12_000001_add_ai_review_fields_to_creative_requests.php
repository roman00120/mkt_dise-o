<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creative_requests', function (Blueprint $table): void {
            $table->string('ai_review_status', 20)->nullable()->after('last_autosaved_at');
            $table->json('ai_review_result')->nullable()->after('ai_review_status');
            $table->timestamp('ai_reviewed_at')->nullable()->after('ai_review_result');
            $table->text('ai_review_error')->nullable()->after('ai_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('creative_requests', function (Blueprint $table): void {
            $table->dropColumn(['ai_review_status', 'ai_review_result', 'ai_reviewed_at', 'ai_review_error']);
        });
    }
};
