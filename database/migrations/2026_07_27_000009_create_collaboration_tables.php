<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('commentable_type');
            $table->unsignedBigInteger('commentable_id');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->string('visibility', 20);
            $table->text('body');
            $table->timestamp('edited_at')->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['commentable_type', 'commentable_id']);
            $table->index(['visibility', 'created_at']);
        });

        Schema::create('comment_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->cascadeOnDelete();
            $table->foreignId('edited_by')->constrained('users')->restrictOnDelete();
            $table->text('previous_body');
            $table->timestamps();
        });

        Schema::create('comment_mentions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->cascadeOnDelete();
            $table->foreignId('mentioned_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['comment_id', 'mentioned_user_id']);
            $table->index('mentioned_user_id');
        });

        Schema::create('comment_attachments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('comment_id')->constrained('comments')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('disk', 40);
            $table->string('path');
            $table->string('mime_type', 150);
            $table->string('extension', 12);
            $table->unsignedBigInteger('size');
            $table->string('checksum', 64)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['comment_id', 'uploaded_by']);
        });

        Schema::create('notification_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('event_type', 60);
            $table->boolean('in_app')->default(true);
            $table->boolean('email')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('comment_attachments');
        Schema::dropIfExists('comment_mentions');
        Schema::dropIfExists('comment_revisions');
        Schema::dropIfExists('comments');
    }
};
