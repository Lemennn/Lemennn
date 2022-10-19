<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected  $fillable = [
        'users_id',
        'products_id',
        'transactions_id',
        'quantity'
    ];

    // public function user(){
    //     $this->belongsTo(User::class, 'users_id', 'id');
    // }

    // public function transaction(){
    //     $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    // }

    public function product(){
        return $this->hasOne(Product::class, 'id' , 'products_id');
    }
}
