<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Seller;

class SellerController extends ApiController
{
    public function __construct()
    {
          $this->middleware('client.credentials:')->only(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::has('products')->get();
        return $this->showAll($sellers);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
        return $this->showOne($seller);
    }

}
