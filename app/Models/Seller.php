<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;
use App\Scopes\SellerScope;
use App\Transformers\SellerTransformer;

class Seller extends User
{
    use HasFactory;
	public $transformer = SellerTransformer::class;

    protected static function boot(){
    	parent::boot();
    	static::addGlobalScope(new SellerScope);
    }
     
    public function products(){
    	return $this->hasMany(Product::class);
    }
}
