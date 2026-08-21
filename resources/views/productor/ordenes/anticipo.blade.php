@extends('layouts.productor.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
    @endsection

    @section('content')
        <div class="card" x-data="{ show: true }" x-cloak>
            <div id="accordion-anticipos">
                <div class="row px-3 pt-3 pb-0">
                    <div class="col-md-6">
                        <button :class="show ? 'btn bg-gradient-primary disabled' : 'btn bg-gradient-primary'" x-on:click="show = ! show" type="button">
                            Anticipo Jurídico
                        </button>
                        <button :class="!show ? 'btn bg-gradient-info disabled' : 'btn bg-gradient-info'" x-on:click="show = ! show" type="button">
                            Anticipo Productor
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0" id="anticipo-juridico" x-show="show" x-transition>
                @livewire('productor.ordenes.anticipo-juridico')
            </div>
            <div class="card-body pt-0" id="anticipo-productor" x-show="!show" x-transition>
                @livewire('productor.ordenes.anticipo-productor')
            </div>
        </div>
{{--        @livewire('productor.ordenes.anticipo') --}}
    @endsection
