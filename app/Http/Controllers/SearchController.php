<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Requests\SearchRequest;

class SearchController extends Controller
{
    public function index(){
    	return view('index');
    }

    public function search(SearchRequest $request){
    	$city = trim($request->q);
    	if($city == '' ){
    		return response()->json(['cities' => [], 'products' => []]);
    	}

    	$products = Product::where('city', 'LIKE', "%". $city ."%")->get();

    	$cities = $products->unique('city')->pluck('city');
    	return response()->json(['cities' => $cities, 'products' => $products]);
    }
}
