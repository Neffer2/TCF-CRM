@extends('layouts.admin.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
    @endsection

    @section('content')     
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header p-0 mx-3 my-3 position-relative z-index-1 col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="mb-0">Items consumidos</h3>
                                <p class="text-sm mb-0">Items consumidos en la orden de compra. Presupuesto completo <a href="#">aqu&iacute;.</a></p>
                            </div>
                        </div>
                    </div>  
                    <div class="card-body rounded table-responsive bg-whitem p-0 mx-2">
                        <table class="table m-0">
                            <thead> 
                                <tr>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">ITEM</th>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">CANTIDAD</th>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DIA</th>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">OTROS</th>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DESCRIPCION</th>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. UNITARIO</th>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL</th>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                                    <th class="font-weight-bold font-table bg-gradient-warning text-white">UTILIDAD</th>
            
                                    <th class="font-weight-bold font-table bg-gradient-success text-white">MES</th>
                                    <th class="font-weight-bold font-table bg-gradient-success text-white">DIAS</th>
                                    <th class="font-weight-bold font-table bg-gradient-success text-white">CIUDAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orden->ordenItems as $key => $item)                
                                    <tr> 
                                        <td class="font-weight-bold font-table">
                                            {{ $item->display_item }}
                                        </td> 
                                        <td class="font-weight-bold font-table">
                                            {{ $item->itemPresupuesto->cantidad }}
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            {{ $item->itemPresupuesto->dia }}
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            {{ $item->itemPresupuesto->otros }}
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            <textarea name="" id="" cols="30" rows="1" readonly>{{ $item->itemPresupuesto->descripcion }}</textarea>
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            $ {{ number_format($item->itemPresupuesto->v_unitario) }}
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            $ {{ number_format($item->itemPresupuesto->v_total) }}
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            {{ $item->itemPresupuesto->proveedor }} 
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            {{ $item->itemPresupuesto->margen_utilidad }}
                                        </td>
            
                                        <td class="font-weight-bold font-table">
                                            {{ $item->itemPresupuesto->mesDescription->description }} 
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            {{ $item->itemPresupuesto->dias }}
                                        </td>
                                        <td class="font-weight-bold font-table">
                                            {{ $item->itemPresupuesto->ciudad }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div>
            <div class="col-md-12">
                @livewire('productor.ordenes.juridica', ['presupuesto' => $presupuesto, 'orden_compra' => $orden], key("juridica{{ $presupuesto->id }}".$orden->id))
            </div>
        </div>
    @endsection  