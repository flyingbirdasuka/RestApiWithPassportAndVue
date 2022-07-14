<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Transformers\CategoryTransformer;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $transformer = CategoryTransformer::class;

    // for SOftDeletes
    protected $dates = ['deleted_at']; 

    protected $fillable = [
    	'name',
    	'description',
    ];

    protected $hidden = [
        'pivot'
    ];
    
    public function products(){
    	return $this->belongsToMany(Product::class);
    }
}
