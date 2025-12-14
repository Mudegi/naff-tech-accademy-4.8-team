@extends('layouts.dashboard')

@section('title', 'Conversation')

@section('content')
<div class="container-fluid py-4">
    <a href="{{ route('teacher.messages.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
        <i class="fas fa-arrow-left me-1"></i>Back to Messages
    </a>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">
                        <i class="fas fa-user"></i> {{ $message->parent->name }}
                    </h4>
                    <div class="text-muted">
                        <i class="fas fa-user-graduate"></i> Regarding: <strong>{{ $message->student->name }}</strong>
                        <span class="mx-2">•</span>
                        <i class="fas fa-comments"></i> {{ $thread->count() }} message{{ $thread->count() != 1 ? 's' : '' }}
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    @if($message->parent->phone_number)
                        <a href="tel:{{ $message->parent->phone_number }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-phone"></i> {{ $message->parent->phone_number }}
                        </a>
                    @endif
                    @if($message->parent->email)
                        <a href="mailto:{{ $message->parent->email }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-envelope"></i> Email
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card-body" style="max-height: 600px; overflow-y: auto;" id="messageThread">
            @foreach($thread as $msg)
                <div class="message-item mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-start {{ $msg->sender_id == Auth::id() ? 'flex-row-reverse' : '' }}">
                        <div class="avatar-circle {{ $msg->sender_id == Auth::id() ? 'bg-success' : 'bg-primary' }} text-white me-3 {{ $msg->sender_id == Auth::id() ? 'ms-3 me-0' : '' }}" 
                             style="width: 40px; height: 40px; min-width: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            {{ strtoupper(substr($msg->sender->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 {{ $msg->sender_id == Auth::id() ? 'text-end' : '' }}">
                            <div class="mb-1">
                                <strong>{{ $msg->sender_id == Auth::id() ? 'You' : $msg->sender->name }}</strong>
                                <span class="text-muted small ms-2">{{ $msg->created_at->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                            <div class="message-bubble {{ $msg->sender_id == Auth::id() ? 'bg-success text-white' : 'bg-light' }} p-3 rounded" 
                                 style="display: inline-block; max-width: 70%; {{ $msg->sender_id == Auth::id() ? 'float: right;' : '' }}">
                                {{ $msg->message }}
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3"><i class="fas fa-reply"></i> Reply to Parent</h5>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-circle"></i> Error:</strong>
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form method="POST" action="{{ route('teacher.messages.reply', $message->id) }}">
                @csrf
                
                <div class="mb-3">
                    <label for="message" class="form-label">Your Reply</label>
                    <textarea name="message" id="message" class="form-control" rows="4" required placeholder="Type your message here...">{{ old('message') }}</textarea>
                    <div class="form-text">Minimum 10 characters. Be professional and constructive.</div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Reply
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-scroll to bottom of messages thread on load
document.addEventListener('DOMContentLoaded', function() {
    const messageThread = document.getElementById('messageThread');
    if (messageThread) {
        messageThread.scrollTop = messageThread.scrollHeight;
    }
});
</script>
@endsection
