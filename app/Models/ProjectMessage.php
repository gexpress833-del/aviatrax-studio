<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMessage extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'sender_id', 'message', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'project_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
