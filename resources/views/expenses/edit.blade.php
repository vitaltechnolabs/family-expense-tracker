<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('expenses.update', $expense) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date -->
                            <div>
                                <label for="date" class="block font-medium text-sm text-gray-700">Date</label>
                                <input id="date"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                    type="date" name="date" value="{{ old('date', $expense->date->format('Y-m-d')) }}"
                                    required />
                                @error('date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block font-medium text-sm text-gray-700">Amount (₹)</label>
                                <input id="amount"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                    type="number" step="0.01" name="amount"
                                    value="{{ old('amount', $expense->amount) }}" required />
                                @error('amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category_id"
                                    class="block font-medium text-sm text-gray-700">Category</label>
                                <select id="category_id" name="category_id"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                    required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ $expense->category_id == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tag -->
                            <div>
                                <label for="tag_id" class="block font-medium text-sm text-gray-700">Tag
                                    (Optional)</label>
                                <select id="tag_id" name="tag_id"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                                    <option value="">Select Tag</option>
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->tag_id }}" {{ $expense->tag_id == $tag->tag_id ? 'selected' : '' }}>
                                            {{ $tag->tag_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tag_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block font-medium text-sm text-gray-700">Payment
                                    Method</label>
                                <select id="payment_method" name="payment_method"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                    required>
                                    @foreach(['UPI', 'Cash', 'Net Banking', 'Cheque'] as $method)
                                        <option value="{{ $method }}" {{ $expense->payment_method == $method ? 'selected' : '' }}>{{ $method }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- For Member -->
                            <div>
                                <label for="for_member_id" class="block font-medium text-sm text-gray-700">For Whom
                                    (Optional)</label>
                                <select id="for_member_id" name="for_member_id"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                                    <option value="">Self</option>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->user_id }}" {{ $expense->for_member_id == $member->user_id ? 'selected' : '' }}>
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('for_member_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mt-4">
                            <label for="remarks" class="block font-medium text-sm text-gray-700">Remarks
                                (Optional)</label>
                            <textarea id="remarks" name="remarks"
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
                                rows="3">{{ old('remarks', $expense->remarks) }}</textarea>
                            @error('remarks')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Update Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>