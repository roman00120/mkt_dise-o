<?php

namespace Tests\Unit\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Services\Auth\LoginRedirectService;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    public function test_enums_expose_only_defined_roles_and_statuses(): void
    {
        self::assertSame('admin', UserRole::ADMIN->value);
        self::assertSame('creative', UserRole::CREATIVE->value);
        self::assertSame('active', UserStatus::ACTIVE->value);
        self::assertCount(7, UserRole::cases());
        self::assertCount(3, UserStatus::cases());
    }

    public function test_redirect_service_maps_roles_to_named_routes(): void
    {
        $service = new LoginRedirectService;
        $user = new User(['role' => UserRole::MARKETING]);

        self::assertStringEndsWith('/app', $service->pathFor($user));
    }
}
