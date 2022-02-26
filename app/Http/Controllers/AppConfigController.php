<?php

namespace App\Http\Controllers;

use App\Models\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use Auth;

class AppConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->cannot('view', new AppConfig())) {
            abort(403);
        }

        return Inertia::render('AppConfig/Index', [
            'appConfigs' => AppConfig::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AppConfig  $appConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AppConfig $appConfig)
    {
        if (Auth::user()->cannot('update', $appConfig)) {
            abort(403);
        }
        $appConfig->fill($request->only($appConfig->getFillable()));
        $appConfig->save();
        return Redirect::back();
    }
}
