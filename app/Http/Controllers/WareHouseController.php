<?php

namespace App\Http\Controllers;

use App\Country;
use App\WareHouse;
use Illuminate\Http\Request;

class WareHouseController extends Controller
{
    public function index(Request $request) {
        $warehouses = WareHouse::paginate(30);
        $countries = Country::all();

        return view('setttings.warehouses.index')->with([
            'warehouses' => $warehouses,
            'countries' => $countries
        ]);
    }

    public function store(Request $request) {

        $this->validate($request, [
           'title' => 'required',
           'address' => 'required',
           'zip' => 'required'
        ]);

        $warehouse = new WareHouse();
        $warehouse->title = $request->title;
        $warehouse->address = $request->address;
        $warehouse->zip = $request->zip;
        $warehouse->state = $request->state;
        $warehouse->country_id = $request->country_id;
        $warehouse->save();

        return redirect()->back()->with('success', 'Warehouse Added Successfully!');
    }

    public function update(Request $request, $id) {

        $this->validate($request, [
            'title' => 'required',
            'address' => 'required',
            'zip' => 'required'
        ]);

        $warehouse = WareHouse::find($id);
        $warehouse->title = $request->title;
        $warehouse->address = $request->address;
        $warehouse->zip = $request->zip;
        $warehouse->state = $request->state;
        $warehouse->country_id = $request->country_id;
        $warehouse->save();

        return redirect()->back()->with('success', 'Warehouse Updated Successfully!');
    }

    public function delete($id) {
        $warehouse = WareHouse::find($id);
        $warehouse->delete();

        return redirect()->back()->with('success', 'Warehouse Deleted Successfully!');
    }
}
