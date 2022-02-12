<?php

namespace App\Http\Controllers;

use App\Domains\ZakatDomain;
use App\Models\Family;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use Auth;
use Session;

class FamilyController extends Controller
{
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
        // TODO move logic in create() to index()
        $family = Auth::user()->family;
        $muzakkis = [];

        if ($family == null) {
            $family = Session::get('family'); // if any from previous postback
        } else {
            $muzakkis = $family->muzakkis;
        }
        return Inertia::render('Family/Create', ['family' => $family, 'muzakkis' => $muzakkis]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $family = new Family();
        $formData = $request->only($family->getFillable());
        $family->fill($formData);

        $domain = new ZakatDomain(Auth::user());
        $domain->registerFamily(Auth::user(), $family);

        return Redirect::route('family.create')->with(['family' => $family]);
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
    public function update(Request $request, Family $family)
    {
        $formData = $request->only($family->getFillable());
        $family->fill($formData);

        $family->save();
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $families = Family::where('head_of_family', 'like', "%{$search}%");

        // json
        return $families->take(10)->get();
    }
}
