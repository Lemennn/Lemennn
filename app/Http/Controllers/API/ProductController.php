<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function all(Request $request){
        //inputan
        $id = $request->input('id');
        $limit = $request->input('limit',6);
        $name = $request->input('id');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        //ambil data dari id
        if($id){
            $products = Product::with('category', 'galleries')->find($id);
            if($products){
                return ResponseFormatter::success(
                    $products,
                    'Data Produk Berhasil Diambil'
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data Tidak Ada',
                    400
                );
            }
        }

        //ambil seluruh data
        $products = Product::with('category', 'galleries');

        //filter data yang sudah ada
        if($name){
            $products->where('name', 'Like' , '%' . $name .'%');
        }
        if($description){
            $products->where('description', 'Like' , '%' . $description .'%');
        }
        if($tags){
            $products->where('tags$tags', 'Like' , '%' . $tags .'%');
        }
        if($categories){
            $products->where('categories_id', $categories);
        }
        
        if($price_from){
            $products->where('price', '>=' , $price_from);
        }
        if($price_to){
            $products->where('price', '<=' , $price_to);
        }

        //tampil data
        return ResponseFormatter::success(
            $products->paginate($limit),
            'Data Berhasil Diambil'
        );
    }
}
