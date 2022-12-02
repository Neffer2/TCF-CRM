<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComercialController extends Controller
{
    public function index (){
        return view('comercial.index');
    }

    public function show_upload (){
        return view('comercial.base.upload');
    }

    public function upload_base (Request $request){
        $request->validate([
            'base_xls' => ['required','mimetypes:xlsx, csv, xls', 'max:10000']
        ]);
        dd($request->file);
    }
}
