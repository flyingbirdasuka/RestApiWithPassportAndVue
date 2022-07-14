<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Buyer;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Transformers\TransactionTransformer;


class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $transformer = TransactionTransformer::class;
    protected $fillable = [
		'quantity',
		'buyer_id',
		'product_id',
    ];

    protected $dates = ['deleted_at'];
    
    public function product(){
    	return $this->belongsTo(Product::class);
    }

    public function buyer(){
    	return $this->belongsTo(Buyer::class);
    }
}
