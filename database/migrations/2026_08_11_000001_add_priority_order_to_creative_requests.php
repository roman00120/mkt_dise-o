<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creative_requests', function (Blueprint $table): void {
            $table->unsignedInteger('priority_order')->nullable()->index()->after('requested_priority');
        });
    }

    public function down(): void
    {
        Schema::table('creative_requests', function (Blueprint $table): void {
            $table->dropColumn('priority_order');
        });
    }
};
