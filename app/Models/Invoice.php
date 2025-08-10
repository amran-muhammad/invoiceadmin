<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['invoice_number', 'client_name','address1','address2','mobile','shop_id','items', 'total'];

    protected $casts = [
        'items' => 'array', // Store as JSON
    ];
}
