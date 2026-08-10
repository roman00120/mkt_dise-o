<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group', 'is_sensitive', 'updated_by'];

    protected function casts(): array
    {
        return ['is_sensitive' => 'boolean'];
    }
}
