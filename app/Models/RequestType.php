<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RequestType extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
    }

    protected $fillable = ['uuid', 'service', 'key', 'label', 'description', 'is_active', 'sort_order', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
