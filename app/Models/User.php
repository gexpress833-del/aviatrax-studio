<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'stage_name',
        'email',
        'phone',
        'telephone',
        'password',
        'status',
        'statut',
        'avatar_url',
        'photo_path',
        'role_id',
        'responsable_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'string',
        'statut' => 'string',
    ];

    public static function isCloudinaryConfigured(): bool
    {
        $cloud = config('filesystems.disks.cloudinary.cloud');
        $key = config('filesystems.disks.cloudinary.key');
        $secret = config('filesystems.disks.cloudinary.secret');

        if (! $cloud || ! $key || ! $secret) {
            return false;
        }

        return ! str_contains(strtolower((string) $cloud), 'your_')
            && ! str_contains(strtolower((string) $key), 'your_')
            && ! str_contains(strtolower((string) $secret), 'your_');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (blank($this->photo_path)) {
            return null;
        }

        $photoPath = (string) $this->photo_path;

        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            return $photoPath;
        }

        if (self::isCloudinaryConfigured()) {
            $cloudName = config('filesystems.disks.cloudinary.cloud');
            $publicId = ltrim($photoPath, '/');

            return 'https://res.cloudinary.com/' . $cloudName . '/image/upload/' . $publicId;
        }

        return null;
    }

    /**
     * Relation avec le rôle.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Projets où l'utilisateur est l'artiste.
     */
    public function projetsArtiste()
    {
        return $this->hasMany(Projet::class, 'artiste_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'artist_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()->active()->latest('ends_at');
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()->active()->exists();
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'studio_manager_id');
    }

    /**
     * Projets où l'utilisateur est le responsable studio.
     */
    public function projetsResponsable()
    {
        return $this->hasMany(Projet::class, 'responsable_id');
    }

    /**
     * Responsable studio de l'artiste.
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Artistes affectés à ce responsable studio.
     */
    public function artistesAffectes()
    {
        return $this->hasMany(User::class, 'responsable_id');
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique.
     */
    public function hasRole($roleName)
    {
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }

    public function isAdmin()
    {
        return $this->hasRole('administrateur');
    }

    public function isSecretariat()
    {
        return $this->hasRole('secretariat');
    }

    public function isResponsableStudio()
    {
        return $this->hasRole('responsable_studio');
    }

    public function isArtiste()
    {
        return $this->hasRole('artiste');
    }

    public function isSecretaire()
    {
        return $this->hasRole('secretariat');
    }

    public function canManageArtistes()
    {
        return in_array($this->role?->name, ['administrateur', 'secretariat', 'responsable_studio'], true);
    }

    /**
     * Vérifie si le compte est validé.
     */
    public function isValide()
    {
        return $this->status === 'valide' || $this->statut === 'valide';
    }

    public function isPendingValidation()
    {
        return $this->status === 'en_attente_validation' || $this->statut === 'en_attente_validation';
    }

    public function isSuspended()
    {
        return $this->status === 'suspendu' || $this->statut === 'suspendu';
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function projectComments()
    {
        return $this->hasMany(ProjectComment::class);
    }

    public function projectFiles()
    {
        return $this->hasMany(ProjectFile::class, 'uploaded_by');
    }
}