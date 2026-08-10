<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliverableFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uuid', 'deliverable_version_id', 'uploaded_by', 'original_name', 'stored_name', 'disk', 'path', 'mime_type', 'extension', 'size', 'checksum', 'category', 'description', 'is_primary'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function version()
    {
        return $this->belongsTo(DeliverableVersion::class, 'deliverable_version_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
