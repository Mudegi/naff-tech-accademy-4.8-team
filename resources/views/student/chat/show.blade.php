@extends('layouts.student-dashboard')

@section('title', $conversation->getDisplayTitle(auth()->id()))

@section('content')
<div class="chat-container">
    <div class="chat-header">
        <div class="chat-header-left">
            <a href="{{ route('student.chat.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="chat-info">
                <h1>{{ $conversation->getDisplayTitle(auth()->id()) }}</h1>
                @if($conversation->type === 'group')
                    <p class="participants-count">{{ $otherParticipants->count() + 1 }} members</p>
                @else
                    <p class="online-status">
                        <span class="status-dot"></span>
                        Online
                    </p>
                @endif
            </div>
        </div>
        <div class="chat-header-right">
            @if($conversation->type === 'group')
                <button class="btn btn-secondary" id="groupInfoBtn">
                    <i class="fas fa-info-circle"></i>
                </button>
            @endif
            <button class="btn btn-secondary" id="leaveChatBtn">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </div>

    <div class="chat-content">
        <!-- Messages Area -->
        <div class="messages-container" id="messagesContainer">
            <div class="messages-list" id="messagesList">
                @if($messages->count() > 0)
                    @foreach($messages as $message)
                        <div class="message-item {{ $message->user_id === auth()->id() ? 'own-message' : 'other-message' }}" data-message-id="{{ $message->id }}" data-user-id="{{ $message->user_id }}">
                            <div class="message-avatar">
                                @if($message->user_id === auth()->id())
                                    <i class="fas fa-user"></i>
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="sender-name">{{ $message->user->name }}</span>
                                    <span class="message-time">{{ $message->getFormattedTime() }}</span>
                                </div>
                                @if(!empty($message->message))
                                    <div class="message-text">{{ $message->message }}</div>
                                @endif

                                @if($message->attachment_url)
                                    <div class="message-attachment {{ $message->type === 'image' ? 'image-attachment' : 'file-attachment' }}">
                                        @if($message->type === 'image')
                                            <img src="{{ $message->attachment_url }}" alt="Chat image attachment">
                                            <a href="{{ $message->attachment_url }}" target="_blank" class="attachment-action">
                                                <i class="fas fa-arrow-down"></i> Download image
                                            </a>
                                        @else
                                            <div class="file-info">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>PDF Attachment</span>
                                            </div>
                                            <a href="{{ $message->attachment_url }}" target="_blank" class="attachment-action">
                                                <i class="fas fa-download"></i> View PDF
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-messages">
                        <i class="fas fa-comments"></i>
                        <h3>No messages yet</h3>
                        <p>Start the conversation by sending a message below.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Message Input -->
        <div class="message-input-container">
            <form id="messageForm" class="message-form">
                <div class="message-input-wrapper">
                    <label for="attachmentInput" class="attachment-btn" title="Attach PDF or PNG">
                        <i class="fas fa-paperclip"></i>
                        <input type="file" id="attachmentInput" accept=".pdf,.png">
                    </label>
                    <textarea id="messageInput" placeholder="Type your message..." rows="1"></textarea>
                    <button type="submit" class="send-btn" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="attachment-preview" id="attachmentPreview" style="display: none;">
                    <div class="preview-details">
                        <i class="fas fa-paperclip"></i>
                        <span id="attachmentName"></span>
                    </div>
                    <button type="button" class="remove-attachment" id="removeAttachmentBtn" title="Remove attachment">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Group Info Modal -->
@if($conversation->type === 'group')
<div id="groupInfoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Group Information</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="group-details">
                <h4>{{ $conversation->title }}</h4>
                @if($conversation->description)
                    <p>{{ $conversation->description }}</p>
                @endif
                <div class="group-members">
                    <div class="members-header">
                        <h5>Members ({{ $otherParticipants->count() + 1 }})</h5>
                        <button id="addMembersBtn" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Members
                        </button>
                    </div>
                    <div class="members-list" id="membersList">
                        <div class="member-item">
                            <div class="member-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="member-info">
                                <span class="member-name">{{ auth()->user()->name }}</span>
                                <span class="member-role">You</span>
                            </div>
                        </div>
                        @foreach($otherParticipants as $participant)
                            <div class="member-item" data-user-id="{{ $participant->id }}">
                                <div class="member-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="member-info">
                                    <span class="member-name">{{ $participant->name }}</span>
                                    <span class="member-role">Member</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Add Members Modal -->
<div id="addMembersModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Members to Group</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="search-section">
                <div class="search-input-wrapper">
                    <input type="text" id="searchNewMembers" placeholder="Search for students to add...">
                    <i class="fas fa-search search-icon"></i>
                    <div class="search-loading" id="searchNewMembersLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>
                <div class="search-hint" id="searchNewMembersHint">
                    Type at least 2 characters to search for students
                </div>
            </div>
            
            <div class="users-list" id="newMembersList"></div>
            
            <div class="selected-members-section">
                <div class="selected-members-header">
                    <h5>Selected Members (<span id="selectedNewMembersCount">0</span>)</h5>
                </div>
                <div class="selected-members-list" id="selectedNewMembersList">
                    <div class="no-members-selected">No members selected yet</div>
                </div>
            </div>
            
            <div class="add-members-section">
                <button type="button" id="addSelectedNewMembersBtn" class="btn btn-outline-primary" style="display: none;">
                    <i class="fas fa-plus"></i> Add Selected Members
                </button>
                <button type="button" id="addMembersToGroupBtn" class="btn btn-primary" onclick="addMembersToGroup()" style="display: none;">
                    <i class="fas fa-users"></i> Add to Group
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 120px);
    max-width: 1200px;
    margin: 0 auto;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.chat-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s;
}

.back-btn:hover {
    background: #d1d5db;
    color: #374151;
}

.chat-info h1 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.participants-count,
.online-status {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.chat-header-right {
    display: flex;
    gap: 0.5rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-secondary {
    background: #e5e7eb;
    color: #6b7280;
}

.btn-secondary:hover {
    background: #d1d5db;
    color: #374151;
}

.chat-content {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow: hidden;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.messages-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message-item {
    display: flex;
    gap: 0.75rem;
    max-width: 70%;
}

.message-item.own-message {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-item.other-message {
    align-self: flex-start;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.message-content {
    background: #f3f4f6;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    max-width: 100%;
}

.own-message .message-content {
    background: #2563eb;
    color: white;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}

.sender-name {
    font-weight: 600;
    font-size: 0.875rem;
}

.own-message .sender-name {
    color: rgba(255, 255, 255, 0.8);
}

.message-time {
    font-size: 0.75rem;
    color: #9ca3af;
}

.own-message .message-time {
    color: rgba(255, 255, 255, 0.7);
}

.message-text {
    word-wrap: break-word;
    line-height: 1.4;
}

.message-attachment {
    margin-top: 0.5rem;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    padding: 0.5rem;
    background: #f9fafb;
}

.message-attachment img {
    max-width: 220px;
    border-radius: 0.5rem;
    display: block;
}

.message-attachment .file-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #374151;
    font-weight: 600;
}

.message-attachment .attachment-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.85rem;
    color: #2563eb;
    font-weight: 600;
    margin-top: 0.5rem;
}

.no-messages {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.no-messages i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.message-input-container {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
}

.message-form {
    width: 100%;
}

.message-input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 1.5rem;
    padding: 0.75rem 1rem;
}

.attachment-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #eef2ff;
    border: 1px dashed #c7d2fe;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4338ca;
    cursor: pointer;
    flex-shrink: 0;
}

.attachment-btn input[type="file"] {
    display: none;
}

.attachment-preview {
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.75rem;
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.attachment-preview .preview-details {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #1e3a8a;
    font-size: 0.875rem;
    font-weight: 500;
}

.remove-attachment {
    background: transparent;
    border: none;
    color: #1e3a8a;
    cursor: pointer;
    font-size: 1rem;
}

#messageInput {
    flex: 1;
    border: none;
    outline: none;
    resize: none;
    font-size: 0.875rem;
    line-height: 1.4;
    max-height: 120px;
    min-height: 20px;
}

.send-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    background: #2563eb;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}

.send-btn:hover {
    background: #1d4ed8;
}

.send-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 0.75rem;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.close {
    font-size: 1.5rem;
    font-weight: bold;
    cursor: pointer;
    color: #6b7280;
}

.close:hover {
    color: #374151;
}

.modal-body {
    padding: 1.5rem;
}

.group-details h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.group-details p {
    color: #6b7280;
    margin-bottom: 1.5rem;
}

.group-members h5 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    font-weight: 600;
}

.members-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.member-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.member-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.member-info {
    display: flex;
    flex-direction: column;
}

.member-name {
    font-weight: 500;
    color: #1a1a1a;
}

.member-role {
    font-size: 0.875rem;
    color: #6b7280;
}

.members-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

.btn-primary {
    background: #2563eb;
    color: white;
    border: none;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.search-section {
    margin-bottom: 1.5rem;
}

.search-input-wrapper {
    position: relative;
    margin-bottom: 0.5rem;
}

.search-input-wrapper input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    outline: none;
    transition: border-color 0.2s;
}

.search-input-wrapper input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    pointer-events: none;
}

.search-loading {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
}

.search-hint {
    font-size: 0.75rem;
    color: #6b7280;
    text-align: center;
}

.users-list {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.user-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background-color 0.2s;
}

.user-item:hover {
    background: #f9fafb;
}

.user-item:last-child {
    border-bottom: none;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.user-info {
    flex: 1;
}

.user-name {
    font-weight: 500;
    color: #1a1a1a;
    font-size: 0.875rem;
}

.user-email {
    font-size: 0.75rem;
    color: #6b7280;
}

.user-checkbox {
    margin: 0;
}

.selected-members-section {
    margin-bottom: 1rem;
}

.selected-members-header h5 {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.selected-members-list {
    min-height: 60px;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 0.75rem;
    background: #f9fafb;
}

.no-members-selected {
    text-align: center;
    color: #6b7280;
    font-size: 0.875rem;
    padding: 1rem;
}

.selected-member {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #2563eb;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    margin: 0.25rem;
}

.selected-member .remove-member {
    cursor: pointer;
    margin-left: 0.25rem;
    opacity: 0.8;
}

.selected-member .remove-member:hover {
    opacity: 1;
}

.add-members-section {
    text-align: center;
}

.btn-outline-primary {
    background: transparent;
    color: #2563eb;
    border: 1px solid #2563eb;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-outline-primary:hover {
    background: #2563eb;
    color: white;
}

.no-results {
    text-align: center;
    color: #6b7280;
    font-size: 0.875rem;
    padding: 1rem;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .chat-container {
        height: calc(100vh - 80px);
        border-radius: 0;
    }
    
    .message-item {
        max-width: 85%;
    }
    
    .chat-header {
        padding: 0.75rem 1rem;
    }
    
    .messages-container {
        padding: 0.75rem;
    }
    
    .message-input-container {
        padding: 0.75rem 1rem;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const messagesList = document.getElementById('messagesList');
    const messagesContainer = document.getElementById('messagesContainer');
    const groupInfoBtn = document.getElementById('groupInfoBtn');
    const groupInfoModal = document.getElementById('groupInfoModal');
    const leaveChatBtn = document.getElementById('leaveChatBtn');
    const addMembersBtn = document.getElementById('addMembersBtn');
    const addMembersModal = document.getElementById('addMembersModal');
    const attachmentInput = document.getElementById('attachmentInput');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const attachmentName = document.getElementById('attachmentName');
    const removeAttachmentBtn = document.getElementById('removeAttachmentBtn');
    
    const conversationId = {{ $conversation->id }};
    const currentUserId = {{ auth()->id() }};
    let isLoading = false;
    let selectedNewMembers = [];
    let searchTimeout;
    let attachmentFile = null;

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    if (attachmentInput) {
        attachmentInput.addEventListener('change', function() {
            if (!this.files || !this.files[0]) {
                clearAttachment();
                return;
            }

            const file = this.files[0];
            const allowedTypes = ['application/pdf', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Only PDF and PNG attachments are allowed.');
                clearAttachment();
                return;
            }

            attachmentFile = file;
            attachmentName.textContent = file.name;
            attachmentPreview.style.display = 'flex';
        });
    }

    if (removeAttachmentBtn) {
        removeAttachmentBtn.addEventListener('click', function() {
            clearAttachment();
        });
    }

    // Send message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if ((message.length === 0 && !attachmentFile) || isLoading) return;
        
        sendMessage(message);
    });

    // Enter key to send (Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });

    // Group info modal
    if (groupInfoBtn) {
        groupInfoBtn.addEventListener('click', () => {
            groupInfoModal.style.display = 'block';
        });
    }

    // Add members modal
    if (addMembersBtn) {
        addMembersBtn.addEventListener('click', () => {
            addMembersModal.style.display = 'block';
            selectedNewMembers = [];
            updateSelectedNewMembers();
        });
    }

    // Close modal
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            const modal = e.target.closest('.modal');
            modal.style.display = 'none';
            if (modal.id === 'addMembersModal') {
                // Reset add members modal
                document.getElementById('searchNewMembers').value = '';
                document.getElementById('newMembersList').innerHTML = '';
                document.getElementById('addSelectedNewMembersBtn').style.display = 'none';
                document.getElementById('addMembersToGroupBtn').style.display = 'none';
                selectedNewMembers = [];
            }
        });
    });

    // Search for new members
    const searchNewMembersInput = document.getElementById('searchNewMembers');
    if (searchNewMembersInput) {
        searchNewMembersInput.addEventListener('input', function() {
            const query = this.value.trim();
            const newMembersList = document.getElementById('newMembersList');
            const searchLoading = document.getElementById('searchNewMembersLoading');
            const searchHint = document.getElementById('searchNewMembersHint');
            const addSelectedBtn = document.getElementById('addSelectedNewMembersBtn');

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                newMembersList.innerHTML = '';
                searchHint.style.display = 'block';
                addSelectedBtn.style.display = 'none';
                return;
            }

            searchHint.style.display = 'none';
            searchLoading.style.display = 'block';

            searchTimeout = setTimeout(() => {
                searchNewMembersAPI(query, newMembersList);
            }, 300);
        });
    }

    // Add Selected Members button
    const addSelectedNewMembersBtn = document.getElementById('addSelectedNewMembersBtn');
    if (addSelectedNewMembersBtn) {
        addSelectedNewMembersBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('#newMembersList input[type="checkbox"]:checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one member to add.');
                return;
            }
            
            checkboxes.forEach(checkbox => {
                const userData = JSON.parse(checkbox.dataset.user);
                if (!selectedNewMembers.find(u => u.id === userData.id)) {
                    selectedNewMembers.push(userData);
                }
            });
            
            updateSelectedNewMembers();
            
            // Clear search and hide button
            document.getElementById('searchNewMembers').value = '';
            document.getElementById('newMembersList').innerHTML = '';
            this.style.display = 'none';
        });
    }

    // Leave chat
    leaveChatBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to leave this conversation?')) {
            fetch(`/student/chat/${conversationId}/leave`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert('Error leaving conversation: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error leaving conversation. Please try again.');
            });
        }
    });

    // Auto-scroll to bottom
    scrollToBottom();

    // Debug message alignment (only for development)
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('127.0.0.1')) {
        console.log('Current user ID:', currentUserId);
        console.log('Messages in chat:');
        document.querySelectorAll('.message-item').forEach((msg, index) => {
            const userId = msg.dataset.userId;
            const isOwn = msg.classList.contains('own-message');
            console.log(`Message ${index + 1}: User ID ${userId}, Is Own: ${isOwn}, Classes: ${msg.className}`);
        });
    }

    // Poll for new messages every 3 seconds
    setInterval(loadNewMessages, 3000);

    function sendMessage(message) {
        isLoading = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const formData = new FormData();
        if (message.length > 0) {
            formData.append('message', message);
        }
        if (attachmentFile) {
            formData.append('attachment', attachmentFile);
        }

        fetch(`/student/chat/${conversationId}/message`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
                clearAttachment();
                addMessageToChat(data.message);
                scrollToBottom();
            } else {
                alert('Error sending message: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending message. Please try again.');
        })
        .finally(() => {
            isLoading = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        });
    }

    function addMessageToChat(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-item ${message.user_id === currentUserId ? 'own-message' : 'other-message'}`;
        messageDiv.dataset.messageId = message.id;
        messageDiv.dataset.userId = message.user_id;
        
        const senderName = message.user && message.user.name ? message.user.name : 'You';
        const hasText = message.message && message.message.trim().length > 0;
        const hasAttachment = Boolean(message.attachment_url);
        let attachmentHtml = '';

        if (hasAttachment) {
            if (message.type === 'image') {
                attachmentHtml = `
                    <div class="message-attachment image-attachment">
                        <img src="${message.attachment_url}" alt="Image attachment">
                        <a href="${message.attachment_url}" target="_blank" class="attachment-action">
                            <i class="fas fa-arrow-down"></i> Download image
                        </a>
                    </div>
                `;
            } else {
                attachmentHtml = `
                    <div class="message-attachment file-attachment">
                        <div class="file-info">
                            <i class="fas fa-file-pdf"></i>
                            <span>PDF Attachment</span>
                        </div>
                        <a href="${message.attachment_url}" target="_blank" class="attachment-action">
                            <i class="fas fa-download"></i> View PDF
                        </a>
                    </div>
                `;
            }
        }

        messageDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="sender-name">${senderName}</span>
                    <span class="message-time">${new Date(message.created_at).toLocaleTimeString()}</span>
                </div>
                ${hasText ? `<div class="message-text">${escapeHtml(message.message)}</div>` : ''}
                ${attachmentHtml}
            </div>
        `;
        
        messagesList.appendChild(messageDiv);
    }

    function clearAttachment() {
        attachmentFile = null;
        if (attachmentPreview) {
            attachmentPreview.style.display = 'none';
        }
        if (attachmentName) {
            attachmentName.textContent = '';
        }
        if (attachmentInput) {
            attachmentInput.value = '';
        }
    }

    function loadNewMessages() {
        // This would be implemented to check for new messages
        // For now, we'll just keep the polling structure
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Add Members functionality
    function searchNewMembersAPI(query, container) {
        const searchLoading = document.getElementById('searchNewMembersLoading');
        const addSelectedBtn = document.getElementById('addSelectedNewMembersBtn');
        
        fetch(`/student/chat/search/users?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.style.display = 'none';
                
                if (data.users && data.users.length > 0) {
                    // Filter out users who are already in the group
                    const existingMemberIds = Array.from(document.querySelectorAll('#membersList .member-item[data-user-id]'))
                        .map(item => parseInt(item.dataset.userId));
                    
                    const availableUsers = data.users.filter(user => !existingMemberIds.includes(user.id));
                    
                    if (availableUsers.length > 0) {
                        renderNewMembersList(availableUsers, container);
                        addSelectedBtn.style.display = 'block';
                    } else {
                        container.innerHTML = '<div class="no-results">All found users are already members of this group</div>';
                        addSelectedBtn.style.display = 'none';
                    }
                } else {
                    container.innerHTML = '<div class="no-results">No users found</div>';
                    addSelectedBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error searching users:', error);
                searchLoading.style.display = 'none';
                container.innerHTML = '<div class="no-results">Error searching users</div>';
                addSelectedBtn.style.display = 'none';
            });
    }

    function renderNewMembersList(users, container) {
        container.innerHTML = users.map(user => `
            <div class="user-item">
                <input type="checkbox" class="user-checkbox" data-user='${JSON.stringify(user)}'>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <div class="user-name">${escapeHtml(user.name)}</div>
                    <div class="user-email">${escapeHtml(user.email)}</div>
                </div>
            </div>
        `).join('');
    }

    function updateSelectedNewMembers() {
        const countElement = document.getElementById('selectedNewMembersCount');
        const listElement = document.getElementById('selectedNewMembersList');
        const addToGroupBtn = document.getElementById('addMembersToGroupBtn');
        
        countElement.textContent = selectedNewMembers.length;
        
        if (selectedNewMembers.length === 0) {
            listElement.innerHTML = '<div class="no-members-selected">No members selected yet</div>';
            addToGroupBtn.style.display = 'none';
        } else {
            listElement.innerHTML = selectedNewMembers.map(member => `
                <div class="selected-member">
                    <span>${escapeHtml(member.name)}</span>
                    <span class="remove-member" onclick="removeSelectedNewMember(${member.id})">&times;</span>
                </div>
            `).join('');
            addToGroupBtn.style.display = 'block';
        }
    }

    function removeSelectedNewMember(userId) {
        selectedNewMembers = selectedNewMembers.filter(member => member.id !== userId);
        updateSelectedNewMembers();
    }

    // Add members to group
    function addMembersToGroup() {
        if (selectedNewMembers.length === 0) {
            alert('Please select at least one member to add.');
            return;
        }

        const memberIds = selectedNewMembers.map(member => member.id);
        
        fetch(`/student/chat/${conversationId}/add-members`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ participants: memberIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Successfully added ${data.added_count} member(s) to the group!`);
                addMembersModal.style.display = 'none';
                // Refresh the page to show new members
                location.reload();
            } else {
                alert('Error adding members: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding members. Please try again.');
        });
    }

    // Make functions globally available
    window.removeSelectedNewMember = removeSelectedNewMember;
    window.addMembersToGroup = addMembersToGroup;
});
</script>
@endpush

@endsection
