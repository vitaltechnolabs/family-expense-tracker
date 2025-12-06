@forelse ($expenses as $expense)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $expense->date->format('d M Y') }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
                {{ $expense->category->category_name }}
            </div>
            <div class="text-xs text-gray-500">
                {{ $expense->tag ? $expense->tag->tag_name : '' }}
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
            ₹{{ number_format($expense->amount, 2) }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $expense->user->name }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            @can('update', $expense)
                <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
            @endcan
            @can('delete', $expense)
                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
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