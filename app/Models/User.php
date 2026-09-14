<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'telefono',
        'rol',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user_rol(){
        return $this->hasOne(Rol::class, 'id', 'rol');
    }

    // Roles que esta cuenta PUEDE usar (users.rol es el rol ACTIVO).
    // Permite que una persona tenga varios roles sin cuentas duplicadas.
    public function roles(){
        return $this->belongsToMany(Rol::class, 'role_user', 'user_id', 'rol_id')->withTimestamps();
    }

    public function puedeUsarRol($rolId): bool
    {
        return $this->roles()->where('roles_user.id', $rolId)->exists();
    }

    /* Roles del espacio administrativo (comparten el layout de admin) */
    const ROL_ADMIN = 1;
    const ROL_CONTROLLER = 11;
    const ROL_LIDER_COMERCIAL = 12;
    const ROL_GERENCIA = 20;

    /** Controller: revisión financiera de presupuestos, actualizaciones, consumidos y reportes. */
    public function esControl(): bool
    {
        return (int) $this->rol === self::ROL_CONTROLLER;
    }

    /** Líder comercial: dashboard, validaciones y presupuestos de su equipo (lider_comercial_user). */
    public function esLiderComercial(): bool
    {
        return (int) $this->rol === self::ROL_LIDER_COMERCIAL;
    }

    /** Admin, Gerencia, Controller o Líder comercial: usan el layout y las pantallas de administración. */
    public function espacioAdmin(): bool
    {
        return in_array((int) $this->rol, [self::ROL_ADMIN, self::ROL_GERENCIA, self::ROL_CONTROLLER, self::ROL_LIDER_COMERCIAL], true);
    }

    /** Layout (menú lateral) que corresponde al rol activo. */
    public function layoutRol(): string
    {
        if ($this->espacioAdmin()) { return 'layouts.admin.main'; }
        switch ((int) $this->rol) {
            case 2: return 'layouts.comercial.main';
            case 5: return 'layouts.asistente.main';
            case 6: return 'layouts.lider-produccion.main';
            case 7: return 'layouts.productor.main';
            case 8: return 'layouts.tesoreria.main';
            case 3: case 9: return 'layouts.contabilidad.main';
            default: return 'layouts.admin.main';
        }
    }

    /** Cargo visible: Gerencia si tiene ese rol (nunca es el activo), si no el rol activo. */
    public function cargo(): string
    {
        if ($this->puedeUsarRol(self::ROL_GERENCIA)) { return 'Gerencia'; }
        return optional($this->user_rol)->description ?: 'Usuario';
    }

    /** URL pública del avatar (o null si es el genérico / no existe). */
    public function avatarUrl(): ?string
    {
        if (!$this->avatar || str_ends_with($this->avatar, 'avatar.jpg')) { return null; }
        return asset('storage/'.str_replace('public/', '', $this->avatar));
    }

    /** Puede revisar/aprobar presupuestos con centro de costos (Admin y Controller). */
    public function revisaPresupuestos(): bool
    {
        return in_array((int) $this->rol, [self::ROL_ADMIN, self::ROL_GERENCIA, self::ROL_CONTROLLER], true);
    }

    public function asistente(){
        return $this->hasMany(Asistente::class, 'comercial_id', 'id');
    }

    public function comercialesAsignados()
    {   
        // Parámetros: Modelo Destino, Tabla Pivote, FK Local (lider_id), FK Destino (comercial_id)
        return $this->belongsToMany(User::class, 'lider_comercial_user', 'lider_id', 'comercial_id');
    }

    public function comerciales() {
        return $this->belongsToMany(
            User::class,
            'lider_comercial_user',
            'lider_id',
            'comercial_id'
        );
    }

    public function lideres() {
        return $this->belongsToMany(
            User::class,
            'lider_comercial_user',
            'comercial_id',
            'lider_id'
        );
    }
}
