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

                    <!-- Filters -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100 shadow-sm">
                        <form method="GET" action="{{ route('expenses.index') }}"
                            class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            @if(isset($canViewAll) && $canViewAll)
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Member</label>
                                    <select name="member_id"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
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
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Tag Filter -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tag</label>
                                <select name="tag_id"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                    <option value="">No Tag</option>
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
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">End
                                    Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                            </div>
                            <div class="md:col-span-5 flex justify-end mt-2">
                                <a href="{{ route('expenses.index') }}"
                                    class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    Clear
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    Apply Filters
                                </button>
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
                            <tbody x-ref="expenseBody" class="bg-white divide-y divide-gray-200">
                                @include('expenses.partials.expense-rows')
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
        <div x-show="isModalOpen" x-ref="modalContainer" class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

            <!-- Modal Panel -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all w-full max-w-[95%] sm:my-8 sm:w-full sm:max-w-lg"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-6" id="modal-title">
                                    Add New Expense
                                </h3>

                                <!-- Inline Success Message -->
                                <div x-show="showSuccessMessage" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2"
                                    class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800">
                                                Expense saved successfully!
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <form @submit.prevent="submitExpense" class="space-y-5">
                                    <!-- Date & Amount -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                            <input type="date" x-model="form.date" required
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount
                                                (₹)</label>
                                            <div class="relative rounded-md shadow-sm">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500 sm:text-sm">₹</span>
                                                </div>
                                                <input type="number" step="0.01" x-model="form.amount"
                                                    x-ref="amountInput" required
                                                    class="block w-full rounded-lg border-gray-300 pl-7 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5"
                                                    placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category & Tag -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                            <select x-model="form.category_id" required
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->category_id }}">
                                                        {{ $category->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tag <span
                                                    class="text-gray-400 font-normal">(Optional)</span></label>
                                            <select x-model="form.tag_id"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                                                <option value="">No Tag</option>
                                                @foreach($tags as $tag)
                                                    <option value="{{ $tag->tag_id }}">{{ $tag->tag_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Payment & Member -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment
                                                Method</label>
                                            <select x-model="form.payment_method" required
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                                                <option value="Cash">Cash</option>
                                                <option value="Net Banking">Net Banking</option>
                                                <option value="UPI">UPI</option>
                                                <option value="Cheque">Cheque</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">For
                                                Member</label>
                                            <select x-model="form.for_member_id"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                                                <option value="">Myself</option>
                                                @foreach($members as $member)
                                                    <option value="{{ $member->user_id }}">{{ $member->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Remarks -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                        <textarea x-model="form.remarks" rows="3"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2"
                                            placeholder="Add any notes here..."></textarea>
                                    </div>

                                    <!-- Error Message -->
                                    <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-[-10px]"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="rounded-md bg-red-50 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">There were errors with your
                                                    submission</h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <p x-text="errorMessage"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div
                                        class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3 pt-4 border-t border-gray-100">
                                        <button type="submit" :disabled="isLoading"
                                            :class="isSaved ? 'bg-green-600 hover:bg-green-700 focus-visible:outline-green-600' : 'bg-indigo-600 hover:bg-indigo-500 focus-visible:outline-indigo-600'"
                                            class="inline-flex w-full justify-center rounded-lg px-3 py-2.5 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                            <span x-show="!isLoading && !isSaved">Save Expense</span>
                                            <span x-show="isSaved" class="flex items-center">
                                                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Saved!
                                            </span>
                                            <span x-show="isLoading" class="flex items-center">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Saving...
                                            </span>
                                        </button>
                                        <button type="button" @click="closeModal()"
                                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0 transition-colors duration-200">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
                isSaved: false, // For button feedback
                showToast: false, // Keeping global toast for other potential uses
                showSuccessMessage: false, // New inline success state
                expensesAdded: 0, // Track if we need to refresh list
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
                    this.showSuccessMessage = false;
                    this.isSaved = false;
                    this.expensesAdded = 0; // Reset counter on open
                    this.errorMessage = '';
                    this.$nextTick(() => {
                        this.$refs.amountInput.focus();
                    });
                },
                closeModal() {
                    this.isModalOpen = false;
                    this.errorMessage = '';
                    this.showSuccessMessage = false;
                    this.isSaved = false;
                    
                    // Refresh list if expenses were added
                    if (this.expensesAdded > 0) {
                        this.fetchExpenseList();
                    }
                },
                fetchExpenseList() {
                    // Fetch the partially rendered table rows
                    axios.get('{{ route("expenses.index") }}?refresh_list=1')
                        .then(response => {
                            this.$refs.expenseBody.innerHTML = response.data;
                        })
                        .catch(error => {
                            console.error('Failed to refresh list:', error);
                        });
                },
                submitExpense() {
                    this.isLoading = true;
                    this.errorMessage = '';
                    this.showSuccessMessage = false;
                    this.isSaved = false;

                    // Simple Validation
                    if (!this.form.amount || !this.form.category_id || !this.form.date) {
                        this.errorMessage = 'Please fill in all required fields.';
                        this.isLoading = false;
                        return;
                    }

                    axios.post('{{ route("expenses.store") }}', this.form)
                        .then(response => {
                            // Success
                            this.showSuccessMessage = true;
                            this.isSaved = true; // Trigger button feedback
                            this.isLoading = false;
                            this.expensesAdded++; // Increment add counter
                            
                            // Scroll to top to show success message
                            this.$refs.modalContainer.scrollTo({ top: 0, behavior: 'smooth' });

                            // Auto-hide success states after 3 seconds
                            setTimeout(() => {
                                this.showSuccessMessage = false;
                                this.isSaved = false;
                            }, 3000);

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
                            console.error('Submission Error:', error);
                            if (error.response && error.response.data && error.response.data.errors) {
                                // Join all error messages
                                this.errorMessage = Object.values(error.response.data.errors).flat().join(', ');
                            } else if (error.message) {
                                this.errorMessage = error.message;
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