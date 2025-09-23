@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold mb-4">{{ $item->name }}</h1>

        @if($item->description)
            <div class="prose max-w-none">
                {{ $item->description }}
            </div>
        @endif

        <div class="mt-8">
            <a href="{{ route('inventory.index') }}"
               class="text-blue-600 hover:text-blue-800">
                ← Back to Inventory
            </a>
        </div>
    </div>
</div>
@endsection