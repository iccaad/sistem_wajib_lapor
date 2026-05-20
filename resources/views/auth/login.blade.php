<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-white tracking-tight">Login Admin</h2>
        <p class="text-brand-soft text-sm mt-1">Gunakan akun resmi Polrestabes</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-white" />
            <x-text-input id="email" class="block mt-1 w-full bg-white/5 border-white/10 text-white focus:ring-brand-accent" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ showPassword: false }">
            <x-input-label for="password" :value="__('Password')" class="text-white" />

            <div class="relative mt-1">
                <input id="password"
                       :type="showPassword ? 'text' : 'password'"
                       name="password"
                       required
                       autocomplete="current-password"
                       class="block w-full rounded-md border bg-white/5 border-white/10 text-white focus:ring-brand-accent shadow-sm pr-10 px-3 py-2 text-sm">
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-white/40 hover:text-white/70 transition">
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-white/5 text-brand-accent shadow-sm focus:ring-brand-accent" name="remember">
                <span class="ms-2 text-sm text-brand-soft">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col gap-4 mt-8">
            <x-primary-button class="w-full justify-center py-3 text-sm shadow-xl shadow-black/20">
                {{ __('Masuk Ke Sistem') }}
            </x-primary-button>

            @if (Route::has('admin.password.request'))
                <a class="text-center text-sm text-brand-soft hover:text-white transition-colors" href="{{ route('admin.password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
