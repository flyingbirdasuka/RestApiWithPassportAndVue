<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    public function __construct()
    {   
        $this->middleware('client.credentials')->only(['index']);    
        $this->middleware('auth"api')->except(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;
        return $this->showAll($product->categories);
    }

    //set the relationship
    public function update(Request $request, Product $product, Category $category){
        $product->categories()->syncWithoutDetaching([$category->id]);
        return $this->showAll($product);
        
    }

    //remove the relationship
    public function destroy(Product $product, Category $category){
        if(!$product->categories()->find($category->id)){
            return $this->errorResponse('The spscified category is not a category of this product', 404);
        }
        $product->categories()->detach($category->id);
        return $this->showAll($product->categories);
    }
}

