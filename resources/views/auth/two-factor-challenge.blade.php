<x-guest-layout>
    <div class="w-full flex items-center justify-center">
        <div class="shadow-2xl flex flex-col md:flex-row w-full md:w-3/4 mx-auto" style="min-height: 90vh; margin: 5vh 0;">
            <div class="hidden md:flex md:w-1/2 fd-bg-secondary justify-center items-center login-image">
                <img src="{{ asset('images/login.png')}}" alt="Image authentification à deux facteurs" class="w-full h-full rounded-lg">
            </div>
            <div class="w-full md:w-1/2 bg-white px-8 md:px-12 py-8 rounded-lg">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png')}}" alt="Logo campus connect" class="mx-auto mb-4 logo-image" width="45" height="45">
                    <h2 class="text-2xl font-semibold">Authentification à deux facteurs</h2>
                </div>

                <div x-data="{ recovery: false }">
                    <div class="mb-4 text-sm text-gray-600" x-show="! recovery">
                        {{ __('Veuillez confirmer l\'accès à votre compte en entrant le code d\'authentification fourni par votre application d\'authentification.') }}
                    </div>

                    <div class="mb-4 text-sm text-gray-600" x-cloak x-show="recovery">
                        {{ __('Veuillez confirmer l\'accès à votre compte en entrant l\'un de vos codes de récupération d\'urgence.') }}
                    </div>

                    <x-validation-errors class="mb-4" />

                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf

                        <div class="mt-4" x-show="! recovery">
                            <x-label for="code" value="{{ __('Code') }}" />
                            <x-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                        </div>

                        <div class="mt-4" x-cloak x-show="recovery">
                            <x-label for="recovery_code" value="{{ __('Code de récupération') }}" />
                            <x-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                                            x-show="! recovery"
                                            x-on:click="
                                                recovery = true;
                                                $nextTick(() => { $refs.recovery_code.focus() })
                                            ">
                                {{ __('Utiliser un code de récupération') }}
                            </button>

                            <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                                            x-cloak
                                            x-show="recovery"
                                            x-on:click="
                                                recovery = false;
                                                $nextTick(() => { $refs.code.focus() })
                                            ">
                                {{ __('Utiliser un code d\'authentification') }}
                            </button>
                        </div>

                        <div class="mt-4 text-center">
                            <x-button class="fd-bg-secondary w-full flex items-center justify-center">
                                {{ __('Se connecter') }}
                            </x-button>
                        </div>
                    </form>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Retour à la page de connexion</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
