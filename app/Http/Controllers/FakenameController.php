<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FakenameController extends Controller
{
    public function create()
    {
        if(! auth()->user()) {
            return redirect()->route('login');
        }

        return view('fakename');
    }

    public function store(Request $request)
    {
        $request->user()->update([
            'fakename' => $request['fakename']
        ]);

        return redirect()->route('home')->with(['success' => 'ياهلا ومرحبا، وانتبه لحد يدري عن اللقب']);
    }
}
