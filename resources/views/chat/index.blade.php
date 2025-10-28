<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('AI Chatbot') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Chat Container -->
                    <div id="chat-container" class="h-96 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-4 bg-gray-50 dark:bg-gray-700">
                        <div id="chat-messages" class="space-y-4">
                            <!-- Messages will be loaded here -->
                        </div>
                    </div>

                    <!-- Message Input -->
                    <form id="chat-form" class="flex space-x-2">
                        <input type="text" id="message-input"
                               class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               placeholder="Type your message here..."
                               required>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Send
                        </button>
                    </form>

                    <!-- Loading Indicator -->
                    <div id="loading-indicator" class="hidden mt-2 text-sm text-gray-500 dark:text-gray-400">
                        AI is thinking...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const chatMessages = document.getElementById('chat-messages');
            const loadingIndicator = document.getElementById('loading-indicator');

            // Load chat history
            loadChatHistory();

            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message) return;

                // Add user message
                addMessage('user', message);
                messageInput.value = '';

                // Show loading
                loadingIndicator.classList.remove('hidden');

                // Send message to server
                fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.classList.add('hidden');
                    if (data.success) {
                        addMessage('ai', data.response);
                    } else {
                        addMessage('ai', 'Sorry, I encountered an error. Please try again.');
                    }
                })
                .catch(error => {
                    loadingIndicator.classList.add('hidden');
                    addMessage('ai', 'Sorry, I encountered an error. Please try again.');
                    console.error('Error:', error);
                });
            });

            function addMessage(sender, message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

                const messageContent = document.createElement('div');
                messageContent.className = `max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                    sender === 'user'
                        ? 'bg-indigo-600 text-white'
                        : 'bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-gray-100'
                }`;
                messageContent.textContent = message;

                messageDiv.appendChild(messageContent);
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function loadChatHistory() {
                fetch('/chat/history')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.messages) {
                        data.messages.forEach(msg => {
                            addMessage(msg.sender, msg.message);
                        });
                    }
                })
                .catch(error => console.error('Error loading chat history:', error));
            }
        });
    </script>
</x-app-layout>
