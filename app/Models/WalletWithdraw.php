<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletWithdraw extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "wallet_withdraws";
    protected $guarded = false;
}
