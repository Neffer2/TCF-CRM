<?php

namespace App\Http\Livewire\Com\GestionComercial\Contactos;

use Livewire\Component;
use App\Models\Contacto;
use App\Models\EstadoGestionComercial;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ListaContactos extends Component
{
    use WithPagination; // Habilita la paginación de Livewire

    protected $paginationTheme = 'bootstrap'; // Usa el tema bootstrap para la paginación

    // Useful vars
    protected $listeners = ['list' => 'render']; // Escucha el evento 'list' para recargar la lista

    // Renderiza la vista principal del componente de contactos
    public function render()
    {
        // Obtiene los contactos del usuario autenticado, ordenados por id descendente y paginados
        $contactos = Contacto::select('id','nombre','apellido','empresa','cargo','correo','celular','web', 'direccion', 'pbx')
            ->where('id_user', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Retorna la vista con los contactos paginados
        return view('livewire.com.gestion-comercial.contactos.lista-contactos', ['contactos' => $contactos]);
    }
}
