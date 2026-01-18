<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\BelongsToAdministradora;

class User extends Authenticatable
{
    use HasFactory, Notifiable, BelongsToAdministradora;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'telefone',
        'password',
        'administradora_id',
        'condominio_id',
        'perfil', // Mantendo por compatibilidade enquanto migramos totalmente para Roles
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
        ];
    }

    /**
     * Relacionamento com Administradora (Tenancy principal)
     */
    public function administradora(): BelongsTo
    {
        return $this->belongsTo(Administradora::class, 'administradora_id');
    }

    /**
     * Relacionamento com Condomínio (Principalmente para Zeladores)
     */
    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    /**
     * Relacionamento com Zelador (Dados extras)
     */
    public function zelador(): HasOne
    {
        return $this->hasOne(Zelador::class);
    }

    /**
     * Roles do usuário
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Verifica se o usuário tem uma determinada role
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return !! $role->intersect($this->roles)->count();
    }

    /**
     * Verifica se o usuário tem uma permissão (via roles)
     */
    public function hasPermission($permission): bool
    {
        return $this->roles->map->permissions->flatten()->contains('name', $permission);
    }

    // Métodos de conveniência baseados na hierarquia solicitada

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->perfil === 'admin';
    }

    public function isAdministradora(): bool
    {
        return $this->hasRole('administradora') || $this->perfil === 'administradora';
    }

    public function isGerente(): bool
    {
        return $this->hasRole('gerente') || $this->perfil === 'gerente';
    }

    public function isZelador(): bool
    {
        return $this->hasRole('zelador') || $this->perfil === 'zelador';
    }

    /**
     * Scope para filtrar por administradora
     */
    public function scopeDaAdministradora($query, $id)
    {
        return $query->where('administradora_id', $id);
    }
}
