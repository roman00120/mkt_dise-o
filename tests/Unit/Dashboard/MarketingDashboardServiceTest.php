<?php

namespace Tests\Unit\Dashboard;

use App\Services\Dashboard\MarketingDashboardService;
use Carbon\Carbon;
use Tests\TestCase;

class MarketingDashboardServiceTest extends TestCase
{
    public function test_service_returns_expected_dashboard_sections(): void
    {
        $data = app(MarketingDashboardService::class)->forUser();

        $this->assertCount(4, $data['metrics']);
        $this->assertCount(3, $data['serviceCards']);
        $this->assertNotEmpty($data['attentionItems']);
        $this->assertNotEmpty($data['recentRequests']);
        $this->assertNotEmpty($data['pendingDeliverables']);
        $this->assertNotEmpty($data['recentActivity']);
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

    public function test_attention_items_are_ordered_by_due_date(): void
    {
        $items = app(MarketingDashboardService::class)->forUser()['attentionItems'];
        $dates = array_map(fn (array $item): int => $item['due_at']->getTimestamp(), $items);

        $sorted = $dates;
        sort($sorted);

        $this->assertSame($sorted, $dates);
    }
}
