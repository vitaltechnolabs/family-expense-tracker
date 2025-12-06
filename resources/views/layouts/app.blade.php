<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<link rel="manifest" href="{{ asset('manifest.json') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
<meta name="theme-color" content="#2563eb">
<script>
    // Register Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').then(function (registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            console.log('ServiceWorker registration failed: ', err);
        });
    }

    // PWA Install Prompt Logic
    document.addEventListener('alpine:init', () => {
        Alpine.store('pwa', {
            installPrompt: null,
            canInstall: false,
            init() {
                window.addEventListener('beforeinstallprompt', (e) => {
                    // Prevent the mini-infobar from appearing on mobile
                    e.preventDefault();
                    // Stash the event so it can be triggered later.
                    this.installPrompt = e;
                    
                    // Check if user dismissed it in this session
                    if (!sessionStorage.getItem('pwaBannerDismissed')) {
                        this.canInstall = true;
                    }
                    console.log('beforeinstallprompt fired');
                });
                window.addEventListener('appinstalled', () => {
                    this.canInstall = false;
                    this.installPrompt = null;
                    console.log('PWA was installed');
                });
            },
            dismiss() {
                this.canInstall = false;
                sessionStorage.setItem('pwaBannerDismissed', 'true');
            },
            async install() {
                if (!this.installPrompt) return;
                // Show the install prompt
                this.installPrompt.prompt();
                // Wait for the user to respond to the prompt
                const { outcome } = await this.installPrompt.userChoice;
                console.log(`User response to the install prompt: ${outcome}`);
                if (outcome === 'accepted') {
                    this.canInstall = false;
                }
                this.installPrompt = null;
            }
        });
    });
</script>

<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <!-- PWA Install Banner -->
        <div x-data x-show="$store.pwa.canInstall" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
            class="bg-indigo-600 text-white px-4 py-3 shadow-md relative z-50">
            <div class="flex items-center justify-between max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    <span class="flex p-2 rounded-lg bg-indigo-800">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </span>
                    <p class="ml-3 font-medium truncate">
                        <span class="md:hidden">Install App for better experience!</span>
                        <span class="hidden md:inline">Install the Family Expense Tracker app for a better
                            experience!</span>
                    </p>
                </div>
                <div class="flex items-center">
                    <button @click="$store.pwa.install()" type="button"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Install
                    </button>
                    <button @click="$store.pwa.dismiss()" type="button"
                        class="-mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center font-bold text-xl text-blue-600">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="block h-9 w-auto mr-2">
                                ExpenseTracker
                            </a>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                                {{ __('Expenses') }}
                            </x-nav-link>
                            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                {{ __('Reports') }}
                            </x-nav-link>

                            @if(Auth::user()->role === 'admin')
                                <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                    {{ __('Categories') }}
                                </x-nav-link>
                                <x-nav-link :href="route('tags.index')" :active="request()->routeIs('tags.*')">
                                    {{ __('Tags') }}
                                </x-nav-link>
                                <x-nav-link :href="route('family.invite')" :active="request()->routeIs('family.invite')">
                                    {{ __('Invite Member') }}
                                </x-nav-link>
                            @endif
                            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                                {{ __('Profile') }}
                            </x-nav-link>
                            <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                                {{ __('Settings') }}
                            </x-nav-link>
                            <!-- Install PWA Button (Desktop) -->
                            <button x-data x-show="$store.pwa.canInstall" @click="$store.pwa.install()" type="button"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                {{ __('Install App') }}
                            </button>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="flex items-center">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                        {{ __('Expenses') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('Reports') }}
                    </x-responsive-nav-link>

                    @if(Auth::user()->role === 'admin')
                        <x-responsive-nav-link :href="route('categories.index')"
                            :active="request()->routeIs('categories.*')">
                            {{ __('Categories') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('tags.index')" :active="request()->routeIs('tags.*')">
                            {{ __('Tags') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('family.invite')" :active="request()->routeIs('family.invite')">
                            {{ __('Invite Member') }}
                        </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                        {{ __('Settings') }}
                    </x-responsive-nav-link>
                    <!-- Install PWA Button (Mobile) -->
                    <button x-data x-show="$store.pwa.canInstall" @click="$store.pwa.install()"
                        class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        {{ __('Install App') }}
                    </button>
                </div>
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            {{ $slot }}
        </main>

        <footer class="bg-white border-t border-gray-100 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    Built with ❤️ by <a href="https://vitaltechnolabs.com" target="_blank"
                        class="text-blue-600 hover:text-blue-800">Vital Technolabs LLP</a>.
                </p>
            </div>
        </footer>
    </div>
</body>

</html>