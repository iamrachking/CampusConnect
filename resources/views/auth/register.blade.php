<x-guest-layout>
    <div class="w-full flex items-center justify-center">
        <div class="shadow-2xl flex flex-col md:flex-row w-full md:w-3/4 mx-auto" style="min-height: 90vh; margin: 5vh 0;">
            <!-- Div de l'image, visible uniquement sur les écrans moyens et grands -->
            <div class="hidden md:flex md:w-1/2 fd-bg-secondary justify-center items-center login-image">
                <img src="{{ asset('images/login.png')}}" alt="Image enfant" class="w-full h-full rounded-lg">
            </div>
            <!-- Div du formulaire, prenant toute la largeur sur petit écran, la moitié sur écrans moyens et grands -->
            <div class="w-full md:w-1/2 bg-white px-8 md:px-12 py-8 rounded-lg">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png')}}" alt="Logo campus connect" class="mx-auto mb-4 logo-image" width="45" height="45">
                    <h2 class="text-2xl font-semibold">Bienvenue Sur Campus Connect</h2>
                    <p class="text-gray-600">Vous avez déja un compte ? <a href="{{ route('login') }}" class="text-blue-500">Se connecter </a></p>
                </div>
                <x-validation-errors class="mb-2" />
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="flex flex-col md:flex-row justify-between align-center">
                        <div class="w-full md:w-1/2 md:pr-2">
                            <x-label for="nom" value="{{ __('Nom') }}" />
                            <x-input id="nom" class="block mt-1 w-full" type="text" name="nom"
                                :value="old('nom')" required autocomplete="family-name" />
                        </div>
                        <div class="w-full md:w-1/2 md:pl-2 mt-4 md:mt-0">
                            <x-label for="prenom" value="{{ __('Prénom') }}" />
                            <x-input id="prenom" class="block mt-1 w-full" type="text" name="prenom"
                                :value="old('prenom')" required autofocus autocomplete="given-name" />
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autocomplete="username" />
                    </div>
                    
                    <div class="flex flex-col md:flex-row justify-between align-center mt-4">
                        <div class="w-full md:w-1/2 md:pr-2">
                            <x-label for="password" value="{{ __('Mot de passe') }}" />
                            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                                autocomplete="new-password" />
                        </div>

                        <div class="w-full md:w-1/2 md:pl-2 mt-4 md:mt-0">
                            <x-label for="password_confirmation" value="{{ __('Confirmer le mot de passe') }}" />
                            <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                        </div>
                    </div>
                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mt-4">
                            <x-label for="terms">
                                <div class="flex items-center">
                                    <x-checkbox name="terms" id="terms" required />

                                    <div class="ms-2">
                                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' =>
                                                '<a target="_blank" href="' .
                                                route('terms.show') .
                                                '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                                __('Terms of Service') .
                                                '</a>',
                                            'privacy_policy' =>
                                                '<a target="_blank" href="' .
                                                route('policy.show') .
                                                '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                                __('Privacy Policy') .
                                                '</a>',
                                        ]) !!}
                                    </div>
                                </div>
                            </x-label>
                        </div>
                    @endif

                    <div class="lg:mt-5 mt-4 text-center">
                        <x-button class="fd-bg-secondary w-full flex items-center justify-center">
                            {{ __('S\'inscrire') }}
                        </x-button>
                    </div>
                </form>
                <div class="flex items-center justify-center my-3">
                    <span class="w-full border-t"></span>
                    <span class="px-4 text-gray-500">OU</span>
                    <span class="w-full border-t"></span>
                </div>

                <div class="flex flex-wrap gap-3 justify-center mt-3">
                    <div><i class="mdi mdi-google-plus bg-gray-200 py-1 px-2 border border-gray-300 rounded-lg shadow"></i></div>
                    <div><i class="mdi mdi-facebook bg-gray-200 py-1 px-2 border border-gray-300 rounded-lg shadow"></i></div>
                    <div><i class="mdi mdi-twitter bg-gray-200 py-1 px-2 border border-gray-300 rounded-lg shadow"></i></div>
                    <div><i class="mdi mdi-instagram bg-gray-200 py-1 px-2 border border-gray-300 rounded-lg shadow"></i></div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
