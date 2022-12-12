@extends('layouts.comercial.main')
  @section('profile-card')
    <div class="card shadow-lg mx-4 card-profile-bottom mt-5">
      <div class="card-body p-3">
        <div class="row gx-4">
          <div class="col-auto">
            <div class="avatar avatar-xl position-relative">
              <img src="../../../assets/img/team-1.jpg" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
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
          <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
            <div class="nav-wrapper position-relative end-0">
              <ul x-data class="nav nav-pills nav-fill p-1" role="tablist">
                <li class="nav-item" role="presentation">
                  <a @click="$dispatch('fire-toggle', 0)" class="nav-link mb-0 px-0 py-1 active d-flex align-items-center justify-content-center " data-bs-toggle="tab" href="javascript:;" role="tab" aria-selected="true">
                    <i class="ni ni-app"></i>
                    <span class="ms-2">Base</span>
                  </a>
                </li>
                <li class="nav-item" role="presentation">
                  <a @click="$dispatch('fire-toggle', 1)" href="{{ route('base-upload') }}" class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center " data-bs-toggle="tab" href="javascript:;" role="tab" aria-selected="false" tabindex="-1">
                    <i class="ni ni-cloud-upload-96"></i>
                    <span class="ms-2">Subir</span>
                  </a>
                </li>
                {{-- <li class="nav-item" role="presentation">
                  <a class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center " data-bs-toggle="tab" href="javascript:;" role="tab" aria-selected="false" tabindex="-1">
                    <i class="ni ni-settings-gear-65"></i>
                    <span class="ms-2">Settings</span>
                  </a>
                </li> --}}
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endsection

  @section('content')    
    <div x-data="menu" @fire-toggle.window="Toggle"> 

      <div class="row mt-4" x-show="!toggle[0]" x-transition x-cloak>
        <div class="col-12 col-md-12 col-xl-12">
          <div class="card card-body mt-4">
              <h5 class="mb-0">Actualiza tu base comercial</h5>
              <p class="text-sm mb-0">Para actualizar tu base comercial necesitas utilizar &eacute;ste <a href="#"><b>formato</b></a>.</p>
              <label class="mt-4 form-label">Carga tu hoja de c&aacute;lculo</label>
              <form action="{{ route('base-upload') }}" enctype="multipart/form-data" method="POST" class="form-control">
                @csrf
                <div class="fallback">
                  <input name="base_xls" type="file" required/>
                </div>
                <div class="d-flex justify-content-end mt-4">
                  <button type="submit" name="button" class="btn bg-gradient-warning m-0 ms-2">Cargar</button>
                </div>
              </form>
          </div>
        </div>
      </div> 

      <div class="row mt-4" x-show="!toggle[1]" x-transition> 
        @livewire('com.base-list', ['user_id' => Auth::user()->id]) 
      </div>
    </div>
  @endsection

  @section('scripts-imports')
    <script src="{{ asset('assets/js/plugins/datatables.js') }}"></script>
  @endsection
  @section('scripts')
    <script>
        const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {
          searchable: true,
          fixedHeight: true
        });

        function menu(){
          return {
            toggle: [true, false],
              Toggle (index){
                this.toggle.forEach((elem, index) => {
                  this.toggle[index] = false;
                });
                this.toggle[index.detail] = true;
              }
          }
        }
    </script>
  @endsection   