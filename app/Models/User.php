<?php

namespace App\Models;

use App\Models\AffiliateProgram;
use App\Models\UserBonus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Withdraw;
use App\Models\Payment;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $table = "users";
    protected $guarded = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'patronymic',
        'role',
        'birth_date',
        'balance',
        'login',
        'email',
        'referral_code',
        'fingerprint',
        'password',
        'gender',
        'phone',
        'address',
        'currency',
        'country',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'role',
        'email_verified_at',
        'fingerprint',
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

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function bonus()
    {
        return $this->hasMany(UserBonus::class);
    }

    public function scopeGetReferralCode($query, $userId)
    {
        return $query->where('id', $userId)->whereNotNull('referral_code');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referral_id', 'id');
    }

    public function referralClicks()
    {
        return $this->hasMany(ReferralClick::class, 'user_id', 'id');
    }

    public function affiliateProgram()
    {
        return $this->hasOne(AffiliateProgram::class, 'user_id');
    }

    public function referredAffiliatePrograms()
    {
        return $this->hasMany(AffiliateProgram::class, 'referral_id');
    }
}
