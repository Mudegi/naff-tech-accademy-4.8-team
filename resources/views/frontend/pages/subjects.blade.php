@extends('frontend.layouts.app')

@section('title', 'Subjects')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Our Subjects</h1>
    
    @if($subjects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($subjects as $subject)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2">{{ $subject->name }}</h2>
                        <p class="text-gray-600 mb-4">{{ $subject->description }}</p>
                        <a href="{{ route('subjects.show', $subject->hash_id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Learn More</a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $subjects->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600">No subjects available at the moment.</p>
        </div>
    @endif
</div>
@endsection 