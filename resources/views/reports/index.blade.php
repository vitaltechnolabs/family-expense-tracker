<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.index') }}"
                        class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Member Filter (Admin/Allowed Only) -->
                        @if($canViewAll)
                            <div>
                                <label for="member_id" class="block text-sm font-medium text-gray-700">Member</label>
                                <select name="member_id" id="member_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                                    <option value="">All Members</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->user_id }}" {{ request('member_id') == $member->user_id ? 'selected' : '' }}>
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Date Range -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Apply Filter
                            </button>
                        </div>
                    </form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Category Breakdown -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-medium mb-4 text-center">Expense by Category</h3>
                            <canvas id="categoryChart"></canvas>
                        </div>

                        <!-- Member Breakdown (Admin Only) -->
                        @if(Auth::user()->role === 'admin')
                            <div class="bg-gray-50 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-medium mb-4 text-center">Expense by Member</h3>
                                <canvas id="memberChart"></canvas>
                            </div>
                        @endif

                        <!-- Monthly Trend -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow md:col-span-2">
                            <h3 class="text-lg font-medium mb-4 text-center">Monthly Spending Trend (Last 6 Months)</h3>
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($categoryData->pluck('label')),
                datasets: [{
                    data: @json($categoryData->pluck('value')),
                    backgroundColor: @json($categoryData->pluck('color')),
                }]
            }
        });

        // Member Chart
        @if(Auth::user()->role === 'admin')
            const memberCtx = document.getElementById('memberChart').getContext('2d');
            new Chart(memberCtx, {
                type: 'bar',
                data: {
                    labels: @json($memberData->pluck('label')),
                    datasets: [{
                        label: 'Total Spent',
                        data: @json($memberData->pluck('value')),
                        backgroundColor: '#4F46E5',
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        @endif

        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($trendData->pluck('month')),
                datasets: [{
                    label: 'Monthly Total',
                    data: @json($trendData->pluck('total')),
                    borderColor: '#10B981',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>