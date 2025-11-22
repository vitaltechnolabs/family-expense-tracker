<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invite Family Member') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('family.invite.store') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Member Name</label>
                            <input id="name"
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                type="text" name="name" :value="old('name')" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Access PIN -->
                        <div class="mt-4">
                            <label for="access_pin" class="block font-medium text-sm text-gray-700">Access PIN (4
                                Digits)</label>
                            <input id="access_pin"
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                type="text" name="access_pin" maxlength="4" pattern="\d{4}" :value="old('access_pin')"
                                required placeholder="1234" />
                            @error('access_pin')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address (Optional) -->
                        <div class="mt-4">
                            <label for="email" class="block font-medium text-sm text-gray-700">Email (Optional)</label>
                            <input id="email"
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                type="email" name="email" :value="old('email')" />
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
                            <select id="role" name="role"
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Account Name -->
                        <div class="mt-4">
                            <label for="account_name" class="block font-medium text-sm text-gray-700">Account
                                Name</label>
                            <input id="account_name"
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                type="text" name="account_name" :value="old('account_name')" required />
                            @error('account_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Invite Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>