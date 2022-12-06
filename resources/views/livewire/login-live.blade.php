<div>
    <div class="mb-3">
        <input id="email"
                wire:model="email"
                class="form-control"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required autofocus>
        @error('email') 
            <div class="text-danger font-weight-bold" style="font-size: 12px;">
                @foreach ($errors->all() as $error)
                    {{ $error }} <br>
                @endforeach
            </div>
        @enderror 
    </div> 
    <div class="mb-3">
        <input id="password"
                wire:model="password"
                class="form-control" 
                type="password" 
                name="password"
                required autocomplete="current-password"> 
        @error('password') 
            <div class="text-danger font-weight-bold" style="font-size: 12px;">
                @foreach ($errors->all() as $error)
                    {{ $error }} <br>
                @endforeach
            </div> 
        @enderror 
    </div>
    <div class="form-check form-switch">
        <input id="remember_me" type="checkbox" name="remember" class="form-check-input" type="checkbox" id="rememberMe">
        <label class="form-check-label" for="rememberMe">{{ __('Recuérdame') }}</label>
    </div>
    <div class="text-center"> 
        <button type="submit" class="btn bg-gradient-warning w-100 my-4 mb-2">{{ __('Iniciar sesión') }}</button>
    </div>
    <div class="mb-2 position-relative text-center">
        <p class="text-sm font-weight-bold mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">or</p>
    </div>
    <div class="text-center">
        <a href="{{ route('register') }}" class="btn bg-gradient-dark w-100 mt-2 mb-4">{{ __('Registrarme') }}</a>
    </div> 
</div>  