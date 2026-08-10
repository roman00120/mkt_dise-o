<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_information_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creative_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->text('message');
            $table->string('category')->nullable();
            $table->string('previous_status', 40);
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('response')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('responded_at')->nullable();
            $table->string('status', 20)->default('open')->index();
            $table->timestamps();
            $table->index(['creative_request_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_information_requests');
    }
};
