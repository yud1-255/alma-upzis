<?php

namespace App\Http\Controllers;

use App\Domains\ResidenceDomain;
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

        $domain = new ResidenceDomain();
        $blockNumbers = $domain->getBlockNumberOptions();
        $houseNumbers = $domain->getHouseNumbers();

        return Inertia::render('Family/Create', [
            'family' => $family,
            'muzakkis' => $muzakkis,
            'blockNumbers' => $blockNumbers,
            'houseNumbers' => $houseNumbers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'head_of_family' => ['required'],
            'phone' => ['required']
        ]);

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
        $request->validate([
            'head_of_family' => ['required'],
            'phone' => ['required'],
            'bpi_block_no' => ['required_if:is_bpi,1'],
            'bpi_house_no' => ['required_if:is_bpi,1']
        ]);

        $formData = $request->only($family->getFillable());
        $family->fill($formData);

        $family->save();
        return Redirect::back();
    }

    public function assign($id)
    {
        $family = Family::find($id);

        if ($family != null) {
            $domain = new ZakatDomain(Auth::user());
            $domain->registerFamily(Auth::user(), $family);
        }

        return Redirect::route('family.create');
    }

    public function search(Request $request)
    {
        $domain = new ResidenceDomain();
        $search = $request->search;

        // json
        return $domain->searchFamily($search);;
    }

    public function checkKkNumber(Request $request)
    {
        $kkNumber = $request->kkNumber;
        $domain = new ResidenceDomain();

        // json
        return $domain->getFamily($kkNumber);
    }
}
