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
            <x-input-label for="email" :value="__('Email')" class="text-brand-light" />
            <x-text-input id="email" class="block mt-1 w-full bg-white/5 border-white/10 text-white focus:ring-brand-accent" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-brand-light" />

            <x-text-input id="password" class="block mt-1 w-full bg-white/5 border-white/10 text-white focus:ring-brand-accent"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

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
