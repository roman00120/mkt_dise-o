<?php

namespace App\Services\Health;

class SchedulerHealthCheck
{
    public function check(): array
    {
        return ['status' => 'warning', 'message' => 'El heartbeat se valida por ejecución del scheduler.'];
    }
}
