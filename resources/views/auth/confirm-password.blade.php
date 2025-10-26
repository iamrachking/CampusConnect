<x-guest-layout>
    <div class="w-full flex items-center justify-center">
        <div class="shadow-2xl flex flex-col md:flex-row w-full md:w-3/4 mx-auto" style="min-height: 90vh; margin: 5vh 0;">
            <div class="hidden md:flex md:w-1/2 fd-bg-secondary justify-center items-center login-image">
                <img src="{{ asset('images/login.png')}}" alt="Image confirmation mot de passe" class="w-full h-full rounded-lg">
            </div>
            <div class="w-full md:w-1/2 bg-white px-8 md:px-12 py-8 rounded-lg">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png')}}" alt="Logo campus connect" class="mx-auto mb-4 logo-image" width="45" height="45">
                    <h2 class="text-2xl font-semibold">Confirmation du mot de passe</h2>
                    <p class="text-gray-600 mt-2">
                        {{ __('Ceci est une zone sécurisée de l\'application. Veuillez confirmer votre mot de passe avant de continuer.') }}
                    </p>
                </div>

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div>
                        <x-label for="password" value="{{ __('Mot de passe') }}" />
                        <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
                    </div>

                    <div class="mt-4 text-center">
                        <x-button class="fd-bg-secondary w-full flex items-center justify-center">
                            {{ __('Confirmer') }}
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
