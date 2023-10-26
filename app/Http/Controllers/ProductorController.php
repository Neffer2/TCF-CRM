<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use App\Models\PresupuestoProyecto;
use App\Models\ItemPresupuesto; 
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class ProductorController extends Controller
{   
    public function index(){
        $email = new WelcomeMail('Eduardo Campano');
        Mail::to('n3f73r@gmail.com')->send($email);
        dd("Yá");

        $proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->where('productor', Auth::id())->get();
        return view('productor.index', ['proyectos' => $proyectos]);
    }

    public function showFirmar($orden){
        return view('productor.gr.index', ['orden' => $orden]);
    }
}
 