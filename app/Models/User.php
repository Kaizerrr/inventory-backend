<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\CanResetPassword;


use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{


    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'last_login_at',
        'role_id'
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

    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    public function hasPermission($privilege)
    {
        // Retrieve the user's role
        $role = $this->role;

        if ($role) {
            // Check if the role has the specified permission
            return $role->privileges->contains('name', $privilege);
        }

        return false;
    }

    /**
     * Get all the logs for the user.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    /**
     * Get the last action done by the user.
     */
    public function lastLog()
    {
        return $this->logs()->latest()->first();
    }

    public function latestLogs()
    {
        return $this->logs()->with('student')->latest()->take(100);
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email;
    }
}
