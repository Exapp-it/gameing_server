<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "wallet_invoices";
    protected $guarded = false;
}
