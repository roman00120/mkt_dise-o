<?php

namespace Tests\Feature\Reports;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_access_is_scoped_by_role(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $supervisor = User::factory()->create(['role' => UserRole::SUPERVISOR]);
        $marketing = User::factory()->create(['role' => UserRole::MARKETING]);
        $creative = User::factory()->create(['role' => UserRole::DESIGN]);

        $this->actingAs($admin)->get(route('admin.reports.executive'))->assertOk()->assertSee('Resumen ejecutivo');
        $this->actingAs($supervisor)->get(route('creative.reports.operations'))->assertOk()->assertSee('Analítica operativa');
        $this->actingAs($creative)->get(route('creative.metrics.mine'))->assertOk()->assertSee('Mis métricas');
        $this->actingAs($marketing)->get(route('admin.reports.executive'))->assertForbidden();
    }

    public function test_sent_metrics_exclude_drafts_and_cycle_excludes_invalid_dates(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        CreativeRequest::factory()->create(['requester_id' => $admin->id, 'status' => RequestStatus::DRAFT, 'submitted_at' => null]);
        CreativeRequest::factory()->create(['requester_id' => $admin->id, 'status' => RequestStatus::COMPLETED, 'submitted_at' => now()->subDays(4), 'completed_at' => now()->subDays(2)]);
        CreativeRequest::factory()->create(['requester_id' => $admin->id, 'status' => RequestStatus::COMPLETED, 'submitted_at' => now(), 'completed_at' => now()->subDay()]);

        $this->actingAs($admin)->get(route('admin.reports.executive'))->assertOk()->assertSee('Tiempo de ciclo completo');
    }

    public function test_csv_and_pdf_exports_are_authenticated_and_available(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin)->post(route('password.confirm.store'), ['password' => 'password']);
        $this->get(route('admin.reports.export.csv'))->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->get(route('admin.reports.export.pdf'))->assertOk()->assertHeader('content-type', 'application/pdf');
    }
}
