@extends('frontend.layouts.app')

@section('title', 'Conversation')

@section('styles')
<style>
    body { background: #f5f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .conversation-container { max-width: 900px; margin: 0 auto; padding: 2rem 1rem; }
    .back-link { color: #6366f1; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
    .back-link:hover { color: #4f46e5; }
    
    .conversation-header { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid #6366f1; }
    .conversation-title { margin: 0 0 0.5rem 0; font-size: 1.5rem; font-weight: 700; color: #1a202c; }
    .conversation-meta { display: flex; gap: 1.5rem; color: #718096; font-size: 0.875rem; }
    .meta-item { display: flex; align-items: center; gap: 0.5rem; }
    
    .messages-thread { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 1.5rem; max-height: 600px; overflow-y: auto; }
    .message-item { margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid #e2e8f0; }
    .message-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
    
    .message-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; }
    .sender-info { display: flex; align-items: center; gap: 0.75rem; }
    .sender-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; }
    .sender-avatar.parent { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
    .sender-avatar.teacher { background: linear-gradient(135deg, #10b981, #059669); }
    .sender-name { font-weight: 700; color: #1a202c; font-size: 1rem; }
    .message-date { color: #a0aec0; font-size: 0.875rem; }
    
    .message-body { color: #4a5568; line-height: 1.6; padding: 1rem; background: #f7fafc; border-radius: 8px; border-left: 3px solid #e2e8f0; }
    .message-item.from-me .message-body { background: linear-gradient(135deg, #f5f3ff 0%, #f7fafc 100%); border-left-color: #6366f1; }
    
    .reply-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 2rem; }
    .reply-title { margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 700; color: #1a202c; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; font-size: 0.875rem; }
    .form-textarea { width: 100%; padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; min-height: 150px; resize: vertical; font-family: inherit; }
    .form-textarea:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    
    .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.2s; }
    .btn-primary:hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
</style>
@endsection

@section('content')
<div class="conversation-container">
    <a href="{{ route('parent.messages.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Messages
    </a>
    
    <div class="conversation-header">
        <h1 class="conversation-title">Conversation with {{ $message->teacher->name }}</h1>
        <div class="conversation-meta">
            <span class="meta-item">
                <i class="fas fa-user-graduate"></i> Regarding: {{ $message->student->name }}
            </span>
            <span class="meta-item">
                <i class="fas fa-comments"></i> {{ $thread->count() }} message{{ $thread->count() != 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border-left: 4px solid #10b981; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; color: #065f46;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="messages-thread">
        @foreach($thread as $msg)
            <div class="message-item {{ $msg->sender_id == Auth::id() ? 'from-me' : '' }}">
                <div class="message-header">
                    <div class="sender-info">
                        <div class="sender-avatar {{ $msg->sender_id == Auth::id() ? 'parent' : 'teacher' }}">
                            {{ strtoupper(substr($msg->sender->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="sender-name">
                                {{ $msg->sender_id == Auth::id() ? 'You' : $msg->sender->name }}
                            </div>
                            <div class="message-date">{{ $msg->created_at->format('M d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>
                <div class="message-body">
                    {{ $msg->message }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="reply-card">
        <h2 class="reply-title"><i class="fas fa-reply"></i> Reply to this conversation</h2>
        
        @if($errors->any())
            <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; color: #991b1b;">
                <strong><i class="fas fa-exclamation-circle"></i> Error:</strong>
                {{ $errors->first() }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('parent.messages.reply', $message->id) }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="message">Your Reply</label>
                <textarea name="message" id="message" class="form-textarea" required placeholder="Type your message here...">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane"></i> Send Reply
            </button>
        </form>
    </div>
</div>

<script>
// Auto-scroll to bottom of messages thread on load
document.addEventListener('DOMContentLoaded', function() {
    const messagesThread = document.querySelector('.messages-thread');
    if (messagesThread) {
        messagesThread.scrollTop = messagesThread.scrollHeight;
    }
});
</script>
@endsection
