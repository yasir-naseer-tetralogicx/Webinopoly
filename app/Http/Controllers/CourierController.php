<?php

namespace App\Http\Controllers;

use App\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $couriers = Courier::latest()->paginate(20);
        return view('setttings.couriers.index')->with('couriers', $couriers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
           'title' => 'required|unique:couriers'
        ]);
        $courier = new Courier();
        $courier->title =  $request->title;
        $courier->url =  $request->url;
        $courier->save();

        return redirect()->back()->with('success', 'Courier Service Provider Added Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Courier  $courier
     * @return \Illuminate\Http\Response
     */
    public function show(Courier $courier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Courier  $courier
     * @return \Illuminate\Http\Response
     */
    public function edit(Courier $courier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Courier  $courier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Courier $courier)
    {
        $this->validate($request, [
            'title' => 'required|unique:couriers,title,'.$courier->id
        ]);

        $courier->title =  $request->title;
        $courier->url =  $request->url;
        $courier->save();

        return redirect()->back()->with('success', 'Courier Service Provider Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Courier  $courier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Courier $courier)
    {
        $courier->delete();

        return redirect()->back()->with('success', 'Courier Service Provider Deleted Successfully!');

    }
}
