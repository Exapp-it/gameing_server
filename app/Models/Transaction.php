<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Game;
use App\Models\GameSession;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "transactions";
    protected $guarded = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function gameSession()
    {
        return $this->belongsTo(GameSession::class);
    }
}
