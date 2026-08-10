<?php

namespace Tests\Unit\Dashboard;

use App\Models\User;
use App\Services\Dashboard\MarketingDashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingDashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_returns_empty_database_state_without_fallback_data(): void
    {
        $user = User::factory()->create();
        $data = app(MarketingDashboardService::class)->forUser('all', $user);

        $this->assertCount(4, $data['metrics']);
        $this->assertSame(0, $data['metrics'][0]['value']);
        $this->assertSame([], $data['attentionItems']);
        $this->assertSame([], $data['recentRequests']);
        $this->assertSame([], $data['pendingDeliverables']);
        $this->assertSame([], $data['recentActivity']);
        $this->assertSame('all', $data['filter']);
    }

    public function test_date_health_uses_explicit_business_thresholds(): void
    {
        $service = app(MarketingDashboardService::class);
        $today = Carbon::parse('2026-07-27', 'America/Mexico_City');

        $this->assertSame('without_date', $service->dateHealth(null, false, $today));
        $this->assertSame('overdue', $service->dateHealth('2026-07-26', false, $today));
        $this->assertSame('due_soon', $service->dateHealth('2026-07-29', false, $today));
        $this->assertSame('on_time', $service->dateHealth('2026-08-05', false, $today));
        $this->assertSame('on_time', $service->dateHealth('2026-07-20', true, $today));
    }
}
