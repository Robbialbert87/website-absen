<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\Filterable;

#[Fillable(['name', 'username', 'nip', 'password', 'pegawai_id', 'ruangan_id', 'password_changed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, Filterable;

    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin');
    }

    /**
     * Pegawai biasa = hanya memiliki role 'user', bukan admin/super_admin/kepala_ruangan/staff.
     * User ini login via NIP dan hanya dapat mengakses menu Absensi Kegiatan.
     */
    public function isPegawaiBiasa(): bool
    {
        $privilegedRoles = ['admin', 'super_admin', 'kepala_ruangan', 'staff'];
        foreach ($privilegedRoles as $role) {
            if ($this->hasRole($role)) {
                return false;
            }
        }
        return true;
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
