<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Family Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Family Portal Link -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Family Portal Link</h3>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="{{ route('family.portal', $family->slug) }}"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                            id="portalLink">
                        <button onclick="copyLink()"
                            class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            Copy
                        </button>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Share this link with your family members for easy PIN-based
                        login.</p>
                </div>
            </div>

            <!-- General Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">General Preferences</h3>
                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Currency -->
                            <div>
                                <label for="currency" class="block font-medium text-sm text-gray-700">Currency</label>
                                <select id="currency" name="currency"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                                    <option value="USD" {{ ($family->settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="INR" {{ ($family->settings['currency'] ?? '') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                    <option value="EUR" {{ ($family->settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ ($family->settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                            </div>

                            <!-- Language -->
                            <div>
                                <label for="language" class="block font-medium text-sm text-gray-700">Language</label>
                                <select id="language" name="language"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                                    <option value="en" {{ ($family->settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="hi" {{ ($family->settings['language'] ?? '') == 'hi' ? 'selected' : '' }}>Hindi</option>
                                    <option value="gu" {{ ($family->settings['language'] ?? '') == 'gu' ? 'selected' : '' }}>Gujarati</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(Auth::user()->role === 'admin')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Family Members</h3>
                            <a href="{{ route('family.invite') }}"
                                class="text-sm text-indigo-600 hover:text-indigo-900">Invite New Member</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Access PIN
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Can View All
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($members as $member)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $member->role === 'admin' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($member->role) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $member->access_pin ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($member->can_view_all_expenses)
                                                    <span class="text-green-600 font-bold">Yes</span>
                                                @else
                                                    <span class="text-gray-400">No</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button onclick="openEditModal({{ $member->toJson() }})"
                                                    class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div id="editMemberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full"
        style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 text-center">Edit Member</h3>
                <form id="editMemberForm" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="edit_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email (Optional)</label>
                            <input type="email" name="email" id="edit_email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role" id="edit_role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Access PIN (4 Digits)</label>
                            <input type="text" name="access_pin" id="edit_pin" maxlength="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Leave blank to keep current">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Leave blank to keep current">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="can_view_all_expenses" id="edit_view_all" value="1"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="edit_view_all" class="ml-2 block text-sm text-gray-900">Can View All
                                Expenses</label>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copyLink() {
            var copyText = document.getElementById("portalLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            alert("Copied the link: " + copyText.value);
        }

        function openEditModal(member) {
            document.getElementById('editMemberModal').style.display = 'block';
            document.getElementById('editMemberForm').action = "/family/member/" + member.user_id;

            document.getElementById('edit_name').value = member.name;
            document.getElementById('edit_email').value = member.email || '';
            document.getElementById('edit_role').value = member.role;
            document.getElementById('edit_pin').value = member.access_pin || '';
            document.getElementById('edit_view_all').checked = member.can_view_all_expenses;
        }

        function closeEditModal() {
            document.getElementById('editMemberModal').style.display = 'none';
        }

        window.onclick = function (event) {
            var modal = document.getElementById('editMemberModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</x-app-layout>