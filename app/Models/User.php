<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'password',
        'role', 'actif', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'actif'             => 'boolean',
        'password'          => 'hashed',
    ];

    // ─────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    // ─────────────────────────────────────────────
    // Helpers de rôle — utilisés dans les vues et controllers
    // ─────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Peut valider ou rejeter des factures.
     */
    public function peutValider(): bool
    {
        return in_array($this->role, ['comptable', 'admin', 'super_admin']);
    }

    /**
     * Lecture seule — ne peut ni uploader ni valider.
     */
    public function estAuditeur(): bool
    {
        return $this->role === 'auditeur';
    }

    /**
     * Label lisible du rôle pour l'affichage.
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin'       => 'Administrateur',
            'comptable'   => 'Comptable',
            'auditeur'    => 'Auditeur',
            default       => $this->role,
        };
    }
}
