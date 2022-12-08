<?php

namespace App\Models;

use App\Notifications\ResetPassword;
// use App\Traits\HasRolesAndPermissions;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\ScopeUserFilter;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'facebook_id',
        'facebook_token',
        'facebook_refresh_token',
        'facebook_avatar',
        'google_id',
        'google_token',
        'google_refresh_token',
        'google_avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function language()
    {
        return $this->hasMany('App\Models\UserLanguage', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'user_id');
    }

    public function tokens()
    {
        return $this->hasMany('App\Models\VerifyUser', 'user_id');
    }

    public function availableLanguage()
    {
        return $this->language()->where('language_id', '=', Language::getIdByName(app()->getLocale()));
    }
    public function getFilterScopes(): array
    {
        return [
            'id' => [
                'hasParam' => true,
                'scopeMethod' => 'id'
            ],
            'name' => [
                'hasParam' => true,
                'scopeMethod' => 'name'
            ],
            'email' => [
                'hasParam' => true,
                'scopeMethod' => 'email'
            ],
            'status' => [
                'hasParam' => true,
                'scopeMethod' => 'status'
            ],
        ];
    }


    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $url = 'https://gabi.ge/' . app()->getLocale() . '/reset-password?token=' . $token . '&email=' . $this->email;

        $this->notify(new ResetPassword($url));
    }
}
