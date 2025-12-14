@extends('layouts.student-dashboard')

@section('title', 'Chat')

@section('content')
<div class="chat-container">
    <div class="chat-header">
        <h1>Chat</h1>
        <div class="chat-actions">
            <button id="newChatBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Chat
            </button>
            <button id="newGroupBtn" class="btn btn-secondary">
                <i class="fas fa-users"></i> New Group
            </button>
        </div>
    </div>

    <div class="chat-content">
        <!-- Conversations List -->
        <div class="conversations-sidebar">
            <div class="search-box">
                <input type="text" id="searchConversations" placeholder="Search conversations...">
                <i class="fas fa-search"></i>
            </div>
            
            <div class="conversations-list" id="conversationsList">
                @if($conversations->count() > 0)
                    @foreach($conversations as $conversation)
                        <div class="conversation-item" data-conversation-id="{{ $conversation->id }}">
                            <div class="conversation-avatar">
                                @if($conversation->type === 'private')
                                    <i class="fas fa-user"></i>
                                @else
                                    <i class="fas fa-users"></i>
                                @endif
                            </div>
                            <div class="conversation-details">
                                <div class="conversation-name">
                                    {{ $conversation->getDisplayTitle(auth()->id()) }}
                                </div>
                                <div class="conversation-preview">
                                    @if($conversation->messages->count() > 0)
                                        @php $latestMessage = $conversation->messages->first(); @endphp
                                        <span class="message-preview">
                                            {{ $latestMessage->user_id === auth()->id() ? 'You: ' : '' }}{{ Str::limit($latestMessage->message, 50) }}
                                        </span>
                                        <span class="message-time">{{ $latestMessage->created_at->diffForHumans() }}</span>
                                    @else
                                        <span class="no-messages">No messages yet</span>
                                    @endif
                                </div>
                            </div>
                            @if($conversation->unread_count > 0)
                                <div class="unread-badge">{{ $conversation->unread_count }}</div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="no-conversations">
                        <i class="fas fa-comments"></i>
                        <h3>No conversations yet</h3>
                        <p>Start a new chat with other students or create a group discussion.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-main">
            <div class="chat-welcome">
                <i class="fas fa-comments"></i>
                <h2>Welcome to Chat</h2>
                <p>Select a conversation to start chatting or create a new one.</p>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div id="newChatModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Start New Chat</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="search-users">
                <div class="search-input-wrapper">
                    <input type="text" id="searchUsers" placeholder="Search for students..." autocomplete="off">
                    <i class="fas fa-search search-icon"></i>
                    <div class="search-loading" id="searchLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>
                <div id="usersList" class="users-list"></div>
                <div class="search-hint" id="searchHint">
                    <i class="fas fa-info-circle"></i>
                    <span>Type at least 2 characters to search for students</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Group Modal -->
<div id="newGroupModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create Group Chat</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="createGroupForm">
                <div class="form-group">
                    <label for="groupTitle">Group Name</label>
                    <input type="text" id="groupTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label for="groupDescription">Description (Optional)</label>
                    <textarea id="groupDescription" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Add Members</label>
                    <input type="text" id="searchGroupUsers" placeholder="Search for students...">
                    <div id="groupUsersList" class="users-list"></div>
                    <div id="selectedMembers" class="selected-members"></div>
                    <div class="add-members-section">
                        <button type="button" id="addSelectedMembersBtn" class="btn btn-outline-primary" style="display: none;">
                            <i class="fas fa-plus"></i> Add Selected Members
                        </button>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('newGroupModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.chat-container {
    padding: 1rem;
    max-width: 1400px;
    margin: 0 auto;
    height: calc(100vh - 120px);
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.chat-header h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.chat-actions {
    display: flex;
    gap: 0.75rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.chat-content {
    display: flex;
    gap: 1rem;
    height: calc(100% - 80px);
}

.conversations-sidebar {
    width: 350px;
    min-width: 350px;
    max-width: 350px;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.search-box {
    position: relative;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    box-sizing: border-box;
}

.search-box input {
    width: 100%;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    box-sizing: border-box;
}

.search-box i {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    pointer-events: none;
}

.search-box input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.conversations-list {
    flex: 1;
    overflow-y: auto;
}

.conversation-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.2s;
    position: relative;
}

.conversation-item:hover {
    background: #f9fafb;
}

.no-results {
    padding: 20px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
}

.no-results p {
    margin: 0;
}

.conversation-item.active {
    background: #eff6ff;
    border-left: 3px solid #2563eb;
}

.conversation-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    color: #6b7280;
}

.conversation-details {
    flex: 1;
    min-width: 0;
}

.conversation-name {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.conversation-preview {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
}

.message-preview {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-time {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-left: 0.5rem;
}

.unread-badge {
    background: #ef4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

.no-conversations {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.no-conversations i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.chat-main {
    flex: 1;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-welcome {
    text-align: center;
    color: #6b7280;
}

.chat-welcome i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.chat-welcome h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
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

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

.form-group textarea {
    resize: vertical;
}

.search-input-wrapper {
    position: relative;
    margin-bottom: 1rem;
}

.search-input-wrapper input {
    width: 100%;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.search-input-wrapper input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    pointer-events: none;
}

.search-loading {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #2563eb;
}

.search-hint {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: #f3f4f6;
    border-radius: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.search-hint i {
    color: #9ca3af;
}

.users-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    background: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.users-list:empty {
    display: none;
}

.user-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.2s;
}

.user-item:hover {
    background: #f9fafb;
}

.user-item:last-child {
    border-bottom: none;
}

.user-item:first-child {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}

.user-item:last-child {
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    color: white;
    font-size: 1rem;
    font-weight: 600;
}

.user-info {
    flex: 1;
}

.user-name {
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.user-email {
    font-size: 0.875rem;
    color: #6b7280;
}

.conversation-status {
    font-size: 0.75rem;
    color: #10b981;
    font-weight: 500;
    margin-top: 2px;
}

.no-users {
    padding: 2rem 1rem;
    text-align: center;
    color: #6b7280;
}

.no-users i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #d1d5db;
}

.selected-members {
    margin-top: 0.5rem;
}

.selected-members-header {
    margin-bottom: 0.5rem;
    color: #374151;
    font-size: 0.875rem;
}

.no-members-selected {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: #f9fafb;
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
    text-align: center;
}

.no-members-selected i {
    font-size: 1.25rem;
    color: #9ca3af;
}

.selected-member {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    margin: 0.25rem 0.25rem 0.25rem 0;
}

.remove-member {
    cursor: pointer;
    color: #6b7280;
}

.remove-member:hover {
    color: #ef4444;
}

.add-members-section {
    margin-top: 0.5rem;
}

.btn-outline-primary {
    background: transparent;
    border: 1px solid #3b82f6;
    color: #3b82f6;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-outline-primary:hover {
    background: #3b82f6;
    color: white;
}

.group-user-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    background: white;
    transition: all 0.2s;
}

.group-user-item:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.user-checkbox {
    display: flex;
    align-items: center;
}

.user-checkbox input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    accent-color: #3b82f6;
    cursor: pointer;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .chat-content {
        flex-direction: column;
    }
    
    .conversations-sidebar {
        width: 100%;
        height: 300px;
    }
    
    .chat-main {
        height: calc(100% - 300px);
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newChatBtn = document.getElementById('newChatBtn');
    const newGroupBtn = document.getElementById('newGroupBtn');
    const newChatModal = document.getElementById('newChatModal');
    const newGroupModal = document.getElementById('newGroupModal');
    const searchUsers = document.getElementById('searchUsers');
    const searchGroupUsers = document.getElementById('searchGroupUsers');
    const usersList = document.getElementById('usersList');
    const groupUsersList = document.getElementById('groupUsersList');
    const createGroupForm = document.getElementById('createGroupForm');
    const selectedMembers = document.getElementById('selectedMembers');
    
    let selectedUsers = [];
    let searchTimeout;

    // Open modals
    newChatBtn.addEventListener('click', () => openModal('newChatModal'));
    newGroupBtn.addEventListener('click', () => openModal('newGroupModal'));

    // Close modals
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            const modal = e.target.closest('.modal');
            closeModal(modal.id);
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target.id);
        }
    });

    // Search users for new chat
    searchUsers.addEventListener('input', function() {
        const query = this.value.trim();
        const searchHint = document.getElementById('searchHint');
        const searchLoading = document.getElementById('searchLoading');
        const searchIcon = document.querySelector('.search-icon');
        
        if (query.length < 2) {
            usersList.innerHTML = '';
            searchHint.style.display = 'flex';
            searchLoading.style.display = 'none';
            searchIcon.style.display = 'block';
            return;
        }

        searchHint.style.display = 'none';
        searchLoading.style.display = 'block';
        searchIcon.style.display = 'none';

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchUsersAPI(query, usersList, startPrivateChat, searchLoading, searchIcon);
        }, 300);
    });

    // Search users for group
    searchGroupUsers.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            groupUsersList.innerHTML = '';
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchUsersAPI(query, groupUsersList, addToGroup);
        }, 300);
    });

    // Add Selected Members button
    const addSelectedMembersBtn = document.getElementById('addSelectedMembersBtn');
    if (addSelectedMembersBtn) {
        addSelectedMembersBtn.addEventListener('click', function() {
            console.log('Add Selected Members button clicked'); // Debug log
            
            const checkboxes = document.querySelectorAll('#groupUsersList input[type="checkbox"]:checked');
            console.log('Found checkboxes:', checkboxes.length); // Debug log
            
            if (checkboxes.length === 0) {
                alert('Please select at least one member to add.');
                return;
            }
            
            checkboxes.forEach(checkbox => {
                const userData = JSON.parse(checkbox.dataset.user);
                console.log('Adding user:', userData.name); // Debug log
                
                if (!selectedUsers.find(u => u.id === userData.id)) {
                    selectedUsers.push(userData);
                }
            });
            
            console.log('Total selected users after adding:', selectedUsers.length); // Debug log
            updateSelectedMembers();
            
            // Clear search and hide button
            document.getElementById('searchGroupUsers').value = '';
            document.getElementById('groupUsersList').innerHTML = '';
            this.style.display = 'none';
        });
    }

    // Create group form
    createGroupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Selected users:', selectedUsers); // Debug log
        
        if (selectedUsers.length === 0) {
            alert('Please select at least one member for the group.');
            return;
        }

        const formData = new FormData(this);
        const data = {
            title: formData.get('title'),
            description: formData.get('description'),
            participants: selectedUsers.map(user => user.id) // Send only user IDs
        };

        console.log('Sending data to server:', data); // Debug log

        fetch('/student/chat/group', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug log
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('Error creating group: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating group. Please try again.');
        });
    });

    // Conversation item clicks
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const conversationId = this.dataset.conversationId;
            window.location.href = `/student/chat/${conversationId}`;
        });
    });
});

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    
    // Initialize selected members display for group modal
    if (modalId === 'newGroupModal') {
        updateSelectedMembers();
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    
    // Reset forms
    if (modalId === 'newChatModal') {
        document.getElementById('searchUsers').value = '';
        document.getElementById('usersList').innerHTML = '';
    } else if (modalId === 'newGroupModal') {
        document.getElementById('createGroupForm').reset();
        document.getElementById('searchGroupUsers').value = '';
        document.getElementById('groupUsersList').innerHTML = '';
        selectedUsers = [];
        document.getElementById('selectedMembers').innerHTML = '';
        const addBtn = document.getElementById('addSelectedMembersBtn');
        if (addBtn) {
            addBtn.style.display = 'none';
        }
    }
}

function searchUsersAPI(query, container, callback, searchLoading, searchIcon) {
    fetch(`/student/chat/search/users?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = '';
            
            if (data.users.length === 0) {
                container.innerHTML = `
                    <div class="no-users">
                        <i class="fas fa-user-slash"></i>
                        <p>No students found matching "${query}"</p>
                    </div>
                `;
            } else {
                // For group creation, add checkboxes for multiple selection
                if (container.id === 'groupUsersList') {
                    data.users.forEach(user => {
                        const userItem = document.createElement('div');
                        userItem.className = 'user-item group-user-item';
                        userItem.innerHTML = `
                            <div class="user-checkbox">
                                <input type="checkbox" id="user-${user.id}" value="${user.id}" data-user='${JSON.stringify(user)}'>
                            </div>
                            <div class="user-avatar">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="user-info">
                                <div class="user-name">${user.name}</div>
                                <div class="user-email">${user.email || 'No email'}</div>
                            </div>
                        `;
                        container.appendChild(userItem);
                    });
                    
                    // Show the "Add Selected Members" button
                    const addBtn = document.getElementById('addSelectedMembersBtn');
                    if (addBtn) {
                        addBtn.style.display = 'block';
                    }
                } else {
                    // For new chat modal, use single selection
                    data.users.forEach(user => {
                        const userItem = document.createElement('div');
                        userItem.className = 'user-item';
                        
                        // Show if user already has a conversation (for new chat modal)
                        const conversationStatus = user.has_existing_conversation ? 
                            '<div class="conversation-status">Already chatting</div>' : '';
                        
                        userItem.innerHTML = `
                            <div class="user-avatar">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="user-info">
                                <div class="user-name">${user.name}</div>
                                <div class="user-email">${user.email || 'No email'}</div>
                                ${conversationStatus}
                            </div>
                        `;
                        userItem.addEventListener('click', () => callback(user));
                        container.appendChild(userItem);
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error searching users:', error);
            container.innerHTML = `
                <div class="no-users">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error searching users. Please try again.</p>
                </div>
            `;
        })
        .finally(() => {
            if (searchLoading) searchLoading.style.display = 'none';
            if (searchIcon) searchIcon.style.display = 'block';
        });
}

function startPrivateChat(user) {
    fetch('/student/chat/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ user_id: user.id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Error starting chat: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error starting chat. Please try again.');
    });
}

function addToGroup(user) {
    if (selectedUsers.find(u => u.id === user.id)) {
        return; // User already selected
    }
    
    selectedUsers.push(user);
    updateSelectedMembers();
    
    // Clear the search input and results
    document.getElementById('searchGroupUsers').value = '';
    document.getElementById('groupUsersList').innerHTML = '';
}

function removeFromGroup(userId) {
    selectedUsers = selectedUsers.filter(u => u.id !== userId);
    updateSelectedMembers();
}

function updateSelectedMembers() {
    selectedMembers.innerHTML = '';
    
    if (selectedUsers.length === 0) {
        selectedMembers.innerHTML = `
            <div class="no-members-selected">
                <i class="fas fa-users"></i>
                <span>No members selected yet. Search and select students to add to the group.</span>
            </div>
        `;
        return;
    }
    
    // Add a header showing count
    const headerDiv = document.createElement('div');
    headerDiv.className = 'selected-members-header';
    headerDiv.innerHTML = `<strong>Selected Members (${selectedUsers.length}):</strong>`;
    selectedMembers.appendChild(headerDiv);
    
    selectedUsers.forEach(user => {
        const memberDiv = document.createElement('div');
        memberDiv.className = 'selected-member';
        memberDiv.innerHTML = `
            <span>${user.name}</span>
            <span class="remove-member" onclick="removeFromGroup(${user.id})">
                <i class="fas fa-times"></i>
            </span>
        `;
        selectedMembers.appendChild(memberDiv);
    });
}

// Search conversations functionality
const searchConversations = document.getElementById('searchConversations');
const conversationsList = document.getElementById('conversationsList');

if (searchConversations && conversationsList) {
    searchConversations.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const conversationItems = conversationsList.querySelectorAll('.conversation-item');
        
        conversationItems.forEach(item => {
            const conversationName = item.querySelector('.conversation-name').textContent.toLowerCase();
            const messagePreview = item.querySelector('.message-preview');
            const messageText = messagePreview ? messagePreview.textContent.toLowerCase() : '';
            
            if (conversationName.includes(query) || messageText.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show "No conversations found" message if no results
        const visibleItems = conversationsList.querySelectorAll('.conversation-item[style*="flex"], .conversation-item:not([style*="none"])');
        const noResultsMsg = document.getElementById('noSearchResults');
        
        if (query && visibleItems.length === 0) {
            if (!noResultsMsg) {
                const noResults = document.createElement('div');
                noResults.id = 'noSearchResults';
                noResults.className = 'no-results';
                noResults.innerHTML = '<p>No conversations found matching "' + query + '"</p>';
                conversationsList.appendChild(noResults);
            }
        } else if (noResultsMsg) {
            noResultsMsg.remove();
        }
    });
}
</script>
@endpush

@endsection
