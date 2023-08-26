<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class GameSession extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "game_sessions";
    protected $guarded = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
