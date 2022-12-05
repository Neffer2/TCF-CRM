@extends('layouts.auth.main')
    @section('form')
        <form method="POST" action="{{ route('register') }}" role="form">
            @csrf
            <div class="mb-3">
                <input id="name"
                        class="form-control"
                        type="text"
                        name="name"
                        placeholder="Nombre"
                        value="{{ old('name') }}"
                        required autofocus>
                @error('email') 
                    <div class="form-text">{{ $errors->first('email') }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input id="email"
                        class="form-control"
                        type="email"
                        name="email"
                        placeholder="Correo"
                        value="{{ old('email') }}"
                        required>
            </div>
            <div class="mb-3">
                <input id="password"
                        class="form-control"
                        type="password"
                        name="password"
                        placeholder="Tu contraseña"
                        required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <input id="password_confirmation"
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
            <p class="text-sm mt-3 mb-0">Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-dark font-weight-bolder">Iniciar sesi&oacute;n</a></p>
        </form>
    @endsection 
{{-- <x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-label for="name" :value="__('Name')" />

                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout> --}}
