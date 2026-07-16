<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="font-cairo font-bold text-xl text-ink text-center mb-6">
        تسجيل الدخول
    </h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- الرقم الجامعي -->
        <div>
            <x-input-label for="university_id" value="الرقم الجامعي" />

            <x-text-input
                id="university_id"
                class="block mt-1 w-full"
                type="text"
                name="university_id"
                :value="old('university_id')"
                required
                autofocus
                autocomplete="username"
            />

            <x-input-error :messages="$errors->get('university_id')" class="mt-2" />
        </div>

        <!-- كلمة المرور -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('كلمة المرور')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-xs text-muted hover:text-gold transition-colors" href="{{ route('password.request') }}">
                    {{ __('نسيت كلمة المرور؟') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('تسجيل الدخول') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
