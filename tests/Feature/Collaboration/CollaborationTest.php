<?php

namespace Tests\Feature\Collaboration;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CollaborationTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_comment_and_creative_can_see_public_conversation(): void
    {
        [$marketing, $designer, $request] = $this->setUpRequest();
        $this->actingAs($marketing)->post(route('app.requests.comments.store', $request), ['body' => 'Necesito confirmar la fecha.'])->assertRedirect();
        $this->assertDatabaseHas('comments', ['commentable_id' => $request->id, 'visibility' => 'public', 'body' => 'Necesito confirmar la fecha.']);
        $this->actingAs($designer)->get(route('creative.requests.show', $request))->assertOk()->assertSee('Necesito confirmar la fecha.');
    }

    public function test_marketing_cannot_see_internal_notes(): void
    {
        [$marketing, $designer, $request] = $this->setUpRequest();
        $this->actingAs($designer)->post(route('creative.requests.internal-notes.store', $request), ['body' => 'Solo para el equipo.'])->assertRedirect();
        $this->actingAs($marketing)->get(route('app.requests.show', $request))->assertOk()->assertDontSee('Solo para el equipo.');
        $this->assertDatabaseHas('comments', ['visibility' => 'internal']);
    }

    public function test_replies_are_one_level_and_editing_creates_revision(): void
    {
        [$marketing, $designer, $request] = $this->setUpRequest();
        $comment = Comment::create(['uuid' => fake()->uuid(), 'commentable_type' => $request->getMorphClass(), 'commentable_id' => $request->id, 'user_id' => $marketing->id, 'visibility' => 'public', 'body' => 'Pregunta']);
        $this->actingAs($designer)->post(route('creative.requests.comments.replies.store', [$request, $comment]), ['body' => 'Respuesta'])->assertRedirect();
        $reply = $comment->replies()->firstOrFail();
        $this->actingAs($marketing)->post(route('app.requests.comments.replies.store', [$request, $reply]), ['body' => 'Segundo nivel'])->assertStatus(422);
        $this->actingAs($marketing)->patch(route('app.requests.comments.update', [$request, $comment]), ['body' => 'Pregunta editada'])->assertRedirect();
        $this->assertDatabaseHas('comment_revisions', ['comment_id' => $comment->id, 'previous_body' => 'Pregunta']);
    }

    public function test_comment_attachment_is_private_and_download_is_scoped(): void
    {
        Storage::fake('local');
        [$marketing, $designer, $request] = $this->setUpRequest();
        $this->actingAs($marketing)->post(route('app.requests.comments.store', $request), ['body' => 'Referencia', 'attachments' => [UploadedFile::fake()->create('reference.pdf', 10, 'application/pdf')]])->assertRedirect();
        $comment = $request->comments()->with('attachments')->firstOrFail();
        $attachment = $comment->attachments->firstOrFail();
        Storage::disk('local')->assertExists($attachment->path);
        $this->actingAs($designer)->get(route('creative.requests.comments.attachments.download', [$request, $comment, $attachment]))->assertOk();
    }

    private function setUpRequest(): array
    {
        $marketing = User::factory()->create(['role' => UserRole::MARKETING]);
        $designer = User::factory()->create(['role' => UserRole::DESIGN]);
        $request = CreativeRequest::factory()->create(['requester_id' => $marketing->id, 'assignee_id' => $designer->id, 'service' => 'design', 'status' => RequestStatus::IN_PROGRESS]);

        return [$marketing, $designer, $request];
    }
}
