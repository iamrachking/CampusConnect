<x-guest-layout>
    <div class="w-full flex items-center justify-center">
        <div class="shadow-2xl flex flex-col md:flex-row w-full md:w-3/4 mx-auto" style="min-height: 90vh; margin: 5vh 0;">
            <div class="hidden md:flex md:w-1/2 fd-bg-secondary justify-center items-center login-image">
                <img src="{{ asset('images/login.png')}}" alt="Image vérification email" class="w-full h-full rounded-lg">
            </div>
            <div class="w-full md:w-1/2 bg-white px-8 md:px-12 py-8 rounded-lg">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png')}}" alt="Logo campus connect" class="mx-auto mb-4 logo-image" width="45" height="45">
                    <h2 class="text-2xl font-semibold">Vérification de votre adresse e-mail</h2>
                    <p class="text-gray-600 mt-2">
                        {{ __('Avant de continuer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer par e-mail ? Si vous n\'avez pas reçu l\'e-mail, nous vous en enverrons volontiers un autre.') }}
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ __( "Un nouveau lien de vérification a été envoyé à l'adresse e-mail que vous avez fournie dans les paramètres de votre profil.") }}
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf

                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2">
                        {{ __('Déconnexion') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
