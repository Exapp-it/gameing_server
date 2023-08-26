<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip',
        'domain'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
