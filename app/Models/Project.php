<?php

namespace App\Models;

use App\Enums\ProjectStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'title',
        'slug',
        'artist_id',
        'studio_manager_id',
        'project_type',
        'current_stage',
        'release_date',
        'distribution_status',
        'start_date',
        'expected_end_date',
        'cover_art_url',
        'isrc_code',
        'upc_code',
        'description',
    ];

    protected $casts = [
        'current_stage' => ProjectStage::class,
        'release_date' => 'date',
        'start_date' => 'date',
        'expected_end_date' => 'date',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function studioManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'studio_manager_id');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(ProjectDistribution::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectComment::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function isDistributionOnly(): bool
    {
        return $this->project_type === 'distribution_only';
    }
}
