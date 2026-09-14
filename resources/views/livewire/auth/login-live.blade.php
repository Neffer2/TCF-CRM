<div>
    <div class="bl-field bl-in bl-d3">
        <div class="bl-field__box">
            <input id="email"
                   wire:model="email"
                   class="@error('email') is-invalid @enderror"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="Correo"
                   autocomplete="username"
                   required autofocus>
            <label for="email">Correo corporativo</label>
        </div>
        @error('email')
            <div class="bl-field__error">{{ $message }}</div>
        @enderror
    </div>

    <div class="bl-field bl-in bl-d4">
        <div class="bl-field__box">
            <input id="password"
                   wire:model="password"
                   class="@error('password') is-invalid @enderror"
                   type="password"
                   name="password"
                   placeholder="Contraseña"
                   autocomplete="current-password"
                   required>
            <label for="password">Contraseña</label>
            <button type="button" class="bl-field__toggle" data-toggle-password="password" aria-label="Mostrar u ocultar contraseña" aria-pressed="false">
                <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 12s3.5-6.5 9.5-6.5S21.5 12 21.5 12s-3.5 6.5-9.5 6.5S2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M3 3l18 18"/><path d="M10.6 5.7A10 10 0 0 1 12 5.5c6 0 9.5 6.5 9.5 6.5a17 17 0 0 1-3.2 4"/><path d="M6.6 6.6C4 8.5 2.5 12 2.5 12s3.5 6.5 9.5 6.5c1.7 0 3.2-.5 4.5-1.2"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
            </button>
        </div>
        @error('password')
            <div class="bl-field__error">{{ $message }}</div>
        @enderror
    </div>

    <div class="bl-row bl-in bl-d5">
        <label class="bl-switch">
            <input id="remember_me" type="checkbox" name="remember">
            <i></i>
            <span>{{ __('Recuérdame') }}</span>
        </label>
        <span class="bl-link" title="Pídele al administrador del CRM que restablezca tu clave">¿Olvidaste tu clave?</span>
    </div>

    <button type="submit" class="bl-btn bl-in bl-d5">
        <span>{{ __('Iniciar sesión') }}</span>
        <span class="bl-btn__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7"/><path d="M8 7h9v9"/></svg>
        </span>
    </button>
</div>
