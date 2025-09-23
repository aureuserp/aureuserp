@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Inventory</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($inventory as $item)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-2">{{ $item->name }}</h2>
                <p class="text-gray-600 mb-4">{{ $item->description }}</p>
                <a href="{{ route('inventory.show', $item->id) }}"
                   class="text-blue-600 hover:text-blue-800">
                    Read More
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $inventory->links() }}
    </div>
</div>
@endsection