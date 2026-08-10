<?php

namespace App\Services\Settings;

use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\Cache;

class SystemSettingService
{
    public const ALLOWED = ['recommended_days.design', 'recommended_days.video', 'recommended_days.render', 'organization.name', 'notifications.database'];

    public function __construct(private AuditLogService $audit) {}

    public function all(): array
    {
        return SystemSetting::query()->whereIn('key', self::ALLOWED)->orderBy('group')->orderBy('key')->pluck('value', 'key')->all();
    }

    public function value(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember('system-setting:'.$key, 300, fn () => SystemSetting::where('key', $key)->first());

        return $setting ? match ($setting->type) {
            'integer' => (int) $setting->value, 'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOL), 'json' => json_decode($setting->value, true), default => $setting->value
        } : $default;
    }

    public function update(array $values, User $actor): void
    {
        foreach ($values as $key => $value) {
            if (! in_array($key, self::ALLOWED, true)) {
                continue;
            } $setting = SystemSetting::where('key', $key)->firstOrFail();
            $setting->update(['value' => is_array($value) ? json_encode($value) : (string) $value, 'updated_by' => $actor->id]);
            Cache::forget('system-setting:'.$key);
            $this->audit->record('setting.updated', $actor, $setting, null, ['key' => $key]);
        }
    }
}
