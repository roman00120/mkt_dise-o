<?php

namespace App\Support;

use App\Enums\CreativeService;
use App\Models\RequestType;

final class RequestCatalog
{
    public static function types(CreativeService|string $service): array
    {
        $key = $service instanceof CreativeService ? $service->value : $service;

        return RequestType::query()
            ->where('service', $key)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->pluck('label', 'key')
            ->all();
    }

    public static function services(): array
    {
        return RequestType::query()
            ->where('is_active', true)
            ->select('service')
            ->distinct()
            ->orderBy('service')
            ->pluck('service')
            ->map(function (string $service): array {
                $creativeService = CreativeService::from($service);

                return [
                    'key' => $service,
                    'value' => $service,
                    'name' => $creativeService->label(),
                    'label' => $creativeService->label(),
                    'description' => $creativeService->description(),
                    'icon' => $creativeService->icon(),
                    'tone' => $service,
                ];
            })->values()->all();
    }
}
