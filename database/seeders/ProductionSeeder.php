<?php

namespace Database\Seeders;

use App\Enums\CreativeService;
use App\Models\Department;
use App\Models\RequestType;
use App\Models\SlaPolicy;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['Administración', 'administration'], ['Marketing', 'marketing'], ['Diseño', 'design']] as [$name, $code]) {
            Department::updateOrCreate(['code' => $code], [
                'name' => $name,
                'uuid' => (string) str()->uuid(),
                'is_active' => true,
            ]);
        }

        foreach (RequestTypeSeedData::all() as $type) {
            RequestType::updateOrCreate(
                ['service' => $type['service'], 'key' => $type['key']],
                $type + ['uuid' => (string) str()->uuid(), 'is_active' => true]
            );
        }

        foreach ([
            ['recommended_days.design', '5', 'integer', 'operations'],
            ['recommended_days.video', '7', 'integer', 'operations'],
            ['recommended_days.render', '7', 'integer', 'operations'],
            ['organization.name', 'TOTAL GROUND', 'string', 'organization'],
            ['notifications.database', '1', 'boolean', 'notifications'],
        ] as [$key, $value, $type, $group]) {
            SystemSetting::updateOrCreate(['key' => $key], [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'is_sensitive' => false,
            ]);
        }

        foreach ([
            ['initial_response', null, 480],
            ['assignment', null, 1440],
            ['delivery', CreativeService::DESIGN->value, 7200],
            ['delivery', CreativeService::VIDEO->value, 10080],
            ['delivery', CreativeService::RENDER->value, 10080],
        ] as [$metric, $service, $minutes]) {
            SlaPolicy::updateOrCreate(
                ['metric' => $metric, 'service' => $service, 'effective_from' => today()],
                [
                    'name' => ucfirst(str_replace('_', ' ', $metric)).($service ? ' '.$service : ''),
                    'target_minutes' => $minutes,
                    'warning_threshold_percent' => 80,
                    'business_hours_only' => false,
                    'is_active' => true,
                ]
            );
        }
    }
}
