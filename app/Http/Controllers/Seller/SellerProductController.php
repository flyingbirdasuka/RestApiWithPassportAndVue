<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Seller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Transformers\ProductTransformer;
use Illuminate\Auth\Access\AuthorizationException;

class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store', 'update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;
        return $this->showAll($products); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ]; 
        $this->validate($request, $rules);

        $data = $request->all();
        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = $request->image->store('');
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);
        return $this->showOne($product);
      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'required|integer|min:1',
            'status' => 'in:' . Product::UNAVAILABLE_PRODUCT . ',' . Product::AVAILABLE_PRODUCT,
            'image' => 'image',
        ];
        $this->validate($request, $rules);
        $this->checkSeller($seller, $product);


        $product->fill($request->only([
            'name',
            'quantity',
        ]));

        if($request->has('status')){

            $product->status = $request->status;
            if($product->isAvailable() && $product->categories()->count()==0){
                return $this->errorResponse('an active product must have at least one category', 409);
            }
        }
        if($request->hasFile('image')){
            Storage::delete($product->image);
            $product->image = $request->image->store('');
        }

        if($product->isClean()){
             return $this->errorResponse('you need to specify a different value to update', 422);
        }
        $product->save();
        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);
        Storage::delete($product->image);
        $product->delete();
        return $this->showOne($product);
    }

    protected function checkSeller(Seller $seller, Product $product)
    {
        if($seller->id != $product->seller_id){
            throw new HttpException(422, 'the specified seller is not the actual seller of the product');
        }
    }
    
}
