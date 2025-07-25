@if($categories->count())
    @foreach ($categories as $item)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-2 border-b">{{ $item->id }}</td>
            <td class="px-4 py-2 border-b">{{ $item->name }}</td>
            <td class="px-4 py-2 border-b space-x-2">
                <a href="{{ route('admin.category.edit', $item->id) }}"
                    class="inline-block px-3 py-1 bg-blue-500 text-white text-xs font-medium rounded hover:bg-blue-600 transition">
                    Edit
                </a>
                <button type="button"
                    class="inline-block px-3 py-1 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition btn-delete"
                    data-id="{{ $item->id }}"
                    data-url="{{ route('admin.category.destroy', $item->id) }}">
                    Delete
                </button>
                <a href="{{ route('admin.category.show', $item->id) }}"
                    class="inline-block px-3 py-1 bg-green-500 text-white text-xs font-medium rounded hover:bg-green-600 transition">
                    View Details
                </a>
                <button type="button"
                    class="inline-block px-3 py-1 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition btn-view"
                    data-id="{{ $item->id }}"
                    data-url="{{ route('admin.category.show', $item->id) }}">
                    Quick View
                </button>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="3" class="px-4 py-2 border-b text-center text-gray-500">No categories found.</td>
    </tr>
@endif