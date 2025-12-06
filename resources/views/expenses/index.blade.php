<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expenses') }}
        </h2>
    </x-slot>

    <!-- Main Content Area with Alpine Data -->
    <div class="py-12" x-data="expenseManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Toast Notification -->
            <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2"
                class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center"
                style="display: none;">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span x-text="toastMessage"></span>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Header & Add Button -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Recent Expenses</h3>
                        <button @click="openModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Expense
                        </button>
                    </div>

                    <!-- Filters (Collapsible on Mobile?) -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <form method="GET" action="{{ route('expenses.index') }}"
                            class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            @if(isset($canViewAll) && $canViewAll)
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Member</label>
                                    <select name="member_id"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">All Members</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->user_id }}" {{ request('member_id') == $member->user_id ? 'selected' : '' }}>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <!-- Category Filter -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Category</label>
                                <select name="category_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Tag Filter -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tag</label>
                                <select name="tag_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">All Tags</option>
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->tag_id }}" {{ request('tag_id') == $tag->tag_id ? 'selected' : '' }}>{{ $tag->tag_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Date Range -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Start
                                    Date</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">End
                                    Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="md:col-span-5 flex justify-end">
                                <button type="submit"
                                    class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm font-medium">Apply
                                    Filters</button>
                            </div>
                        </form>
                    </div>

                    <!-- Expenses Table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Member</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($expenses as $expense)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $expense->date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $expense->category->category_name }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $expense->tag ? $expense->tag->tag_name : '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                            ₹{{ number_format($expense->amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $expense->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @can('update', $expense)
                                                <a href="{{ route('expenses.edit', $expense) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            @endcan
                                            @can('delete', $expense)
                                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
                                                    class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                            No expenses found. Start adding some!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Expense Modal -->
        <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

            <!-- Modal Panel -->
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">Add New Expense
                        </h3>

                        <form @submit.prevent="submitExpense">
                            <!-- Date & Amount Row -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" x-model="form.date" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Amount (₹)</label>
                                    <input type="number" step="0.01" x-model="form.amount" x-ref="amountInput" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <!-- Category & Tag Row -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <select x-model="form.category_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}">{{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tag (Optional)</label>
                                    <select x-model="form.tag_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">No Tag</option>
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->tag_id }}">{{ $tag->tag_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Payment Method & For Whom Row -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                    <select x-model="form.payment_method" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="Cash">Cash</option>
                                        <option value="Net Banking">Net Banking</option>
                                        <option value="UPI">UPI</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">For Member</label>
                                    <select x-model="form.for_member_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">Myself</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->user_id }}">{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Remarks</label>
                                <textarea x-model="form.remarks" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                            </div>

                            <!-- Validation Errors -->
                            <div x-show="errorMessage"
                                class="mb-4 p-2 bg-red-100 text-red-700 text-sm rounded border border-red-200"
                                x-text="errorMessage"></div>

                            <!-- Buttons -->
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                <button type="submit" :disabled="isLoading"
                                    class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:col-start-2 disabled:opacity-50">
                                    <span x-show="!isLoading">Save Expense</span>
                                    <span x-show="isLoading">Saving...</span>
                                </button>
                                <button type="button" @click="closeModal()"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Logic -->
    <script>
        function expenseManager() {
            return {
                isModalOpen: false,
                isLoading: false,
                showToast: false,
                toastMessage: '',
                errorMessage: '',
                form: {
                    date: new Date().toISOString().split('T')[0],
                    amount: '',
                    category_id: '',
                    tag_id: '',
                    payment_method: 'Cash',
                    for_member_id: '',
                    remarks: ''
                },
                openModal() {
                    this.isModalOpen = true;
                    this.$nextTick(() => {
                        this.$refs.amountInput.focus();
                    });
                },
                closeModal() {
                    this.isModalOpen = false;
                    this.errorMessage = '';
                },
                submitExpense() {
                    this.isLoading = true;
                    this.errorMessage = '';

                    // Simple Validation
                    if (!this.form.amount || !this.form.category_id || !this.form.date) {
                        this.errorMessage = 'Please fill in all required fields.';
                        this.isLoading = false;
                        return;
                    }

                    axios.post('{{ route("expenses.store") }}', this.form)
                        .then(response => {
                            // Success
                            this.triggerToast('Expense saved successfully!');

                            // Reset form partially (Keep Date & Category for speed, clear Amount/Remarks)
                            this.form.amount = '';
                            this.form.remarks = '';
                            // this.form.category_id = ''; // Keeping category same as previous entry - usually helpful

                            // Re-focus amount for rapid entry
                            this.$nextTick(() => {
                                this.$refs.amountInput.focus();
                            });
                            this.isLoading = false;

                            // Optional: Reload page content behind logic if needed, 
                            // but for speed we just let user add more. 
                            // Real-time table update requires more complex JS or a page reload later.
                        })
                        .catch(error => {
                            this.isLoading = false;
                            if (error.response && error.response.data && error.response.data.errors) {
                                // Join all error messages
                                this.errorMessage = Object.values(error.response.data.errors).flat().join(', ');
                            } else {
                                this.errorMessage = 'Something went wrong. Please try again.';
                            }
                        });
                },
                triggerToast(message) {
                    this.toastMessage = message;
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                }
            }
        }
    </script>
</x-app-layout>