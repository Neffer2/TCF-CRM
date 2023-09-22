@extends('layouts.comercial.main')
  @section('hero-style')
      <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
          <span class="mask bg-gradient-warning opacity-6"></span>
      </div>
      <!-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div>  -->
  @endsection 
  @section('profile-card')
    <div class="card shadow-lg mx-4 card-profile-bottom mt-5">
      <div class="card-body p-3">
        <div class="row gx-4">
          <div class="col-auto"> 
            <div class="avatar avatar-xl position-relative">
              @php
                $aux = str_replace('public/', '', Auth::user()->avatar);
              @endphp
              <img src="{{ asset("storage/$aux") }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">
                {{ Auth::user()->name }}
              </h5>
                <p class="mb-0 font-weight-bold text-sm">
                {{ Auth::user()->email }}
                </p>
            </div>
          </div> 
        </div>
      </div>
    </div>
  @endsection
 
  @section('content')  
  <div class="row">
    <div class="col-ms-12 col-md-12 col-xl-12">
      <div class="card">
          <div class="card-header pb-0"> 
            @livewire('com.dashboard.filters')
          </div> 
          <div class="card-body pt-1">
            @livewire('com.dashboard.block1') 
          </div>  
          <div class="card-body pb-1">
            @livewire('com.dashboard.block2')   
          </div> 
      </div>
    </div>
  </div>   
  @endsection

  @section('scripts-imports')
    <script src="{{ asset('assets/js/plugins/datatables.js') }}"></script>
  @endsection
  @section('scripts')
    <script>
        
    </script>
  @endsection   