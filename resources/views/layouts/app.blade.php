<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="font-bold text-xl text-blue-600">
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