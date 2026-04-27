<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nik',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The participant profile linked to this user account (peserta only).
     * One user has at most one participant record.
     */
    public function participantProfile(): HasOne
    {
        return $this->hasOne(Participant::class, 'user_id');
    }

    /**
     * Participants assigned to this admin for oversight.
     * Only meaningful when role = 'admin'.
     */
    public function assignedParticipants(): HasMany
    {
        return $this->hasMany(Participant::class, 'assigned_admin_id');
    }

    /**
     * Locations created by this admin.
     * Only meaningful when role = 'admin'.
     */
    public function createdLocations(): HasMany
    {
        return $this->hasMany(Location::class, 'created_by');
    }

    /**
     * Activity logs recording actions by this admin.
     * Only meaningful when role = 'admin'.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a peserta.
     */
    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }
}
