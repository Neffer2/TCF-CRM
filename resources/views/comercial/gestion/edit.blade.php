@extends('layouts.comercial.main')
  @section('content')    
    <div class="card card-frame">
      <div class="card-body">
        @livewire('com.gestion-comercial.edit', ['leadId' => $leadId])
      </div> 
    </div>
  @endsection
  
