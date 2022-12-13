<form wire:submit.prevent="update">
    <div>
        <div class="row"> 
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Nombre</label> 
                    <input id="name"
                            wire:model="name"   
                            class="form-control @error('name') is-invalid @enderror"
                            type="text"
                            name="name"
                            placeholder="Nombre"
                            required autofocus>
                    @error('name')  
                        <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Correo</label>
                    <input id="email"
                        wire:model="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        type="email"
                        name="email" 
                        placeholder="Correo"
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
                    <label for="telefono">Tel&eacute;fono</label>
                    <input id="telefono"
                        wire:model="telefono"  
                        class="form-control @error('telefono') is-invalid @enderror"
                        type="number"
                        name="telefono"
                        placeholder="Celular"
                        required autofocus>
                    @error('telefono') 
                        <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-2"> 
                <div class="form-group"> 
                    <label for="rol">Rol</label>
                    <select id="rol"
                            class="form-control @error('rol') is-invalid @enderror"
                            name="rol"
                            placeholder="Rol"
                            required
                            disabled>
                            <option selected value="#">{{ $storedUserData->rol }}</option>
                    </select>
                    @error('rol')  
                        <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input id="password"
                            wire:model.lazy="password"  
                            class="form-control @error('password') is-invalid @enderror"
                            type="password"
                            name="password"
                            placeholder="Tu contraseña"
                            autocomplete="new-password">
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
                    <button type="button" wire:click="random_pass" class="btn bg-gradient-secondary mt-1">Generar contraseña</button>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation"
                        wire:model="passwordConfirmation" 
                        class="form-control"
                        type="password"
                        placeholder="Confirma tu contraseña"
                        name="password_confirmation">
                </div> 
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="author align-items-center">
                    @php
                        $aux = str_replace('public/', '', $storedUserData->avatar);
                    @endphp
                    <img src="{{ asset("storage/$aux") }}" alt="Foto de perfil" class="avatar shadow">
                    <div class="name ps-3">
                        <div class="form-group">
                            <label for="avatar">Avatar</label>
                            <input id="avatar"
                                wire:model="avatar" 
                                class="form-control"
                                type="file"
                                name="avatar"> 
                            @error('avatar')  
                                <div class="text-danger font-weight-bold" style="font-size: 12px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-md-6">
                <button class="btn bg-gradient-warning">Actualizar datos</button>
            </div>
        </div> 
    </div>
</form>
