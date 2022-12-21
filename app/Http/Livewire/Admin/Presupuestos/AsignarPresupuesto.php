<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\models\Año;
use App\models\User;
use App\models\Presupuesto;
use App\models\Mes;
use Illuminate\Validation\Rules;

class AsignarPresupuesto extends Component
{
    // models
    public $comercialesModel;
    public $añosModel;
    public $comercialName;
    public $añoDescription;

    public $eneroModel;
    public $febreroModel; 
    public $marzoModel;
    public $abrilModel;
    public $mayoModel;
    public $junioModel;
    public $julioModel;
    public $agostoModel;
    public $septiembreModel;
    public $octubreModel;
    public $noviembreModel;
    public $diciembreModel;

    // Help vars
    

    public function render()
    {
        return view('livewire.admin.presupuestos.asignar-presupuesto');
    }

    public function mount(){
        $this->getModels();
    }

    public function getModels (){
        $this->comercialesStored = User::select('id', 'name', 'rol')->where('rol', 2)->get();
        $this->añosStored = Año::select('id', 'description')->get();
    }

    public function getPresupuestoStored (){
        $this->validate([
            'comercialesModel' => 'required',
            'añosModel' => 'required'
        ]);

        $presupuestoStored = Presupuesto::select('id', 'valor', 'ano_id', 'id_user')
                                    ->where('ano_id', $this->añosModel)
                                    ->where('id_user', $this->comercialesModel)
                                    ->get(); 
                                    
        $this->comercialName = User::select('name')->where('id', $this->comercialesModel)->first()->name;
        $this->añoDescription = Año::select('description')->where('id', $this->añosModel)->first()->description;

        if (!$presupuestoStored->isEmpty()){
            $this->eneroModel = $presupuestoStored[0]->valor;
            $this->febreroModel = $presupuestoStored[1]->valor;
            $this->marzoModel = $presupuestoStored[2]->valor;
            $this->abrilModel = $presupuestoStored[3]->valor;
            $this->mayoModel = $presupuestoStored[4]->valor;
            $this->junioModel = $presupuestoStored[5]->valor;
            $this->julioModel = $presupuestoStored[6]->valor;
            $this->agostoModel = $presupuestoStored[7]->valor;
            $this->septiembreModel = $presupuestoStored[8]->valor;
            $this->octubreModel = $presupuestoStored[9]->valor;
            $this->noviembreModel = $presupuestoStored[10]->valor;
            $this->diciembreModel = $presupuestoStored[11]->valor;
        }else {
            $this->limpiar(true);
        }                              
    }
    
    public function updatePresupuestos() {
        $this->validate([
            'eneroModel' => ['required', 'numeric'],
            'febreroModel' => ['required', 'numeric'],
            'marzoModel' => ['required', 'numeric'],
            'abrilModel' => ['required', 'numeric'],
            'mayoModel' => ['required', 'numeric'],
            'junioModel' => ['required', 'numeric'],
            'julioModel' => ['required', 'numeric'],
            'agostoModel' => ['required', 'numeric'],
            'septiembreModel' => ['required', 'numeric'],
            'octubreModel' => ['required', 'numeric'],
            'noviembreModel' => ['required', 'numeric'],
            'diciembreModel' => ['required', 'numeric'],
            'añosModel' => ['required'],
            'comercialesModel' => ['required']
        ]);
        
        $presupuestosXmes = Presupuesto::where('ano_id', $this->añosModel)->where('id_user', $this->comercialesModel)->get();
        if (!$presupuestosXmes->isEmpty()){
            foreach ($presupuestosXmes as $presMes){
                switch ($presMes->presupuesto_mes->identifier){
                    case 1:
                        $presMes->valor = $this->eneroModel;
                    break;
                    case 2:
                        $presMes->valor = $this->febreroModel;
                    break;
                    case 3:
                        $presMes->valor = $this->marzoModel;
                    break;
                    case 4:
                        $presMes->valor = $this->abrilModel;
                    break;
                    case 5:
                        $presMes->valor = $this->mayoModel;
                    break;
                    case 6:
                        $presMes->valor = $this->junioModel;
                    break;
                    case 7:
                        $presMes->valor = $this->julioModel;
                    break;
                    case 8:
                        $presMes->valor = $this->agostoModel;
                    break;
                    case 9:
                        $presMes->valor = $this->septiembreModel;
                    break;
                    case 10:
                        $presMes->valor = $this->octubreModel;
                    break;
                    case 11:
                        $presMes->valor = $this->noviembreModel;
                    break;
                    case 12:
                        $presMes->valor = $this->diciembreModel;
                    break;
                }
                $presMes->update();
            }
        }else { 
            $meses = Mes::select('id', 'identifier')->where('ano_id', $this->añosModel)->get();            
            foreach ($meses as $mes){
                $newPresupuesto = new Presupuesto;
                $newPresupuesto->ano_id = $this->añosModel;
                $newPresupuesto->mes_id = $mes->id;
                
                switch ($mes->identifier){
                    case 1:
                        $newPresupuesto->valor = $this->eneroModel;
                    break;
                    case 2:
                        $newPresupuesto->valor = $this->febreroModel;
                    break;
                    case 3:
                        $newPresupuesto->valor = $this->marzoModel;
                    break;
                    case 4:
                        $newPresupuesto->valor = $this->abrilModel;
                    break;
                    case 5:
                        $newPresupuesto->valor = $this->mayoModel;
                    break;
                    case 6:
                        $newPresupuesto->valor = $this->junioModel;
                    break;
                    case 7:
                        $newPresupuesto->valor = $this->julioModel;
                    break;
                    case 8:
                        $newPresupuesto->valor = $this->agostoModel;
                    break;
                    case 9:
                        $newPresupuesto->valor = $this->septiembreModel;
                    break;
                    case 10:
                        $newPresupuesto->valor = $this->octubreModel;
                    break;
                    case 11:
                        $newPresupuesto->valor = $this->noviembreModel;
                    break;
                    case 12:
                        $newPresupuesto->valor = $this->diciembreModel;
                    break;
                }

                $newPresupuesto->id_user = $this->comercialesModel;
                $newPresupuesto->save();
            }
        }
        $this->limpiar();

        return redirect()->back()->with('success', 'Presupuestos asignados exitosamente!'); 
    }

    public function limpiar($except = false){
        $this->eneroModel = "";
        $this->febreroModel = "";
        $this->marzoModel = "";
        $this->abrilModel = "";
        $this->mayoModel = "";
        $this->junioModel = "";
        $this->julioModel = "";
        $this->agostoModel = "";
        $this->septiembreModel = "";
        $this->octubreModel = "";
        $this->noviembreModel = "";
        $this->diciembreModel = "";
        if (!$except){
            $this->comercialesModel = "";
            $this->añosModel = "";
            $this->comercialName = "";
            $this->añoDescription = "";
        }
    }
}
