<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Current Month -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm font-medium uppercase">This Month</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">₹{{ number_format($currentMonthTotal, 2) }}</div>
                </div>

                <!-- Today -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm font-medium uppercase">Today</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">₹{{ number_format($todayTotal, 2) }}</div>
                </div>

                <!-- Quick Action -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center justify-center">
                    <a href="{{ route('expenses.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-150 ease-in-out">
                        + Add New Expense
                    </a>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Recent Expenses</h3>
                    <div class="overflow-x-auto">
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
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($recentExpenses as $expense)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $expense->date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $expense->category->category_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">
                                            ₹{{ number_format($expense->amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $expense->user->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No recent expenses
                                            found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('expenses.index') }}"
                            class="text-blue-600 hover:text-blue-800 font-medium">View All Expenses &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>