@extends('layouts.auth.main')
    @section('form')
        <form method="POST" action="{{ route('login') }}" role="form" class="text-start">
            @csrf
            <div class="mb-3">
                <input id="email"
                        class="form-control @error('email') invalid @enderror"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required autofocus>
            </div> 
            <div class="mb-3">
                <input id="password"
                        class="form-control @error('password') @enderror" 
                        type="password"
                        name="password"
                        required autocomplete="current-password">
            </div>
            <div class="form-check form-switch">
                <input id="remember_me" type="checkbox" name="remember" class="form-check-input" type="checkbox" id="rememberMe">
                <label class="form-check-label" for="rememberMe">{{ __('Recuérdame') }}</label>
            </div>
            <div class="text-center"> 
                <button type="submit" class="btn btn-primary w-100 my-4 mb-2">{{ __('Iniciar sesión') }}</button>
            </div>
            <div class="mb-2 position-relative text-center">
                <p class="text-sm font-weight-bold mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">or</p>
            </div>
            <div class="text-center">
                <a href="{{ route('register') }}" class="btn bg-gradient-dark w-100 mt-2 mb-4">{{ __('Registrarme') }}</a>
            </div>
        </form>
    @endsection 
    {{-- <x-guest-layout>
        <x-auth-card>
    
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />
    
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />
    
            <form >
                @csrf
    
                <!-- Email Address -->
                <div>
                    <x-label for="email" :value="__('Email')" />
    
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                </div>
    
                <!-- Password -->
                <div class="mt-4">
                    <x-label for="password" :value="__('Password')" />
    
                    <x-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />
                </div>
    
                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                        <span class="ml-2 text-sm text-gray-600"></span>
                    </label>
                </div>
    
                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
    
                    <x-button class="ml-3">
                        {{ __('Log in') }}
                    </x-button>
                </div>
            </form>
        </x-auth-card>
    </x-guest-layout> --}}

