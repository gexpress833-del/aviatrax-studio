<?php

namespace App\Models;

use App\Enums\ProjectStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFile extends Model
{
    use HasFactory;

    protected $table = 'project_files';

    protected $fillable = [
        'project_id',
        'uploaded_by',
        'stage',
        'file_name',
        'file_url',
        'public_id',
        'resource_type',
        'version',
        'file_size',
    ];

    protected $casts = [
        'stage' => ProjectStage::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
