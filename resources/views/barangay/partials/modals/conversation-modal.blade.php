<!-- MODAL: Conversation Modal -->
<div id="conversationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-4xl h-[90vh] flex flex-col mx-4">

        <!-- Header -->
        <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="closeConversation()" class="text-white hover:bg-indigo-700 rounded-lg p-2">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
                <div>
                    <h3 class="text-lg font-bold" id="conversationTitle">Conversation</h3>
                    <p class="text-indigo-200 text-sm" id="conversationSubtitle"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="conversationStatus" class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold">
                    <i class="fas fa-circle text-xs mr-1"></i>Active
                </span>
                <button onclick="openCompleteMatchModal()"
                        id="completeMatchBtn"
                        class="px-4 py-2 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 transition text-sm font-semibold">
                    <i class="fas fa-flag-checkered mr-1"></i>Mark Complete
                </button>
            </div>
        </div>

        <!-- Match Info Banner -->
        <div id="matchInfoBanner" class="bg-blue-50 border-b border-blue-200 px-6 py-3">
            <!-- Will be populated with match details -->
        </div>

        <!-- Messages Container -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto px-6 py-4 bg-gray-50">
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                <p>Loading conversation...</p>
            </div>
        </div>

        <!-- Message Input -->
        <div class="border-t bg-white px-6 py-4">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <textarea id="messageInput"
                              rows="3"
                              placeholder="Type your message..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                              onkeydown="handleMessageKeydown(event)"></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>Press Enter to send, Shift+Enter for new line
                    </p>
                </div>
                <button onclick="sendMessage()"
                        id="sendMessageBtn"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold h-fit">
                    <i class="fas fa-paper-plane mr-2"></i>Send
                </button>
            </div>
        </div>
    </div>
</div>
