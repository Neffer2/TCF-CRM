<?php

namespace App\Http\Livewire\Com\GestionComercial\Clientes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\clientes;
use Illuminate\Support\Facades\Auth;

class ListaCliente extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['list' => 'render'];

    public function render(){

        $clientes = clientes::select('id', 'CodigoCLiente', 'NombreCLiente', 'ApellidoCliente', 'TipoCliente','EstadoCliente', 'RazonCliente', 'DireccionCliente', 'TelefonoCliente', 'EmailCliente')
            ->where('id_user', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.com.gestion-comercial.clientes.lista-clientes', compact('clientes'));

    }
}
