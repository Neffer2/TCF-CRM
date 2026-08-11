<?php

namespace App\Http\Livewire\Com\GestionComercial\Contactos;

use Livewire\Component;
use App\Models\Contacto;
use App\Models\clientes;
use App\Models\EstadoGestionComercial;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ListaContactos extends Component
{
    use WithPagination; // Habilita la paginación de Livewire

    protected $paginationTheme = 'bootstrap'; // Usa el tema bootstrap para la paginación

    public $listaClientes; // Variable para almacenar la lista de clientes

    // Useful vars
    protected $listeners = ['list' => 'render']; // Escucha el evento 'list' para recargar la lista

    public function mount()
    {
        // Trae los clientes de la BD (solo id y nombre para optimizar memoria)
        $this->listaClientes = clientes::select('id', 'nombre')->get();
    }

    // Renderiza la vista principal del componente de contactos
    public function render()
    {
        // Obtiene los contactos del usuario autenticado, ordenados por id descendente y paginados
        $contactos = Contacto::select('id','nombre','apellido','empresa','cargo','correo','celular','web', 'direccion', 'pbx','id_cliente')
            ->where('id_user', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Retorna la vista con los contactos paginados
        return view('livewire.com.gestion-comercial.contactos.lista-contactos', ['contactos' => $contactos]);
    }
}
