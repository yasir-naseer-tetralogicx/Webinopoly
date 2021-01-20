<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    private $helper;

    public function __construct()
    {
        $this->helper = new HelperController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('setttings.tags.index')->with('tags', Tag::paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:tags'
        ]);


        DB::beginTransaction();
        try {
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->save();

            $woocommerce = $this->helper->getWooCommerceAdminShop();
            $response = $woocommerce->post('products/tags', ['name' => $tag->name]);
            $tag->woocommerce_id = $response->id;
            $tag->save();

            DB::commit();
            return redirect()->back()->with('success', 'Tag Created Successfully!');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);

        DB::beginTransaction();
        try{
            $woocommerce = $this->helper->getWooCommerceAdminShop();
            $woocommerce->delete('products/tags/'.$tag->woocommerce_id, ['force' => true]);

            $tag->products()->detach();
            $tag->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Tag deleted Successfully');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

