
<form wire:submit.prevent="store">
    <div> 
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Nombre</label> 
                    <input id="name"
                            wire:model="name" 
                            class="form-control @error('name') is-invalid @enderror"
                            type="text"
                            name="name"
                            placeholder="Nombre"
                            value="{{ old('name') }}"
                            required autofocus>
                    @error('name') 
                        <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Correo</label>
                    <input id="email"
                        wire:model="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        type="email"
                        name="email" 
                        placeholder="Correo"
                        value="{{ old('email') }}" 
                        required>
                    @error('email') 
                        <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row"> 
            <div class="col-md-4">
                <div class="form-group"> 
                    <label for="exampleFormControlInput1">Tel&eacute;fono</label>
                    <input id="telefono"
                        wire:model="telefono"  
                        class="form-control @error('telefono') is-invalid @enderror"
                        type="number"
                        name="telefono"
                        placeholder="Celular"
                        value="{{ old('telefono') }}"
                        required autofocus>
                    @error('telefono') 
                        <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group"> 
                    <label for="exampleFormControlInput1">Rol</label>
                    <select id="telefono"
                            wire:model="rol"  
                            class="form-control @error('rol') is-invalid @enderror"
                            name="rol"
                            placeholder="Rol"
                            value="{{ old('rol') }}"
                            required>
                            <option value="" selected>Seleccionar</option>
                        @foreach ($rolList as $rol)
                            <option value="{{ $rol->id }}">{{ $rol->description }}</option>
                        @endforeach
                    </select>
                    @error('rol')  
                        <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Contraseña</label>
                    <input id="password"
                            wire:model.lazy="password"  
                            class="form-control @error('password') is-invalid @enderror"
                            type="password"
                            name="password"
                            placeholder="Tu contraseña"
                            required autocomplete="new-password">
                    @error('password') 
                        <div class="text-danger font-weight-bold" style="font-size: 12px;">
                            @foreach ($errors->get('password') as $error)
                                {{ $error }} <br>
                            @endforeach
                        </div>
                    @enderror  
                    @if ($random_pass)
                        <input type="text" wire:model="random_pass" disabled class="form-control mt-1">
                    @endif
                    <button wire:click="random_pass" type="button" class="btn bg-gradient-secondary mt-1">Generar contraseña</button>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Confirmar contraseña</label>
                    <input id="password_confirmation"
                        wire:model="passwordConfirmation" 
                        class="form-control"
                        type="password"
                        placeholder="Confirma tu contraseña"
                        name="password_confirmation" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn bg-gradient-warning">Crear nuevo miembro</button>
            </div>
        </div> 
    </div>
</form>
