<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function dashboard (){
        if (Auth::user()->rol == 1) {
            return redirect()->route('dashboard-admin');
        }elseif (Auth::user()->rol == 2){
            return redirect()->route('dashboard-com');
        }elseif (Auth::user()->rol == 3){
            return redirect()->route('dashboard-con');
        }elseif (Auth::user()->rol == 5){
            return redirect()->route('dashboard-asis');  
        } 
    }
}  
 