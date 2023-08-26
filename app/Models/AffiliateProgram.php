<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateProgram extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'referral_id',
        'amount',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }
}
