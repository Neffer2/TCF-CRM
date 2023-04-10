<div> 
    <form wire:submit.prevent="store">  
        <div class="row p-2">
            <div class="col-md-12"> 
                <p class="text-sm mb-0"> 
                    Selecciona uno de tus contactos e inicia con &eacute;ste un <b>prospecto.</b>
                    ¿A&uacute;n no tienes contactos? <a href="{{ route('contactos') }}" target="_blank"><b>Aqu&iacute;</b></a> puedes crear uno nuevo.
                </p> 
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                    <label for="contacto">Contactos: </label>
                    <select id="contacto" wire:model.lazy="contacto" class="form-control @error('contacto') is-invalid @elseif(strlen($contacto) > 0) is-valid @enderror" value="{{ old('contacto') }}">
                        <option value="">Seleccionar</option>
                        @foreach ($contactos as $contacto)
                            <option value="{{ $contacto->id }}">{{ $contacto->nombre }} {{ $contacto->apellido }} - {{ $contacto->empresa }}</option>
                        @endforeach
                    </select>
                    @error('contacto')
                        <div id="contacto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror 
                </div>
            </div>
            <div class="col-md-12">
                <button class="btn bg-gradient-warning">Crear nuevo prospecto</button>
            </div>
        </div>
    </form>
    @if($errors->any()) 
    <script>
      Swal.fire(
        '!Oppss tenemos un problema',
        `<ul style='text-align: initial; list-style-type: none;'>
          @foreach($errors->all() as $error) 
            <li>{{ $error }}<li>
          @endforeach
        </ul>`,
        'error'
      );
    </script>
  @endif 
  @if (session('success'))
    <script>
      Swal.fire(
        'Hecho',
        `{{ session('success') }}`,
        'success'
      );
    </script>
  @endif 
</div>