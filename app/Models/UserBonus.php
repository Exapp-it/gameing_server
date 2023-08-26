<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonus_id',
        'user_id',
        'provider',
        'win',
        'round_win',
        'round_id',
        'count',
        'played',
        'start',
        'end',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'start' => 'datetime',
        'end' => 'datetime',
    ];



    public function user()
    {
        return $this->BelongsTo(User::class);
    }
}
