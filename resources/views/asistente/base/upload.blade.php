@extends('layouts.comercial.main')
  @section('content') 
    <div class="row mt-4">
        <div class="col-12 col-md-12 col-xl-12">
            <div class="card card-body mt-4">
                <h5 class="mb-0">ASISTENTE Actualiza tu base comercial</h5>
                <p class="text-sm mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe optio est dolores autem perspiciatis deserunt unde, fugiat, consectetur eligendi iusto qui nam asperiores at velit nostrum nemo laudantium blanditiis numquam!</p>
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
  @endsection  
  