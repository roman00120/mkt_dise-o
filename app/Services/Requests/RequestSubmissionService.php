<?php

namespace App\Services\Requests;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RequestSubmissionService
{
    public function submit(CreativeRequest $request): CreativeRequest
    {
        return DB::transaction(function () use ($request) {
            if (! $request->isDraft()) {
                return $request;
            }

            $serviceCode = $request->service->value ?? (string) $request->service;
            $serviceRole = UserRole::tryFrom($serviceCode);

            // Find an active creative for this service area
            $assignee = User::query()
                ->where('status', UserStatus::ACTIVE)
                ->get()
                ->first(function (User $user) use ($serviceRole, $serviceCode) {
                    if ($serviceRole && $user->hasRole($serviceRole)) {
                        return true;
                    }
                    return $user->hasRole(UserRole::CREATIVE);
                });

            $status = $assignee ? RequestStatus::ASSIGNED : RequestStatus::PENDING;
            $assigneeId = $assignee?->id;
            $assignedAt = $assignee ? now() : null;

            $request->update([
                'status' => $status,
                'assignee_id' => $assigneeId,
                'assigned_at' => $assignedAt,
                'internal_due_date' => $request->internal_due_date ?: $request->required_date,
                'submitted_at' => now(),
                'current_step' => 7,
            ]);

            $request->events()->create([
                'actor_id' => auth()->id(),
                'event' => 'request_submitted',
                'metadata' => [
                    'folio' => $request->folio,
                    'auto_assigned_to' => $assignee?->name,
                ],
            ]);

            if ($assignee) {
                $request->events()->create([
                    'actor_id' => auth()->id(),
                    'event' => 'request_assigned',
                    'metadata' => [
                        'assignee' => $assignee->name,
                        'observation' => 'Auto-asignado al enviar la solicitud',
                    ],
                ]);
            }

            $freshRequest = $request->fresh(['detail', 'files', 'assignee', 'requester']);

            // Send Email Notifications ONLY to Hugo (Admin/Supervisor) & the specific Creative for this service (Carolina for Design, Jesús for Render, Gerardo for Video)
            $recipients = User::query()
                ->where('status', UserStatus::ACTIVE)
                ->get()
                ->filter(function (User $user) use ($freshRequest, $serviceRole) {
                    if ($user->hasRole(UserRole::ADMIN, UserRole::SUPERVISOR)) {
                        return true;
                    }
                    if ($freshRequest->assignee_id && $user->id === $freshRequest->assignee_id) {
                        return true;
                    }
                    if ($serviceRole && $user->hasRole($serviceRole)) {
                        return true;
                    }
                    return false;
                })
                ->unique('id');

            foreach ($recipients as $recipient) {
                try {
                    $recipient->notify(new \App\Notifications\CreativeRequestSubmittedNotification($freshRequest));
                } catch (\Throwable $e) {
                    logger()->error('Failed sending request submitted notification: '.$e->getMessage());
                }
            }

            return $freshRequest;
        });
    }
}
