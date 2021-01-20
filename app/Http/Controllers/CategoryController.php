<?php

namespace App\Http\Controllers;

use App\Category;
use App\Image;
use App\Product;
use App\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function foo\func;


class CategoryController extends Controller
{
    public function __construct()
    {
        $this->helper = new HelperController();
    }

    public function index()
    {

        $categories = Category::orderBy('ranking')->get();
        return view('setttings.category.create')->with([
            'categories' => $categories
        ]);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Category::where('title', $request->cat_title)->exists()) {
                $category = Category::where('title', $request->cat_title)->first();
                $category->title = $request->cat_title;
                $highest_ranking = Category::max('ranking');
                $category->ranking = $highest_ranking + 1;
                $category->save();

                $woocommerce = $this->helper->getWooCommerceAdminShop();
                $response = $woocommerce->put('products/categories/'.$category->woocommerce_id, ['name' => $category->title]);

            } else {
                $category = new Category();
                $category->title = $request->cat_title;
                $highest_ranking = Category::max('ranking');
                $category->ranking = $highest_ranking + 1;
                $category->save();

                $woocommerce = $this->helper->getWooCommerceAdminShop();
                $response = $woocommerce->post('products/categories', ['name' => $category->title]);
                $category->woocommerce_id = $response->id;
                $category->save();
            }
            DB::commit();
            return redirect()->back()->with('success','Category Saved successfully!');
        }
        catch(\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $category = Category::find($id);
            if ($request->hasFile('icon')) {
                $image =  $request->file('icon');
                $destinationPath = 'categories-icons/';
                $filename = now()->format('YmdHi') . str_replace([' ','(',')'], '-', $image->getClientOriginalName());
                $image->move($destinationPath, $filename);
                $category->icon = $filename;
            }
            $category->title = $request->title;
            if(Category::where('ranking', $request->ranking)->exists()) {
                $temp_category = Category::where('ranking', $request->ranking)->first();
                $temp_category->ranking = $category->ranking;
                $temp_category->save();
            }
            $category->ranking = $request->ranking;
            $category->save();

            $woocommerce = $this->helper->getWooCommerceAdminShop();
            $response = $woocommerce->put('products/categories/'.$category->woocommerce_id, ['name' => $category->title]);

            DB::commit();
            return redirect()->back()->with('success','Category updated successfully!');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try{
            $category = Category::find($id);
            $deleted_category_ranking = $category->ranking;

            if(Category::max('ranking') != $deleted_category_ranking) {
                $categories = Category::where('ranking', '>', $deleted_category_ranking)->get();
                foreach ($categories as $c) {
                    $c->ranking = $c->ranking - 1;
                    $c->save();
                }
            }

            $woocommerce = $this->helper->getWooCommerceAdminShop();
            $woocommerce->delete('products/categories/'. $category->woocommerce_id, ['force' => true]);

            $category->delete();
            $subcategories = SubCategory::where('category_id', $id)->get();
            foreach ($subcategories as $subcategory) {
                $subcategory->delete();
            }

            DB::commit();
            return redirect()->back()->with('success','Category Deleted!');
        }
        catch(\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function subsave(Request $request)
    {

        DB::beginTransaction();
        try{
            foreach ($request->sub_title as $sub) {
                if (!empty($sub)) {
                    $subcategory = new SubCategory();
                    $subcategory->title = $sub;
                    $subcategory->category_id = $request->category_id;
                    $subcategory->save();

                    $woocommerce = $this->helper->getWooCommerceAdminShop();
                    $response = $woocommerce->post('products/categories', ['name' => $subcategory->title, 'parent' => $subcategory->hasCategory->woocommerce_id]);
                    $subcategory->woocommerce_id = $response->id;
                    $subcategory->save();
                }
            }
            DB::commit();
            return redirect()->back()->with('success','Sub Category created successfully!');
        }
        catch(\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function subupdate(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $category = SubCategory::find($id);
            $category->title = $request->title;
            $category->save();

            $woocommerce = $this->helper->getWooCommerceAdminShop();
            $response = $woocommerce->put('products/categories/'.$category->woocommerce_id, ['name' => $category->title]);

            DB::commit();
            return redirect()->back()->with('success','Sub Category updated successfully!');
        }
        catch(\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function subdelete($id)
    {
        DB::beginTransaction();
        try{
            $category = SubCategory::find($id);

            $woocommerce = $this->helper->getWooCommerceAdminShop();
            $woocommerce->delete('products/categories/'. $category->woocommerce_id, ['force' => true]);

            $category->delete();

            DB::commit();
            return redirect()->back()->with('success','Deleted!');
        }
        catch(\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update_image_position(Request $request){
        $positions = $request->input('positions');
        $categories = $request->input('category');
        $images_array = [];


        foreach ($positions as $index => $position){
            $category = Category::where('id',$position)->first();
            $category->ranking = $index + 1;
            $category->save();
        }

        return response()->json([
            'message' => 'success',
        ]);

    }

    public function getSubCategories($title) {
        $category = Category::where('title', $title)->first();
        $subcategories = $category->hasSub;

        return view('products.sub-categories')->with('sub_categories', $subcategories)->render();
    }
}
