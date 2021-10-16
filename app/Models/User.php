<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\LogUser;

class User extends Authenticatable
{
    use HasApiTokens, LogUser, HasRoles, Notifiable;

    protected $guard_name = 'sanctum';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username'
        ,'nip'
        ,'jabatan_id'
        ,'position_id'
        ,'email'
        ,'password'
        ,'full_name'
        ,'tgl_lahir'
        ,'ttd'
        ,'jenis_kelamin'
        ,'path_foto'
        ,'password'
        ,'phone'
        ,'address'
        ,'active'
        ,'created_by'
        ,'updated_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'ttl' => 'datetime:Y-m-d',
        'email_verified_at' => 'datetime',
    ];

    public function getPermissionAttribute()
    {
        return $this->getAllPermissions();
    }

    public function getPegawaiId()
    {
        return $this->attributes['pegawai_id'];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }
}