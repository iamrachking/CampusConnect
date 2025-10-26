<x-guest-layout>
    <div class="w-full flex items-center justify-center">
        <div class="shadow-2xl flex flex-col md:flex-row w-full md:w-3/4 mx-auto" style="min-height: 90vh; margin: 5vh 0;">
            <div class="hidden md:flex md:w-1/2 fd-bg-secondary justify-center items-center login-image">
                <img src="{{ asset('images/login.png')}}" alt="Image mot de passe oublié" class="w-full h-full rounded-lg">
            </div>
            <div class="w-full md:w-1/2 bg-white px-8 md:px-12 py-8 rounded-lg">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png')}}" alt="Logo campus connect" class="mx-auto mb-4 logo-image" width="45" height="45">
                    <h2 class="text-2xl font-semibold">Mot de passe oublié</h2>
                    <p class="text-gray-600 mt-2">
                        {{ __('Vous avez oublié votre mot de passe ? Pas de problème. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation du mot de passe.') }}
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <x-validation-errors class="mb-2" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div>
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    </div>

                    <div class="mt-4 text-center">
                        <x-button class="fd-bg-secondary w-full flex items-center justify-center">
                            {{ __('Envoyer le lien de réinitialisation') }}
                        </x-button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Retour à la page de connexion</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
