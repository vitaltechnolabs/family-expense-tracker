<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-3xl font-bold text-gray-900">{{ $family->family_name }}</h2>
        <p class="text-gray-600 mt-2">Who is logging in?</p>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        @foreach ($family->users as $user)
            <div onclick="openPinModal('{{ $user->user_id }}', '{{ $user->name }}')"
                class="cursor-pointer group flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition">
                <div
                    class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center text-2xl font-bold text-indigo-600 mb-3 group-hover:bg-indigo-200 transition">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <span class="text-gray-900 font-medium group-hover:text-indigo-600 transition">{{ $user->name }}</span>
            </div>
        @endforeach
    </div>

    <!-- PIN Modal -->
    <div id="pinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full"
        style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Enter PIN for <span
                        id="userName"></span></h3>
                <div class="mt-2 px-7 py-3">
                    <form id="loginForm" method="POST" action="{{ route('family.login', $family->slug) }}">
                        @csrf
                        <input type="hidden" name="user_id" id="userIdInput">
                        <input type="password" name="access_pin" maxlength="4"
                            class="text-center text-2xl tracking-widest w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="••••" required autofocus>

                        @error('access_pin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <div class="items-center px-4 py-3">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Login
                            </button>
                        </div>
                    </form>
                    <button onclick="closePinModal()"
                        class="mt-2 text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPinModal(userId, userName) {
            document.getElementById('pinModal').style.display = 'block';
            document.getElementById('userIdInput').value = userId;
            document.getElementById('userName').innerText = userName;
            document.querySelector('input[name="access_pin"]').focus();
        }

        function closePinModal() {
            document.getElementById('pinModal').style.display = 'none';
        }

        // Close modal if clicked outside
        window.onclick = function (event) {
            var modal = document.getElementById('pinModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</x-guest-layout>