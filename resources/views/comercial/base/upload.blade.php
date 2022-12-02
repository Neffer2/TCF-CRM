@extends('layouts.comercial.main')
  @section('content')
    <div class="row mt-4">
        <div class="col-12 col-md-12 col-xl-12">
            <div class="card card-body mt-4">
                <h5 class="mb-0">Actualiza tu base comercial</h5>
                <p class="text-sm mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe optio est dolores autem perspiciatis deserunt unde, fugiat, consectetur eligendi iusto qui nam asperiores at velit nostrum nemo laudantium blanditiis numquam!</p>
                {{-- <hr class="horizontal dark my-3"> --}}
                <label class="mt-4 form-label">Importa tu hoja de c&aacute;lculo</label>
                <form action="{{ route('base-upload') }}" method="POST" class="form-control">
                    @csrf
                    <div class="fallback">
                        <input name="base_xls" type="file" required/>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" name="button" class="btn btn-light m-0">Cancel</button>
                        <button type="submit" onclick="submit()" name="button" class="btn bg-gradient-primary m-0 ms-2">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @section('scripts')
        @error('base_xls')
            <script>
                Swal.fire(
                'Datos subidos exitosamente',
                'That thing is still around?',
                'success'
                )
            </script>
        @enderror
        {{-- @if ($errors->any())
            alertify.alert("<h3 style='color: #DD0019;'>¡ERROR!<h3>", `
                <p>
                    <ul>
                        @foreach ($errors->all() as $elem)
                            <li> {{ $elem }} </li>
                        @endforeach
                    </ul>
                </p>`).set('label', "<span style='color: #004991;'> Vale </span>");
        @endif 
        @if (session('status'))
            alertify.alert("<h3 style='color: #004991;'>¡FELICITACIONES!<h3>",`
                <p>
                    <ul>
                        {{ session('status') }}
                    </ul>
                </p>`).set('label', "Vale");
        @endif --}}
    @endsection
  @endsection  
