<div>
    <div class="mb-3">
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
    <div class="mb-3"> 
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
    <div class="mb-3">
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
    <div class="mb-3">
        <input id="password"
                wire:model.lazy="password"  
                class="form-control @error('password') is-invalid @enderror"
                type="password"
                name="password"
                placeholder="Tu contraseña"
                required autocomplete="new-password">
        @error('password') 
            <div class="text-danger font-weight-bold" style="font-size: 12px;">
                @foreach ($errors->all() as $error)
                    {{ $error }} <br>
                @endforeach
            </div>
        @enderror                
    </div>
    <div class="mb-3">
        <input id="password_confirmation"
                wire:model="passwordConfirmation" 
                class="form-control"
                type="password"
                placeholder="Confirma tu contraseña"
                name="password_confirmation" required>
    </div>
    <div class="form-check form-check-info text-start">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
        <label class="form-check-label" for="flexCheckDefault">
            Estoy de acuerdo y acepto  los <a href="javascript:;" class="text-dark font-weight-bolder">Terminos y condiciones</a>
        </label>
    </div>
    <div class="text-center">
        <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Registrarme</button>
    </div>
</div>
