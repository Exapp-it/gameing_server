<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class Withdraw extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "withdraws";
    protected $guarded = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
