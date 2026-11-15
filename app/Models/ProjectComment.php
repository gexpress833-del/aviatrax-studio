<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectComment extends Model
{
    use HasFactory;

    protected $table = 'project_comments';

    protected $fillable = [
        'project_id',
        'user_id',
        'stage',
        'content',
        'is_approval',
    ];

    protected $casts = [
        'is_approval' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
