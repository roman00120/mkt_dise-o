<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        if (in_array((string) config('app.env'), ['production', 'staging'], true)) {
            throw new \RuntimeException('Los seeders de desarrollo o demostración no pueden ejecutarse en producción.');
        }

        if (filter_var(env('ENABLE_DEMO_DATA', false), FILTER_VALIDATE_BOOL)) {
            $this->call(DemoSeeder::class);
        }
    }
}
