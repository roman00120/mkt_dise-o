<?php

namespace App\Services\Requests;

use Illuminate\Support\Facades\DB;

class FolioGenerator
{
    public function next(): string
    {
        $year = (int) now()->year;

        return DB::transaction(function () use ($year): string {
            DB::table('creative_request_sequences')->insertOrIgnore(['year' => $year, 'last_number' => 0, 'created_at' => now(), 'updated_at' => now()]);
            $sequence = DB::table('creative_request_sequences')->where('year', $year)->lockForUpdate()->first();
            $number = $sequence->last_number + 1;
            DB::table('creative_request_sequences')->where('year', $year)->update(['last_number' => $number, 'updated_at' => now()]);

            return sprintf('TG-%d-%04d', $year, $number);
        });
    }
}
