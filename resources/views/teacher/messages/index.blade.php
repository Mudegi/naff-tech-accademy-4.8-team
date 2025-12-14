@extends('layouts.dashboard')

@section('title', 'Parent Messages')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-envelope"></i> Parent Messages
            </h1>
            <p class="text-muted mb-0">Messages from parents about their children</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($conversations->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 mb-2">No Messages Yet</h4>
                <p class="text-muted">You haven't received any messages from parents yet.</p>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($conversations as $threadKey => $messages)
                @php
                    $latestMessage = $messages->first();
                    $unreadCount = $messages->where('read_by_recipient', false)
                                           ->where('sender_id', '!=', Auth::id())
                                           ->count();
                @endphp
                <div class="col-12 mb-3">
                    <div class="card border-0 shadow-sm {{ $unreadCount > 0 ? 'border-start border-primary border-4' : '' }}" style="transition: all 0.2s;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar-circle bg-gradient-primary text-white me-3" style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                            {{ strtoupper(substr($latestMessage->parent->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">
                                                {{ $latestMessage->parent->name }}
                                                @if($unreadCount > 0)
                                                    <span class="badge bg-primary">{{ $unreadCount }} new</span>
                                                @endif
                                            </h5>
                                            <div class="text-muted small mb-2">
                                                <i class="fas fa-user-graduate"></i> Re: {{ $latestMessage->student->name }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-comments"></i> {{ $messages->count() }} message{{ $messages->count() != 1 ? 's' : '' }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-clock"></i> {{ $latestMessage->created_at->diffForHumans() }}
                                            </div>
                                            <div class="text-truncate" style="max-width: 600px;">
                                                <strong>{{ $latestMessage->sender->id == Auth::id() ? 'You' : $latestMessage->sender->name }}:</strong>
                                                {{ Str::limit($latestMessage->message, 100) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="{{ route('teacher.messages.show', $latestMessage->id) }}" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> View Conversation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
