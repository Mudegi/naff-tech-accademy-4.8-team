@extends('frontend.layouts.app')

@section('title', 'Messages')

@section('styles')
<style>
    body { background: #f5f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .messages-container { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; }
    .page-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
    .page-title { font-size: 1.875rem; font-weight: 700; color: #1a202c; margin: 0; }
    .compose-btn { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
    .compose-btn:hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
    
    .conversations-list { display: flex; flex-direction: column; gap: 1rem; }
    .conversation-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 1.5rem; transition: all 0.2s; border: 2px solid transparent; position: relative; }
    .conversation-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.12); border-color: #6366f1; transform: translateY(-2px); }
    .conversation-card.unread { border-left: 4px solid #6366f1; background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%); }
    
    .conversation-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; }
    .conversation-info h3 { margin: 0 0 0.25rem 0; font-size: 1.125rem; font-weight: 700; color: #1a202c; }
    .conversation-meta { display: flex; gap: 1rem; color: #718096; font-size: 0.875rem; }
    .meta-item { display: flex; align-items: center; gap: 0.25rem; }
    .conversation-date { color: #a0aec0; font-size: 0.875rem; }
    .unread-badge { background: #6366f1; color: white; padding: 0.25rem 0.625rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
    
    .last-message { color: #4a5568; line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .message-sender { font-weight: 600; color: #2d3748; }
    
    .conversation-actions { display: flex; gap: 0.5rem; }
    .view-btn { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 0.5rem 1.25rem; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 0.875rem; transition: all 0.2s; }
    .view-btn:hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; transform: translateY(-1px); }
    
    .empty-state { background: white; padding: 4rem 2rem; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .empty-icon { font-size: 4rem; color: #cbd5e0; margin-bottom: 1rem; }
    .empty-state h3 { font-size: 1.5rem; font-weight: 700; color: #1a202c; margin-bottom: 0.5rem; }
    .empty-state p { color: #718096; margin-bottom: 1.5rem; }
</style>
@endsection

@section('content')
<div class="messages-container">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-envelope"></i> Messages</h1>
        <a href="{{ route('parent.messages.create') }}" class="compose-btn">
            <i class="fas fa-pen"></i> New Message
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border-left: 4px solid #10b981; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; color: #065f46;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($conversations->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
            <h3>No Messages Yet</h3>
            <p>Start a conversation with your child's teachers to stay updated on their progress.</p>
            <a href="{{ route('parent.messages.create') }}" class="compose-btn">
                <i class="fas fa-pen"></i> Send Your First Message
            </a>
        </div>
    @else
        <div class="conversations-list">
            @foreach($conversations as $threadKey => $messages)
                @php
                    $latestMessage = $messages->first();
                    $unreadCount = $messages->where('read_by_recipient', false)
                                           ->where('sender_id', '!=', Auth::id())
                                           ->count();
                @endphp
                <div class="conversation-card {{ $unreadCount > 0 ? 'unread' : '' }}">
                    <div class="conversation-header">
                        <div class="conversation-info">
                            <h3>{{ $latestMessage->teacher->name }}</h3>
                            <div class="conversation-meta">
                                <span class="meta-item">
                                    <i class="fas fa-user-graduate"></i> Re: {{ $latestMessage->student->name }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-comments"></i> {{ $messages->count() }} message{{ $messages->count() != 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                            <span class="conversation-date">{{ $latestMessage->created_at->diffForHumans() }}</span>
                            @if($unreadCount > 0)
                                <span class="unread-badge">{{ $unreadCount }} new</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="last-message">
                        <span class="message-sender">{{ $latestMessage->sender->id == Auth::id() ? 'You' : $latestMessage->sender->name }}:</span>
                        {{ Str::limit($latestMessage->message, 150) }}
                    </div>
                    
                    <div class="conversation-actions">
                        <a href="{{ route('parent.messages.show', $latestMessage->id) }}" class="view-btn">
                            <i class="fas fa-eye"></i> View Conversation
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
