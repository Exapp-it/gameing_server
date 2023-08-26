<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\Game;

class Winner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'winners';
    protected $guarded = false;

    protected $casts = array(
        "demo"   => "boolean",
        "amount" => "float",
        "time"   => "datetime",
    );

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
