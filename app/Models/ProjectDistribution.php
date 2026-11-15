<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDistribution extends Model
{
    use HasFactory;

    protected $table = 'project_distributions';

    protected $fillable = [
        'project_id',
        'platform_name',
        'platform_url',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
