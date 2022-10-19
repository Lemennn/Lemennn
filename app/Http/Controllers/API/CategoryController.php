<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function all(Request $request){
        //inputan
        $id = $request->input('id');
        $name = $request->input('nama');
        $limit = $request->input('limit');
        $show_product = $request->input('show_product');

        //query->id
        if($id){
            $category = ProductCategory::with('products')->find($id);
            if($category){
                return ResponseFormatter::success(
                    $category,
                    'Data berhasil di ambil'
                );
            }else{
                return ResponseFormatter::error(
                    $category,
                    'Data tidak ada'
                );
            }
        }

        $category = ProductCategory::with('products');

        if($name){
            $category->where('name', 'Like' , '%' . $name .'%');
        }
        if($show_product){
            $category->with('products');
        }

        return ResponseFormatter::success(
            $category->paginate($limit),
            'Data berhasil dia ambil'
        );

    }
}
