<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SavedReportView extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'user_id', 'name', 'report', 'filters', 'is_default'];

    protected function casts(): array
    {
        return ['filters' => 'array', 'is_default' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
