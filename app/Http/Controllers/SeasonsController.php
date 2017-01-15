<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Season;

class SeasonsController extends Controller
{
    //

    public function create () {

        return view('/seasons/create');

    }

    public function store (Request $request) {

        $this->validate($request, [
            'name'      => 'required | string'
        ]);
        $season = Season::create(Input::all());
        return view('/grand_prixs/create')->withSeason($season->id);
    }
}
